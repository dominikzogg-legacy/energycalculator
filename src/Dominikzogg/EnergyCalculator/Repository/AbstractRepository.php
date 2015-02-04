<?php

namespace Dominikzogg\EnergyCalculator\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use Saxulum\Crud\Repository\QueryBuilderForFilterFormInterface;

abstract class AbstractRepository extends EntityRepository implements QueryBuilderForFilterFormInterface
{
    /**
     * @param QueryBuilder $qb
     * @param string       $alias
     * @param string       $property
     * @param mixed        $value
     */
    protected function addEqualFilter(QueryBuilder $qb, $alias, $property, $value)
    {
        $qb->andWhere($qb->expr()->eq($alias.'.'.$property, ':'.$property));
        $qb->setParameter($property, $value);
    }

    /**
     * @param QueryBuilder $qb
     * @param string       $alias
     * @param string       $property
     * @param mixed        $value
     */
    protected function addLikeFilter(QueryBuilder $qb, $alias, $property, $value)
    {
        $qb->andWhere($qb->expr()->like($alias.'.'.$property, ':'.$property));
        $qb->setParameter($property, '%'.$value.'%');
    }
}
