<?php

namespace Dominikzogg\EnergyCalculator\Repository;

use Doctrine\ORM\QueryBuilder;

class ComestibleRepository extends AbstractRepository
{
    /**
     * @param array $filterData
     * @return QueryBuilder
     */
    public function getQueryBuilderForFilterForm(array $filterData = null)
    {
        $qb = $this->createQueryBuilder('c');

        $this->addLikeFilter($filterData, $qb, 'c', 'name');

        return $qb;
    }
}