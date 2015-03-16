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
    public function normalize(Query $query, QueryBuilder $queryBuilder, $hydratorClass = null)
    {

        /*
         * Add custom hydrator
         */
        $emConfig = $queryBuilder->getEntityManager()->getConfiguration();
        $hydrator = new \ReflectionClass($hydratorClass);
        $hydratorName = $hydrator->getShortName();
        $emConfig->addCustomHydrationMode($hydratorName, $hydratorClass);

        return $query->getResult($hydratorName);
    }
}
