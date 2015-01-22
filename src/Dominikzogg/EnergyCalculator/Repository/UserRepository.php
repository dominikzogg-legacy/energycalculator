<?php

namespace Dominikzogg\EnergyCalculator\Repository;

use Doctrine\ORM\QueryBuilder;

class UserRepository extends AbstractRepository
{
    /**
     * @param array $filterData
     * @return QueryBuilder
     */
    public function getQueryBuilderForFilterForm(array $filterData = array())
    {
        $qb = $this->createQueryBuilder('u');

        $qb->addOrderBy('u.username', 'ASC');

        return $qb;
    }
}