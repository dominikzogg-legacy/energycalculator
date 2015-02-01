<?php

namespace Dominikzogg\EnergyCalculator\Repository;

use Doctrine\ORM\QueryBuilder;
use Dominikzogg\EnergyCalculator\Entity\Comestible;
use Dominikzogg\EnergyCalculator\Entity\User;

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

    /**
     * @param User $user
     * @param string $search
     * @return Comestible[]
     */
    public function searchComestibleOfUser(User $user, $search)
    {
        $qb = $this->createQueryBuilder('c');
        $qb->where($qb->expr()->eq('c.user', ':user'));
        $qb->setParameter('user', $user->getId());
        $qb->andWhere($qb->expr()->like('c.name', ':name'));
        $qb->setParameter('name', '%' . $search . '%');
        $qb->orderBy('c.name');

        return $qb->getQuery()->getResult();
    }
}
