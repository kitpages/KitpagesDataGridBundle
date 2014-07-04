<?php
namespace Kitpages\DataGridBundle\Paginator;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

use Doctrine\ORM\QueryBuilder;

use Kitpages\DataGridBundle\Paginator\PaginatorConfig;
use Kitpages\DataGridBundle\Paginator\Paginator;
use Kitpages\DataGridBundle\Tool\UrlTool;
use Kitpages\DataGridBundle\KitpagesDataGridEvents;
use Kitpages\DataGridBundle\Event\DataGridEvent;

class PaginatorManager
{
    /** @var EventDispatcherInterface */
    protected $dispatcher;

    /** @var  array */
    protected $paginatorParameterList;

    /**
     * @param EventDispatcherInterface                $dispatcher
     */
    public function __construct(
        EventDispatcherInterface $dispatcher,
        $paginatorParameterList
    ) {
        $this->dispatcher = $dispatcher;
        $this->paginatorParameterList = $paginatorParameterList;
    }

    ////
    // paginator
    ////
    /**
     * get Paginator object
     *
     * @param  \Kitpages\DataGridBundle\Paginator\PaginatorConfig $paginatorConfig
     * @param  \Symfony\Component\HttpFoundation\Request      $request
     * @return \Kitpages\DataGridBundle\Paginator\Paginator
     */
    public function getPaginator(PaginatorConfig $paginatorConfig, Request $request)
    {
        $queryBuilder = $paginatorConfig->getQueryBuilder();
        // insert default values in paginator config
        $paginatorConfig = clone($paginatorConfig);
        if (is_null($paginatorConfig->getItemCountInPage())) {
            $paginatorConfig->setItemCountInPage($this->paginatorParameterList["item_count_in_page"]);
        }
        if (is_null($paginatorConfig->getVisiblePageCountInPaginator())) {
            $paginatorConfig->setVisiblePageCountInPaginator($this->paginatorParameterList["visible_page_count_in_paginator"]);
        }

        // create paginator object
        $paginator = new Paginator();
        $paginator->setPaginatorConfig($paginatorConfig);
        $paginator->setUrlTool(new UrlTool());
        $paginator->setRequestUri($request->getRequestUri());

        // get currentPage
        $paginator->setCurrentPage($request->query->get($paginatorConfig->getRequestQueryName("currentPage"), 1));

        // calculate total object count
        $countQueryBuilder = clone($queryBuilder);
        $countQueryBuilder->select("count(DISTINCT ".$paginatorConfig->getCountFieldName().")");
        $countQueryBuilder->setMaxResults(null);
        $countQueryBuilder->setFirstResult(null);
        $countQueryBuilder->resetDQLPart('groupBy');
        $countQueryBuilder->resetDQLPart('orderBy');

        // event to change paginator query builder
        $event = new DataGridEvent();
        $event->set("paginator", $paginator);
        $event->set("paginatorQueryBuilder", $countQueryBuilder);
        $event->set("request", $request);
        $this->dispatcher->dispatch(KitpagesDataGridEvents::ON_GET_PAGINATOR_QUERY, $event);

        if (!$event->isDefaultPrevented()) {
            $query = $countQueryBuilder->getQuery();
            $event->set("query", $query);
        }
        $this->dispatcher->dispatch(KitpagesDataGridEvents::AFTER_GET_PAGINATOR_QUERY, $event);

        // hack : recover query from the event so the developper can build a new query
        // from the paginatorQueryBuilder in the listener and reinject it in the event.
        $query = $event->get("query");

        try {
            $totalCount = $query->getSingleScalarResult();
            $paginator->setTotalItemCount($totalCount);
        } catch (\Doctrine\ORM\NoResultException $e) {
            $paginator->setTotalItemCount(0);
        }

        // calculate total page count
        if ($paginator->getTotalItemCount() == 0) {
            $paginator->setTotalPageCount(0);
        } else {
            $paginator->setTotalPageCount(
                (int) ((($paginator->getTotalItemCount() - 1) / $paginatorConfig->getItemCountInPage()) + 1)
            );
        }

        // change current page if needed
        if ($paginator->getCurrentPage() > $paginator->getTotalPageCount()) {
            $paginator->setCurrentPage(1);
        }

        // calculate nbPageLeft and nbPageRight
        $nbPageLeft = (int) ($paginatorConfig->getVisiblePageCountInPaginator() / 2);
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
        } else {
            $paginator->setPreviousButtonPage( $paginator->getCurrentPage() - 1 );
        }
        // calculate nextButton
        if ($paginator->getCurrentPage() == $paginator->getTotalPageCount()) {
            $paginator->setNextButtonPage(null);
        } else {
            $paginator->setNextButtonPage( $paginator->getCurrentPage() + 1);
        }

        return $paginator;
    }
}
