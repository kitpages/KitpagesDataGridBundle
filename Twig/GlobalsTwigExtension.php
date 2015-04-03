<?php
namespace Kitpages\DataGridBundle\Twig;


class GlobalsTwigExtension
    extends \Twig_Extension
{
    protected $gridParameterList;
    protected $paginatorParameterList;

    public function __construct(
        $gridParameterList,
        $paginatorParameterList
    )
    {
        $this->gridParameterList = $gridParameterList;
        $this->paginatorParameterList = $paginatorParameterList;
    }

    public function getGlobals()
    {
        return array(
            "kitpages_data_grid" => array(
                'grid' => $this->gridParameterList,
                'paginator' => $this->paginatorParameterList
            )
        );
    }
    /**
     * Returns the name of the extension.
     *
     * @return string The extension name
     */
    public function getName()
    {
        return "kitpages_data_grid_globals_extension";
    }

} 
