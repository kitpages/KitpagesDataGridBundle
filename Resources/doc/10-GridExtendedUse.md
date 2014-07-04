Grid Extended Use
=================

More than one Grids in one page
-------------------------------
If you have more than one grid on a page, you have to name each grid with a different name. In your controller

    $gridConfig->setName("first-grid");

Change Paginator parameters of your grid
----------------------------------------
If you want to change the result number on each page for example.

    public function gridAction()
    {
        $gridManater = $this->get("kitpages_data_grid.manager");

        $repository = $this->getDoctrine()->getRepository('KitpagesShopBundle:OrderLine');
        $queryBuilder = $repository->createQueryBuilder('ol');
        $queryBuilder->select('ol');

        $gridConfig = new GridConfig();
        $gridConfig
            ->setQueryBuilder($queryBuilder)
            ->setCountFieldName('ol.id')
            ->addField(new Field('ol.id', array('sortable' => true)))
            ->addField(new Field('ol.shopReference', array(
                'label' => 'Ref',
                'filterable' => true,
                'sortable' => true
            )))
            ->addField(new Field('ol.updatedAt', array(
                'sortable' => true,
                'formatValueCallback' => function ($value) { return $value->format('Y/m/d'); }
            )))
        ;

        // paginator configuration
        $gridPaginatorConfig = new PaginatorConfig();
        $gridPaginatorConfig
            ->setName($gridConfig->getName())
            ->setCountFieldName($gridConfig->getCountFieldName())
            ->setItemCountInPage(10)
        ;
        $gridConfig->setPaginatorConfig($gridPaginatorConfig);

        $grid = $gridManager->getGrid($gridConfig, $this->getRequest());

        return $this->render('AppSiteBundle:Default:grid.html.twig', array(
            'grid' => $grid
        ));
    }

Format some fields system wide
------------------------------
You can change the way datagrid displays a date for your entire website. Or you want add a link around every email.

You can use events to do that :

Before rendering a field, the datagrid sends an event KitpagesDataGridEvents::ON_DISPLAY_GRID_VALUE_CONVERSION that can
replace the standard rendering way to display a field.

After the default formatting, another event KitpagesDataGridEvents::AFTER_DISPLAY_GRID_VALUE_CONVERSION is sent.
You can still here make some small modifications on the display result.

We will create a EventListener to change the way to display these fields:

```php
<?php
namespace App\SiteBundle\EventListener;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Kitpages\DataGridBundle\KitpagesDataGridEvents;
use Kitpages\DataGridBundle\Event\DataGridEvent;

class DataGridConversionSubscriber
    implements EventSubscriberInterface
{
    public static function getSubscribedEvents()
    {
        return array(
            KitpagesDataGridEvents::ON_DISPLAY_GRID_VALUE_CONVERSION => 'onConversion',
            KitpagesDataGridEvents::AFTER_DISPLAY_GRID_VALUE_CONVERSION => 'afterConversion'
        );
    }

    public function onConversion(DataGridEvent $event)
    {
        // prevent default formatting
        $event->preventDefault();

        // get value to display
        $value = $event->get("value");

        // datetime formatting
        if ($value instanceof \DateTime) {
            $formatter = new \IntlDateFormatter(
                'fr',
                \IntlDateFormatter::LONG,
                \IntlDateFormatter::SHORT,
                $value->getTimezone()->getName(),
                \IntlDateFormatter::GREGORIAN
            );
            $event->set("returnValue", $formatter->format($value->getTimestamp()));
            return;
        }

        // email formating (note the way the autoEscape is modified)
        $field = $event->get("field");
        if (strpos($field->getFieldName(), "email") !== false) {
            $field->setAutoEscape(false);
            $ret = '<a href="mailto:'.$value.'">'.$value.'</a>';
            $event->set("returnValue", $ret);
            return;
        }

        // for the other cases, copy the value to the return value
        $event->set("returnValue", $value);
    }

    public function afterConversion(DataGridEvent $event)
    {
        // set to uppercase all lastnames
        $field = $event->get("field");
        if (strpos(strtolower($field->getFieldName()), "lastname") !== false) {
            $event->set("returnValue", strtoupper($event->get("returnValue"));
            return;
        }
    }
}

```

And add the subscriber to the event listener. This is an example in a service.xml.
```xml
<service id="app_site.data_grid_conversion" class="App\SiteBundle\EventListener\DataGridConversionSubscriber">
    <tag name="kernel.event_subscriber" />
</service>
```

(Note that these events are not sent if a formatValueCallback is given for a field.)


Select recursive field
----------------------
If you have for exemple a categorie table which as a recursion with parent_id, you must use 
the leftJoin() condition in your queryBuilder.

    public function gridAction()
    {
        $em = $this->getDoctrine()->getEntityManager();
        $queryBuilder = $em->createQueryBuilder();
        $queryBuilder
            ->select(array('c', 'cp'))
            ->from('My\SiteBundle\Entity\Categorie', 'c')
            ->leftJoin('c.idParent', 'p')
        ;

        $gridConfig = new GridConfig();
        $gridConfig
            ->setQueryBuilder($queryBuilder)
            ->setCountFieldName('c.id')
            ->addField(new Field('c.foo')
            ->addField(new Field('c.idParent', array(
                'label' => 'Parent',
                'formatValueCallback' => function($value) { return (!empty($value)) ? $value['foo'] : 'None'; }
            )))
        ;

        $gridManager = $this->get('kitpages_data_grid.manager');
        $grid = $gridManager->getGrid($gridConfig, $this->getRequest());

        return $this->render('MySiteBundle:Default:grid.html.twig', array(
            'grid' => $grid
        ));
    }

Select collection from a 1-n relationship
-----------------------------------------
If you have for exemple an article with a comments property wich is a 1-n relationship. You have the article id and you want to create a grid for the article's comments :

    public function gridAction()
    {
        $manager = $this->getDoctrine()->getManager();
        
        /* @var $qb \Doctrine\ORM\QueryBuilder */
        $qb = $manager->createQueryBuilder('comments-list');
        $qb->select('c1')
            ->from('Acme\MyBundle\Entity\Comment', 'c1')
            ->where(
                $qb->expr()->in(
                    'c1.id', 
                    $manager->createQueryBuilder('articles')
                        ->select('c2.id')
                        ->from('Acme\MyBundle\Entity\Article', 'a')
                        ->join('a.comments', 'c2')
                        ->where('a.id = :id_article')
                        ->getDQL()
                )
            )
            ->orderBy('c1.created', 'DESC')
            ->setParameter('id_article', $articleId)
        ;

        $gridConfig = new GridConfig();
        $gridConfig->setQueryBuilder($qb)
        $gridConfig->setCountFieldName("c1.id");
        $gridConfig->addField(new Field("c1.comment", array("label" => "Comment")));
        $gridConfig->addField(new Field("c1.author", array("label" => "Author", "filterable" => true, "sortable" => true)));

        $gridManager = $this->get("kitpages_data_grid.manager");
        $grid = $gridManager->getGrid($gridConfig, $this->getRequest());

        return $this->render('MySiteBundle:Default:grid.html.twig', array(
            'grid' => $grid
        ));
    }

formatValueCallback
-------------------
If you want to format a data, you can use a simple callback. For example, if a data is a dateTime, you can format
use that code :

    $gridConfig->addField(new Field(
        'ol.updatedAt',
        array(
            'label' => 'Updated at',
            'sortable'=>true,
            'formatValueCallback' => function($value) { return $value->format('Y/m/d'); }
        )
    ));

You can also have a second argument in your callback that will receive the entire row received from the query.

    $gridConfig->addField(new Field(
        'ol.updatedAt',
        array(
            'label' => 'Date and Id',
            'sortable' => true,
            'formatValueCallback' => function($value, $row) { return $value->format('Y/m/d') . '--' . $row['id']; }
        )
    ));


Add selector action (to filter on a field with a value)
-------------------------------------------------------
You can add button action filters with

    $gridConfig->addSelector(array('label' => 'button label', 'field' => 'c1.author', 'value' => 'Arthur Rimbaud'));
    $gridConfig->addSelector(array('label' => 'button label2', 'field' => 'c1.author', 'value' => 'Paul Verlaine'));
    $gridConfig->addSelector(array('label' => 'button label3', 'field' => 'c1.comment', 'value' => 'No comment'));



Grid with a "GROUP BY" querybuilder
-----------------------------------
For group by queries, watch out for the count field name you define. In the count query
for the paginator, the groupBy part is removed form the queryBuilder and a distinct is
added before the count field.

    // create query builder
    $repository = $this->getDoctrine()->getRepository('AcmeStoreBundle:Product');
    $queryBuilder = $repository->createQueryBuilder("item")
        ->select('item.type, count(item.id) as cnt')
        ->groupBy('item.type')
        ->where('item.price > :price')
        ->setParameter('price', '19.90')
    ;

    $gridConfig = new GridConfig();
    $gridConfig
        ->setQueryBuilder($queryBuilder)
        ->setCountFieldName('item.type')
        ->addField(new Field('item.type'))
        ->addField(new Field('cnt'))
    ;



Render the item value with a twig template
------------------------------------------

You can format a data with a twig template by using the callback system :

    // get templating service
    $twig = $this->get('templating');
    // add the field
    $gridConfig->addField(new Field(
        'ol.updatedAt',
        array(
            "formatValueCallback"=>function($value, $row) use ($twig) {
                return $twig->render("AppSiteBundle:Default:grid-element.html.twig", $row);
            },
            "autoEscape" => false
        )
    ));

And the twig file could be grid-element.html.twig in the right bundle :

    <strong>
      id={{id}}<br/>
      date={{updatedAt | date ("Y/m/d") }}
    </strong>


Add multiple action with checkboxes on the left
-----------------------------------------------

You can add a form around the datagrid and add any input elements in the datagrid just by extending
the embeded twig.

Let's see in this example how to add checkboxes on the left of the grid for multiple actions.

    {% embed kitpages_data_grid.grid.default_twig with {'grid': grid} %}

        {% block kit_grid_before_table %}
            <form action="{{ path('my_route') }}" method="POST">
        {% endblock %}

        {% block kit_grid_thead_before_column %}
            <th>select</th>
        {% endblock %}

        {% block kit_grid_tbody_before_column %}
            <td><input type="checkbox" name="check_{{ item.id }}" /></td>
        {% endblock %}

        {% block kit_grid_after_table %}
            <input type="submit" />
            </form>
        {% endblock %}

    {% endembed %}

You can see all the extension points of the data grid template in the
file [views/Grid/grid.html.twig](https://github.com/kitpages/KitpagesDataGridBundle/blob/master/Resources/views/Grid/grid.html.twig)
