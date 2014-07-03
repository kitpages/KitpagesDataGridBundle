<?php

namespace Kitpages\DataGridBundle\Paginator;

use Kitpages\DataGridBundle\Tool\UrlTool;

class Paginator
{
    protected $totalPageCount = null;
    protected $minPage = null;
    protected $maxPage = null;
    protected $nextButtonPage = null;
    protected $previousButtonPage = null;
    protected $totalItemCount = 0;
    protected $currentPage = 1;
    /** @var UrlTool */
    protected $urlTool = null;
    /** @var PaginatorConfig */
    protected $paginatorConfig = null;
    /** @var string */
    protected $requestUri = null;

    public function getPageRange()
    {
        $tab = array();
        for ($i = $this->minPage ; $i <= $this->maxPage ; $i++) {
            $tab[] = $i;
        }

        return $tab;
    }

    public function getUrl($key, $val)
    {
        return $this->urlTool->changeRequestQueryString(
            $this->requestUri,
            $this->paginatorConfig->getRequestQueryName($key),
            $val
        );
    }

    public function setMaxPage($maxPage)
    {
        $this->maxPage = $maxPage;
    }

    public function getMaxPage()
    {
        return $this->maxPage;
    }

    public function setMinPage($minPage)
    {
        $this->minPage = $minPage;
    }

    public function getMinPage()
    {
        return $this->minPage;
    }

    public function setNextButtonPage($nextButtonPage)
    {
        $this->nextButtonPage = $nextButtonPage;
    }

    public function getNextButtonPage()
    {
        return $this->nextButtonPage;
    }

    public function setPreviousButtonPage($previousButtonPage)
    {
        $this->previousButtonPage = $previousButtonPage;
    }

    public function getPreviousButtonPage()
    {
        return $this->previousButtonPage;
    }

    public function setTotalItemCount($totalItemCount)
    {
        $this->totalItemCount = $totalItemCount;
    }

    public function getTotalItemCount()
    {
        return $this->totalItemCount;
    }

    public function setTotalPageCount($totalPageCount)
    {
        $this->totalPageCount = $totalPageCount;
    }

    public function getTotalPageCount()
    {
        return $this->totalPageCount;
    }

    public function setCurrentPage($currentPage)
    {
        $this->currentPage = $currentPage;
    }

    public function getCurrentPage()
    {
        return $this->currentPage;
    }

    /**
     * @param \Kitpages\DataGridBundle\Tool\UrlTool $urlTool
     */
    public function setUrlTool($urlTool)
    {
        $this->urlTool = $urlTool;
    }

    /**
     * @return \Kitpages\DataGridBundle\Tool\UrlTool
     */
    public function getUrlTool()
    {
        return $this->urlTool;
    }

    /**
     * @param \Kitpages\DataGridBundle\Paginator\PaginatorConfig $paginatorConfig
     */
    public function setPaginatorConfig($paginatorConfig)
    {
        $this->paginatorConfig = $paginatorConfig;
    }

    /**
     * @return \Kitpages\DataGridBundle\Paginator\PaginatorConfig
     */
    public function getPaginatorConfig()
    {
        return $this->paginatorConfig;
    }

    /**
     * @param string $requestUri
     */
    public function setRequestUri($requestUri)
    {
        $this->requestUri = $requestUri;
    }

    /**
     * @return string
     */
    public function getRequestUri()
    {
        return $this->requestUri;
    }

}
