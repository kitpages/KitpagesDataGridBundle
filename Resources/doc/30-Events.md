Events
======

Example
-------
Let's imagine you want to translate your results with the gedmo translatable. You have to follow these steps :

* create a service that can alter the query
* register this service as a listener of the event "kitpages_data_grid.after_get_grid_query"

Service GridListener.php

    <?php
    namespace App\SiteBundle\EventListener;

    use Kitpages\DataGridBundle\Event\DataGridEvent;

    class GridListener
    {
        public function afterGetGridQuery(DataGridEvent $event)
        {
            $event->get("query")->setHint(
                \Doctrine\ORM\Query::HINT_CUSTOM_OUTPUT_WALKER,
                'Gedmo\\Translatable\\Query\\TreeWalker\\TranslationWalker'
            );
        }
    }

Configuration to add in config.yml

    services:
        datagrid_listener:
            class: 'App\SiteBundle\EventListener\GridListener'
            tags:
                - { name: "kernel.event_listener", event: "kitpages_data_grid.after_get_grid_query", method: "afterGetGridQuery" }

