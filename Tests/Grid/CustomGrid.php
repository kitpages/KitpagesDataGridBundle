<?php
namespace Kitpages\DataGridBundle\Tests\Grid;

use Kitpages\DataGridBundle\Grid\Grid;

/**
 * Just a subclass of Grid used to test the mecanism of giving a grid object
 * to getGrid in order to use its own subclass of Grid instead of the Grid class
 */
class CustomGrid extends Grid
{
    private $myCustomParamter;

    /**
     * @return mixed
     */
    public function getMyCustomParamter()
    {
        return $this->myCustomParamter;
    }

    /**
     * @param mixed $myCustomParamter
     */
    public function setMyCustomParamter($myCustomParamter)
    {
        $this->myCustomParamter = $myCustomParamter;
    }
}
