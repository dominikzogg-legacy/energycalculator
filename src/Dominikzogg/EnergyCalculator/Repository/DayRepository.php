<?php

namespace Dominikzogg\EnergyCalculator\Repository;

use Doctrine\ORM\QueryBuilder;
use Dominikzogg\EnergyCalculator\Entity\Day;
use Dominikzogg\EnergyCalculator\Entity\User;

class DayRepository extends AbstractRepository
{
    /**
     * @param array $filterData
     * @return QueryBuilder
     */
    public function getQueryBuilderForFilterForm(array $filterData = null)
    {
        $qb = $this->createQueryBuilder('d');

        if (isset($filterData['date'])) {
            $this->addEqualFilter($qb, 'd', 'date', $filterData['date']);
        }

        if (isset($filterData['user'])) {
            $this->addEqualFilter($qb, 'd', 'user', $filterData['user']);
        }

        $qb->addOrderBy('d.date', 'DESC');

        return $qb;
    }

    /**
     * @param \DateTime $from
     * @param \DateTime $to
     * @param User $user
     * @return Day[]
     */
    public function getInRange(\DateTime $from, \DateTime $to, User $user = null)
    {
        $qb = $this->getInRangeQueryBuilder($from, $to, $user);

        return $qb->getQuery()->getResult();
    }

    /**
     * @param \DateTime $from
     * @param \DateTime $to
     * @param User $user
     * @param string $alias
     * @return \Doctrine\ORM\QueryBuilder
     */
    public function getInRangeQueryBuilder(\DateTime $from, \DateTime $to, User $user = null, $alias = 'd')
    {
        $qb = $this->createQueryBuilder($alias);
        $qb->andWhere($alias . '.date >= :from');
        $qb->andWhere($alias . '.date <= :to');
        $qb->setParameter('from', $from);
        $qb->setParameter('to', $to);
        $qb->orderBy($alias . '.date', 'ASC');

        if (null !== $user) {
            $qb->andWhere($alias . '.user = :user');
            $qb->setParameter('user', $user->getId());
        }

        return $qb;
    }
}
