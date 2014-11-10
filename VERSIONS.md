Versions :
==========

2014-11-10 : v2.3.1

* no BC Break
* fix: fix for cases of multiple join levels. recursion of normalization where wrong.

2014-11-07 : v2.3.0

* no BC Break
* fix: important fix when using datagrid inside a subrequest

2014-09-10 : v2.2.0

* no BC Break
* refactor: JS refactoring thanks to snovichkov
* doc: fixes in doc

2014-07-28 : v2.1.1

* no BC Break
* fix: minor syntax fix in translations

2014-07-21 : v2.1.0

* no BC Break
* new: you can now chain a formatValueCallback and the event system

2014-07-08 : v2.0.0

* WARINIG : BC Break, you can switch to branch 1.x
* new : major refactoring: separation of paginator and grid
* new : much easier access to fields
* new : refactoring : normalizer is a new service
* new : api changes: the queryBuilder is now in the GridConfiguration object
* new : catagory and dataList : you can transfert data to a field to use these custom data in events
* new : configuration for default twigs in config.yml

2013-06-12 : tag v1.10.0

* fix : change url encoding in urlTools (+ phpunit)
* fix : change url encoding in javascript
* refactor : common javascript for every themes

2013-06-04 : tag v1.9.0

* add selectors for predefined filters : (see [doc](https://github.com/kitpages/KitpagesDataGridBundle/blob/master/Resources/doc/10-GridExtendedUse.md#add-selector-action-to-filter-on-a-field-with-a-value) )

2013-05-23 : tag v1.8.0

* add a twitter bootstrap layout for the datagrid

2013-05-22 : tag v1.7.0

* add category parameter in fields. This value is not used internally. You can use it for whatever. It can be useful
for a global formatting of fields with convertion events (seen kitpages_data_grid.on_display_grid_value_conversion)
* add a nullIfNotExists in fields. If you want to display values of a leftJoin query, if there is no value, you can
get an exception. With this value set to true, null is returned without any exception.

2013-05-02 : tag v1.6.1

* fix following issue #18 : https://github.com/kitpages/KitpagesDataGridBundle/issues/18

2013-04-10 : tag v1.6.0

* new : global formatting system with events (see [doc](https://github.com/kitpages/KitpagesDataGridBundle/blob/master/Resources/doc/10-GridExtendedUse.md#format-some-fields-system-wide) )

2013-04-09 : tag v1.5.0

* fix: for accessing data with join relations
* new: error messages more readable during twig displaying
* test: much more unit tests on the Grid::displayGridValue(). Very sensitive method...
* doc: more documentation (thanks to @choomz)
* new : a (ridiculously simple) debug system

2013-03-12 : tag v1.4.0

* unit test updated for composer and sf2.1
* readme updated for sf2.1

2012-08-28 : tag v1.3.0

* manage "group by" requests (thanks to tyx)
* docs : render a cell with a twig file
* docs : group by queries
* fix : travis configuration
* fix : composer dependency

2012-07-09 : tag v1.2.0

* More documentation
* a fluent interface
* small bug fixes (line count for group by queries)
* unit testing
* code cleaning
* twig templates more flexible
* travis-ci integration
* an more advanced format callback system. See [Resources/doc/10-GridExtendedUse.md](https://github.com/kitpages/KitpagesDataGridBundle/blob/master/Resources/doc/10-GridExtendedUse.md)
* 2 new contributors

2012-05-23 : tag v1.1.1

* doc refactoring

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
