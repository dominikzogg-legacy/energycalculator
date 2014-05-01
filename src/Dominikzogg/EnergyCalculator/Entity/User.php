<?php

namespace Dominikzogg\EnergyCalculator\Entity;

use Doctrine\ORM\Mapping as ORM;
use Saxulum\UserProvider\Model\AbstractUser;

/**
 * @ORM\Entity()
 * @ORM\Table(
 *     name="user",
 *     uniqueConstraints={
 *         @ORM\UniqueConstraint(name="username_idx", columns={"username"})
 *     }
 * )
 */
class User extends AbstractUser
{
    /**
     * @var int
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var string $username
     * @ORM\Column(name="username", type="string")
     */
    protected $username;

    /**
     * @var string $password
     * @ORM\Column(name="password", type="string")
     */
    protected $password;

    /**
     * @var string $salt
     * @ORM\Column(name="salt", type="string")
     */
    protected $salt;

    /**
     * @var string $email
     * @ORM\Column(name="email", type="string")
     */
    protected $email;

    /**
     * @var array $roles
     * @ORM\Column(name="roles", type="json_array")
     */
    protected $roles;

    /**
     * @var boolean $enabled
     * @ORM\Column(name="enabled", type="boolean")
     */
    protected $enabled = false;
}