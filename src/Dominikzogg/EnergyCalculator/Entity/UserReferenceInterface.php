<?php

namespace Dominikzogg\EnergyCalculator\Entity;

use Doctrine\ORM\Mapping as ORM;

interface UserReferenceInterface
{
    /**
     * @return User
     */
    public function getUser();

    /**
     * @param User $user
     * @return $this
     */
    public function setUser(User $user);
}