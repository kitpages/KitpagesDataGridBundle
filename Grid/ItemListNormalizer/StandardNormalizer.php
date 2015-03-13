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

        $itemList = $query->getScalarResult();
        $normalizedList = $this->normalizeKeys($itemList);
        return $normalizedList;
    }

    protected function normalizeKeys(array $itemList)
    {
        $normalizedList = array();

        foreach($itemList as $item)
        {
            $normalizedItem = array();

            foreach($item as $key => $val)
            {
                $normalizedKey = str_replace('_', '.', $key);
                $normalizedItem[$normalizedKey] = $val;
            }

            $normalizedList[] = $normalizedItem;
        }
        return $normalizedList;
    }
}
