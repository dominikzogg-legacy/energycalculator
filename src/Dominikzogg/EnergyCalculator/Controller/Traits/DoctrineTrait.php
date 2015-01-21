<?php

namespace Dominikzogg\EnergyCalculator\Controller\Traits;

use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\Persistence\ObjectRepository;

trait DoctrineTrait
{
    /**
     * @var ManagerRegistry
     */
    protected $doctrine;

    /**
     * @param string $class
     * @return ObjectManager|null
     */
    protected function getManagerForClass($class)
    {
        return $this->doctrine->getManagerForClass($class);
    }

    /**
     * @param string $class
     * @return ObjectRepository
     */
    protected function getRepositoryForClass($class)
    {
        return $this->getManagerForClass($class)->getRepository($class);
    }
}