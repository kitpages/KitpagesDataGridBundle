<?php

namespace Kitpages\DataGridBundle\Twig;

use Twig\Extension\AbstractExtension;
use Twig\Extension\GlobalsInterface;

class GlobalsTwigExtension extends AbstractExtension implements GlobalsInterface
{
    protected $gridParameterList;
    protected $paginatorParameterList;

    public function __construct(
        $gridParameterList,
        $paginatorParameterList
    ) {
        $this->gridParameterList = $gridParameterList;
        $this->paginatorParameterList = $paginatorParameterList;
    }

    public function getGlobals(): array
    {
        return [
            "kitpages_data_grid" => [
                'grid' => $this->gridParameterList,
                'paginator' => $this->paginatorParameterList,
            ],
        ];
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
