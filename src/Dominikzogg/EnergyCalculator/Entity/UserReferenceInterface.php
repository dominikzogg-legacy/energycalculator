<?php

namespace Dominikzogg\EnergyCalculator\Entity;


interface UserReferenceInterface
{
    /**
     * @return User
     */
    public function getUser();

    /**
     * @param  User  $user
     * @return $this
     */
    public function setUser(User $user);
}
