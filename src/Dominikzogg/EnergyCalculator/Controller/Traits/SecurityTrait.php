<?php

namespace Dominikzogg\EnergyCalculator\Controller\Traits;

use Dominikzogg\EnergyCalculator\Entity\User;
use Symfony\Component\Security\Core\SecurityContext;

trait SecurityTrait
{
    /**
     * @var SecurityContext
     */
    protected $security;

    /**
     * @return User|Null|string
     */
    protected function getUser()
    {
        if (is_null($this->security->getToken())) {
            return null;
        }

        $user = $this->security->getToken()->getUser();

        if ($user instanceof User) {
            $user = $this->doctrine->getManager()->getRepository(get_class($user))->find($user->getId());
        }

        return $user;
    }
}