Versions :
==========
2012-05-23 : tag v1.1.0

* added events for modifying the way the grid or the paginator works (see Resources/doc/30-Events.md)
* modify the default twig in order to remove the filter form from the table. It is useful if you want add
a form around the grid (let's imagine you add checkboxes on the left of the grid)
* add documentation in Resources/doc/

2012-05-21 : tag v1.0.1

* composer.json added and link to packagist
* normalization of results for request like $queryBuilder->select("item, item.id * 3 as foo"); // warning : see
Limitations paragraph
* add {% block kit_grid_thead_before_column %}{%endblock%} and {% block kit_grid_tbody_before_column %}{%endblock%} for
adding columns before le natural column list

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
