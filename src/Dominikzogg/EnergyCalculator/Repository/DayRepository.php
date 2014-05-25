<?php

namespace Dominikzogg\EnergyCalculator\Repository;

use Doctrine\ORM\EntityRepository;

class DayRepository extends EntityRepository
{
    /**
     * @param \DateTime $from
     * @param \DateTime $to
     * @return array
     */
    public function getInRange(\DateTime $from, \DateTime $to)
    {
        $qb = $this->getInRangeQueryBuilder($from, $to);

        return $qb->getQuery()->getResult();
    }

    /**
     * @param \DateTime $from
     * @param \DateTime $to
     * @param string $alias
     * @return \Doctrine\ORM\QueryBuilder
     */
    public function getInRangeQueryBuilder(\DateTime $from, \DateTime $to, $alias = 'd')
    {
        $qb = $this->createQueryBuilder($alias);
        $qb->andWhere($alias . '.date >= :from');
        $qb->andWhere($alias . '.date <= :to');
        $qb->setParameter('from', $from);
        $qb->setParameter('to', $to);
        $qb->orderBy($alias . '.date', 'ASC');

        return $qb;
    }
}