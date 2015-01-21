<?php

namespace Dominikzogg\EnergyCalculator\Repository;

use Doctrine\Common\Persistence\ObjectRepository;
use Doctrine\ORM\QueryBuilder;

interface QueryBuilderForFilterFormInterface extends ObjectRepository
{
    /**
     * @param array $filterData
     * @return QueryBuilder
     */
    public function getQueryBuilderForFilterForm(array $filterData = array());
}