<?php

namespace Dominikzogg\EnergyCalculator\Repository;

use Doctrine\ORM\QueryBuilder;

class ComestibleRepository extends AbstractRepository
{
    /**
     * @param array $filterData
     * @return QueryBuilder
     */
    public function getQueryBuilderForFilterForm(array $filterData = array())
    {
        $qb = $this->createQueryBuilder('c');

        if (isset($filterData['user'])) {
            $this->addEqualFilter($qb, 'c', 'user', $filterData['user']);
        }

        if (isset($filterData['name'])) {
            $this->addLikeFilter($qb, 'c', 'name', $filterData['name']);
        }

        $qb->addOrderBy('c.name', 'ASC');

        return $qb;
    }
}
