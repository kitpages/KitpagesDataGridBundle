<?php
/**
 * Created by levan on 03/07/14.
 */

namespace Kitpages\DataGridBundle\Grid\ItemListNormalizer;


use Doctrine\ORM\Query;
use Doctrine\ORM\QueryBuilder;

class StandardNormalizer
    implements NormalizerInterface
{
    /**
     * @inheritdoc
     */
    public function normalize(Query $query, QueryBuilder $queryBuilder)
    {
        return $query->getResult('KitpagesDataGridHydrator');
    }
}
