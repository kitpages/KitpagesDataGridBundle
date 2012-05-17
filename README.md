KitpagesDataGridBundle
========================

This Symfony2 Bundle is a simple datagrid bundle. It aims to be easy to use and extensible.

Actual state
============

stable, production ready

System Requirement
==================
* jQuery has to be present on your pages
* version 1.8+ of twig is mandatory (use of twig embeds)

Versions :
==========
2012-05-17 : tag v1.0.0

* sorting added
* template twig more extendable
* small fix
* refactor in Grid Manager

2012-05-02 :

* add possibility to have a join in jour queryBuilder
* remove mandatory name for your entity

migrations

* you have to add the field name used for counting
** Ex : $gridConfig->setCountFieldName("item.id"); // for count(item.id)
* you have to set complete field name instead of short field name
** Ex : $gridConfig->addField(new Field("item.id"));
** instead of just : $gridConfig->addField(new Field("id"));

2012-04-xx

* creation

Author
======
Philippe Le Van (twitter : @plv)

Installation
============
You need to add the following lines in your deps :

    [DataGridBundle]
        git=git://github.com/kitpages/KitpagesDataGridBundle.git
        target=Kitpages/DataGridBundle

in autoload.php, you have to add :

    'Kitpages'         => __DIR__.'/../vendor',

AppKernel.php

    $bundles = array(
        ...
        new Kitpages\DataGridBundle\KitpagesDataGridBundle(),
    );

Configuration in config.yml
===========================

no configuration

Simple Usage example
====================
In the controller
-----------------

    use Kitpages\DataGridBundle\Model\GridConfig;
    use Kitpages\DataGridBundle\Model\Field;

    class ContactController
    {
        public function productListAction()
        {
            // create query builder
            $repository = $this->getDoctrine()->getRepository('AcmeStoreBundle:Product');
            $queryBuilder = $repository->createQueryBuilder("item")
                ->where("item.price > :price")
                ->setParameter('price', '19.90');

            $gridConfig = new GridConfig();
            $gridConfig->setCountFieldName("item.id");
            $gridConfig->addField(new Field("item.id"));
            $gridConfig->addField(new Field("item.slug", array("filterable"=>true)));
            $gridConfig->addField(new Field("item.updatedAt"));

            $gridManager = $this->get("kitpages_data_grid.manager");
            $grid = $gridManager->getGrid($queryBuilder, $gridConfig, $this->getRequest());

            return $this->render('AppSiteBundle:Default:productList.html.twig', array(
                "grid" => $grid
            ));
        }
    }
    ?>

Twig associated
---------------
In your twig you just have to put this code to display the grid you configured.

    {% embed 'KitpagesDataGridBundle:Grid:grid.html.twig' with {'grid': grid} %}
    {% endembed %}

More advanced usage
===================
In the controller
-----------------

same controller than before

Twig associated
---------------
If you want to add a column on the right of the table, you can put this code in your twig.

    {% embed 'KitpagesDataGridBundle:Grid:grid.html.twig' with {'grid': grid} %}

        {% block kit_grid_thead_column %}
            <th>Action</th>
        {% endblock %}

        {% block kit_grid_tbody_column %}
            <td><a href="{{ path ("my_route", {"id": item.id}) }}">Edit</a></td>
        {% endblock %}

    {% endembed %}

More advanced usage
===================
In the controller
-----------------

    use Kitpages\DataGridBundle\Model\GridConfig;
    use Kitpages\DataGridBundle\Model\Field;

    class AdminController extends Controller
    {

        public function listAction($state)
        {
            // create query builder
            $em = $this->get('doctrine')->getEntityManager();
            $queryBuilder = $em->createQueryBuilder()
                ->select("mission, employee, client")
                ->from('KitappMissionBundle:Mission', 'mission')
                ->leftJoin('mission.employee', 'employee')
                ->leftJoin('mission.client', 'client')
                ->where('mission.state = :state')
                ->add('orderBy', 'mission.updatedAt DESC')
                ->setParameter('state', $state);
            $gridConfig = new GridConfig();
            $gridConfig->setCountFieldName("mission.id");
            $gridConfig->addField(new Field("mission.title", array("label" => "title", "filterable"=>true)));
            $gridConfig->addField(new Field("mission.country", array("filterable"=>true)));
            $gridConfig->addField(new Field("client.corporation", array("filterable"=>true)));
            $gridConfig->addField(new Field("employee.lastname", array("filterable"=>true)));
            $gridConfig->addField(new Field("employee.email", array("filterable"=>true)));
            $gridManager = $this->get("kitpages_data_grid.manager");
            $grid = $gridManager->getGrid($queryBuilder, $gridConfig, $this->getRequest());

            return $this->render('KitappMissionBundle:Admin:list.html.twig', array(
                "grid" => $grid
            ));
        }
    }

Twig associated
---------------
same Twig than before

Reference guide
===============
Add a field in the gridConfig
-----------------------------
when you add a field, you can set these parameters :

    $gridConfig->addField(new Field("slug", array(
        "label" => "Mon slug",
        "sortable" => false,
        "visible" => true,
        "filterable"=>true,
        "translatable"=>true,
        "formatValueCallback" => function($value) {return strtoupper($value);},
        "autoEscape" => true
    )));

What can you personalize in your twig template
----------------------------------------------
With the embed system of twig 1.8 and more, you can override some parts of the default
rendering (see example in the "More advanced usage" paragraph).

You can consult the base twig template here to see what you can personalize.
https://github.com/kitpages/KitpagesDataGridBundle/blob/master/Resources/views/Grid/grid.html.twig
