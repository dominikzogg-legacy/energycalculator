<?php

namespace Dominikzogg\EnergyCalculator\Controller\Traits;

use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;

trait DoctrineTrait
{
    /**
     * @var ManagerRegistry
     */
    protected $doctrine;

    /**
     * @param string $class
     * @return EntityManager|null
     */
    protected function getManagerForClass($class)
    {
        return $this->doctrine->getManagerForClass($class);
    }

    /**
     * @param string $class
     * @return EntityRepository
     */
    protected function getRepositoryForClass($class)
    {
        return $this->getManagerForClass($class)->getRepository($class);
    }
}