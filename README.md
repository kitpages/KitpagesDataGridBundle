KitpagesDataGridBundle
========================

This is a bundle is a simple datagrid bundle. It aims to be simple to use and extensible.

WARNING : works only with twig 1.8 +

Actual state
============
beta state

Author
======
Philippe Le Van (twitter : @plv)

Installation
============
You need to add the following lines in your deps :

    [DataGridBundle]
        git=git://github.com/kitpages/KitpagesDataGridBundle.git
        target=Kitpages/DataGridBundle

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

    class ContactController
    {
        public function productListAction()
        {
            // create query builder
            $repository = $this->getDoctrine()->getRepository('AcmeStoreBundle:Product');
            $queryBuilder = $repository->createQueryBuilder("item") // item is a mandatory name !!
                ->where("item.price > :price")
                ->setParameter('price', '19.90');

            $gridConfig = new GridConfig();
            $gridConfig->addField(new Field("id"));
            $gridConfig->addField(new Field("slug", array("filterable"=>true)));
            $gridConfig->addField(new Field("updatedAt"));

            $gridManager = $this->get("kitpages_data_grid.manager");
            $grid = $gridManager->getGrid($queryBuilder, $gridConfig, $this->getRequest());

            return $this->render('AppSiteBundle:Default:grid.html.twig', array(
                "grid" => $grid
            ));
        }
    }
    ?>

Twig associated
---------------

    {% embed 'KitpagesDataGridBundle:Grid:grid.html.twig' with {'grid': grid} %}
    {% endembed %}

More advanced usage
===================
In the controller
-----------------

same controller than before

Twig associated
---------------

    {% embed 'KitpagesDataGridBundle:Grid:grid.html.twig' with {'grid': grid} %}

        {% block kit_grid_thead_column %}
            <th>Action</th>
        {% endblock %}

        {% block kit_grid_tbody_column %}
            <td><a href="{{ path ("my_route", {"id": item.id}) }}">Edit</a></td>
        {% endblock %}

    {% endembed %}

