<?php

namespace Kitpages\DataGridBundle\Grid\ItemListNormalizer;


use Doctrine\ORM\Query;

interface NormalizerInterface
{
    /**
     * @param Query $query
     * @return array
     */
    public function normalize(Query $query);
} 