<?php

namespace Kitpages\DataGridBundle\Grid\ItemListNormalizer;

use Doctrine\ORM\Query;
use Doctrine\ORM\QueryBuilder;

interface NormalizerInterface
{
    /**
     * @param Query $query
     * @param QueryBuilder $queryBuilder
     * @return array
     */
    public function normalize(Query $query, QueryBuilder $queryBuilder);
} 
