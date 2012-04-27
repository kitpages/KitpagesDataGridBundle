<?php
namespace Kitpages\DataGridBundle\Service;

use Symfony\Bundle\DoctrineBundle\Registry;
use Symfony\Component\HttpFoundation\Request;

use Doctrine\ORM\QueryBuilder;

use Kitpages\DataGridBundle\Model\GridConfig;
use Kitpages\DataGridBundle\Model\Grid;
use Kitpages\DataGridBundle\Model\PaginatorConfig;
use Kitpages\DataGridBundle\Model\Paginator;
use Kitpages\DataGridBundle\Tool\UrlTool;

class GridManager
{
    /** @var \Symfony\Bundle\DoctrineBundle\Registry */
    protected $doctrine;

    /**
     * @param \Symfony\Bundle\DoctrineBundle\Registry $doctrine
     */
    public function __construct(
        Registry $doctrine
    ) {
        $this->doctrine = $doctrine;
    }


    /**
     * @return \Symfony\Bundle\DoctrineBundle\Registry
     */
    public function getDoctrine()
    {
        return $this->doctrine;
    }

    public function getGrid(QueryBuilder $queryBuilder, GridConfig $gridConfig, Request $request)
    {
        // change filter

        // create grid objet
        $grid = new Grid();
        $grid->setGridConfig($gridConfig);
        $grid->setUrlTool(new UrlTool());
        $grid->setRequestUri($request->getRequestUri());

        // create base request
        $gridQueryBuilder = clone($queryBuilder);
        $gridQueryBuilder->select("item");
        // TODO : apply filters
        $filter = $request->query->get($grid->getFilterFormName(),"");
        if ($filter) {
            $fieldList = $gridConfig->getFieldList();
            $filterRequestList = array();
            foreach($fieldList as $field) {
                if ($field->getFilterable()) {
                    $filterRequestList[] = $gridQueryBuilder->expr()->like("item.".$field->getFieldName(), ":filter");
                }
            }
            if (count($filterRequestList) > 0) {
                $reflectionMethod = new \ReflectionMethod($gridQueryBuilder->expr(), "orx");
                $gridQueryBuilder->where($reflectionMethod->invokeArgs($gridQueryBuilder->expr(), $filterRequestList));
                $gridQueryBuilder->setParameter("filter", "%".$filter."%");
            }
            $grid->setFilterValue($filter);
        }


        // TODO : apply sorting

        // build paginator
        $paginatorConfig = $gridConfig->getPaginatorConfig();
        if ($paginatorConfig == null) {
            $paginatorConfig = new PaginatorConfig();
        }
        $paginator = $this->getPaginator($gridQueryBuilder, $paginatorConfig, $request);
        $grid->setPaginator($paginator);

        // calculate limits
        $gridQueryBuilder->setMaxResults($paginator->getPaginatorConfig()->getItemCountInPage());
        $gridQueryBuilder->setFirstResult(($paginator->getCurrentPage()-1) * $paginator->getPaginatorConfig()->getItemCountInPage());

        // execute request
        $query = $gridQueryBuilder->getQuery();
        $itemList = $query->getResult();
        $grid->setItemList($itemList);

        return $grid;
    }

    public function getPaginator(QueryBuilder $queryBuilder, PaginatorConfig $paginatorConfig, Request $request)
    {
        // create paginator object
        $paginator = new Paginator();
        $paginator->setPaginatorConfig($paginatorConfig);
        $paginator->setUrlTool(new UrlTool());
        $paginator->setRequestUri($request->getRequestUri());

        // get currentPage
        $paginator->setCurrentPage($request->query->get($paginatorConfig->getRequestQueryName("currentPage"), 1));

        // calculate total object count
        $countQueryBuilder = clone($queryBuilder);
        $countQueryBuilder->select("count(item)");
        $countQueryBuilder->setMaxResults(null);
        $countQueryBuilder->setFirstResult(null);
        $totalCount = $countQueryBuilder->getQuery()->getSingleScalarResult();
        $paginator->setTotalItemCount($totalCount);

        // calculate total page count
        if ($paginator->getTotalItemCount() == 0) {
            $paginator->setTotalPageCount(0);
        }
        else {
            $paginator->setTotalPageCount(
                (int)((($paginator->getTotalItemCount() - 1) / $paginatorConfig->getItemCountInPage()) + 1)
            );
        }

        // change current page if needed
        if ($paginator->getCurrentPage() > $paginator->getTotalPageCount()) {
            $paginator->setCurrentPage(1);
        }

        // calculate nbPageLeft and nbPageRight
        $nbPageLeft = (int)($paginatorConfig->getVisiblePageCountInPaginator() / 2);
        $nbPageRight = $paginatorConfig->getVisiblePageCountInPaginator() - 1 - $nbPageLeft ;

        // calculate lastPage to display
        $maxPage = min($paginator->getTotalPageCount(), $paginator->getCurrentPage() + $nbPageRight);
        // adapt minPage and maxPage
        $minPage = max(1, $maxPage-($paginatorConfig->getVisiblePageCountInPaginator() - 1));
        $maxPage = min($paginator->getTotalPageCount(), $minPage + ($paginatorConfig->getVisiblePageCountInPaginator() - 1));

        $paginator->setMinPage($minPage);
        $paginator->setMaxPage($maxPage);

        // calculate previousButton
        if ($paginator->getCurrentPage() == 1) {
            $paginator->setPreviousButtonPage(null);
        }
        else {
            $paginator->setPreviousButtonPage( $paginator->getCurrentPage() - 1 );
        }
        // calculate nextButton
        if ($paginator->getCurrentPage() == $paginator->getTotalPageCount()) {
            $paginator->setNextButtonPage(null);
        }
        else {
            $paginator->setNextButtonPage( $paginator->getCurrentPage() + 1);
        }

        return $paginator;
    }

}
