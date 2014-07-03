<?php
/**
 * Created by levan on 03/07/14.
 */

namespace Kitpages\DataGridBundle\Grid\ItemListNormalizer;


use Doctrine\ORM\Query;

class LegacyNormalizer
    implements NormalizerInterface
{
    /**
     * @inheritdoc
     */
    public function normalize(Query $query)
    {
        // execute the query
        $itemList = $query->getArrayResult();

        // normalize result (for request of type $queryBuilder->select("item, bp, item.id * 3 as titi"); )
        $normalizedItemList = array();
        foreach ($itemList as $item) {
            $normalizedItem = array();
            foreach ($item as $key => $val) {
                // hack : is_array is added according to this issue : https://github.com/kitpages/KitpagesDataGridBundle/issues/18
                // can't reproduce this error...
                if (is_int($key) && is_array($val)) {
                    foreach ($val as $newKey => $newVal) {
                        $normalizedItem[$newKey] = $newVal;
                    }
                } else {
                    $normalizedItem[$key] = $val;
                }
            }
            $normalizedItemList[] = $normalizedItem;
        }
        return $normalizedItemList;
    }

} 