<?php

namespace Dominikzogg\EnergyCalculator\Entity;

use Doctrine\ORM\Mapping as ORM;
use Dominikzogg\EnergyCalculator\Voter\RelatedObjectInterface;
use Saxulum\UserProvider\Model\AbstractUser;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Entity(repositoryClass="Dominikzogg\EnergyCalculator\Repository\UserRepository")
 * @ORM\Table(
 *     name="sf_user",
 *     uniqueConstraints={
 *         @ORM\UniqueConstraint(name="username_idx", columns={"username"})
 *     }
 * )
 * @ORM\HasLifecycleCallbacks
 * @UniqueEntity("username")
 */
class User extends AbstractUser implements RelatedObjectInterface
{
    /**
     * @var int
     * @ORM\Column(name="id", type="string")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
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

    /**
     * @var \DateTime
     * @ORM\Column(name="created_at", type="datetime", nullable=true)
     */
    protected $createdAt;

    /**
     * @var \DateTime
     * @ORM\Column(name="updated_at", type="datetime", nullable=true)
     */
    protected $updatedAt;

    public function __construct()
    {
        $this->id = new \MongoId();

        parent::__construct();
    }

    /**
     * @ORM\PrePersist()
     */
    public function setCreatedAt()
    {
        $this->createdAt = new \DateTime();
    }

    /**
     * @ORM\PreUpdate()
     */
    public function setUpdatedAt()
    {
        $this->updatedAt = new \DateTime();
    }

    /**
     * @return \DateTime
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    /**
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * @return array
     */
    public function getSecurityRelatedObjects()
    {
        return array($this);
    }

    /**
     * @return string
     */
    public function getRoleNamePart()
    {
        return 'user';
    }
}
