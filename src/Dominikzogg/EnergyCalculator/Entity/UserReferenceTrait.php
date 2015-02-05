<?php

namespace Dominikzogg\EnergyCalculator\Entity;

use Doctrine\ORM\Mapping as ORM;
use Dominikzogg\EnergyCalculator\Voter\RelatedObjectInterface;

trait UserReferenceTrait
{
    /**
     * @var User
     * @ORM\ManyToOne(targetEntity="User")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */
    protected $user;

    /**
     * @return User
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @param  User  $user
     * @return $this
     */
    public function setUser(User $user)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * @return RelatedObjectInterface[]
     */
    public function getSecurityRelatedObjects()
    {
        return array($this->getUser());
    }
}
