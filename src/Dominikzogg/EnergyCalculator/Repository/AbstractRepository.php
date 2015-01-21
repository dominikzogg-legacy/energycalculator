<?php

namespace Dominikzogg\EnergyCalculator\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;

abstract class AbstractRepository extends EntityRepository implements QueryBuilderForFilterFormInterface
{
    /**
     * @param array $filterData
     * @param QueryBuilder $qb
     * @param string $alias
     * @param string $property
     */
    protected function addEqualFilter(array $filterData = null, QueryBuilder $qb, $alias, $property)
    {
        if(null === $filterData) {
            return;
        }

        if(isset($filterData[$property])) {
            $qb->andWhere($qb->expr()->eq($alias . '.' . $property, ':' . $property));
            $qb->setParameter($property, $filterData[$property]);
        }
    }

    /**
     * @param array $filterData
     * @param QueryBuilder $qb
     * @param string $alias
     * @param string $property
     */
    protected function addLikeFilter(array $filterData = null, QueryBuilder $qb, $alias, $property)
    {
        if(null === $filterData) {
            return;
        }

        if(isset($filterData[$property])) {
            $qb->andWhere($qb->expr()->like($alias . '.' . $property, ':' . $property));
            $qb->setParameter($property, '%' . $filterData[$property] . '%');
        }
    }
}