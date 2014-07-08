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
        // execute the query
        $itemList = $query->getArrayResult();

        $dqlParts = $queryBuilder->getDQLParts();
        $rootAlias = $dqlParts["from"][0]->getAlias();

        // normalize result (for request of type $queryBuilder->select("item, bp, item.id * 3 as titi"); )
        $normalizedItemList = array();
        foreach ($itemList as $item) {
            $normalizedItem = $this->normalizeOneItem($dqlParts, $item, $rootAlias);
            $normalizedItemList[] = $normalizedItem;
        }
        return $normalizedItemList;
    }


    protected function normalizeOneItem($dqlParts, $item, $baseAlias)
    {
        // case of left join that returns null
        if (is_null($item)) {
            return array();
        }

        // get aliases to replace from $dqlParts
        $joinAliasList = array();
        if (array_key_exists('join', $dqlParts) && array_key_exists($baseAlias, $dqlParts['join']) ) {
            $joinList = $dqlParts["join"][$baseAlias];
            foreach($joinList as $join) {
                $attributeName = str_replace($baseAlias.'.', '', $join->getJoin());
                $joinAliasList[$attributeName] = $join->getAlias();
            }
        }
        // check if there is a "as xxx" in the dql
        // horrible hack but doctrine doesn't help so much here...
        // if there is numerical keys in the result, as xxx are alpha numeric keys and
        // the root alias is in the numerical "0" part... oh yeah...
        $containAsSection = false;
        foreach ($item as $key=>$val) {
            if (is_int($key)) {
                $containAsSection = true;
            }
        }

        $valueList = array();
        foreach($item as $key => $val) {
            if ($key === 0) {
                $valueListToMerge = $this->normalizeOneItem($dqlParts, $val, $baseAlias);
                $valueList = array_merge($valueList, $valueListToMerge);
            } elseif ( is_int($key) && ($key > 0)) {
                $valueList[$key-1] = $val;
            } elseif ( ! $containAsSection ) {
                if (array_key_exists($key, $joinAliasList)) {
                    $valueListToMerge = $this->normalizeOneItem($dqlParts, $val, $joinAliasList[$key]);
                    $valueList = array_merge($valueList, $valueListToMerge);
                } else {
                    $valueList[$baseAlias.'.'.$key] = $val;
                }
            } else {
                $valueList[$key] = $val;
            }
        }
        return $valueList;
    }
} 