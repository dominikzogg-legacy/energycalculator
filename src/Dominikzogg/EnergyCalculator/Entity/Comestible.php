<?php

namespace Dominikzogg\EnergyCalculator\Entity;

use Doctrine\ORM\Mapping as ORM;
use Dominikzogg\EnergyCalculator\Voter\RelatedObjectInterface;
use Saxulum\Accessor\Accessors\Get;
use Saxulum\Accessor\Accessors\Set;
use Saxulum\Accessor\AccessorTrait;
use Saxulum\Hint\Hint;
use Saxulum\Accessor\Prop;

/**
 * @ORM\Entity(repositoryClass="Dominikzogg\EnergyCalculator\Repository\ComestibleRepository")
 * @ORM\Table(name="comestible")
 * @ORM\HasLifecycleCallbacks
 * @method int getId()
 * @method string getName()
 * @method $this setName(string $name)
 * @method float getCalorie()
 * @method $this setCalorie(float $calorie)
 * @method float getProtein()
 * @method $this setProtein(float $protein)
 * @method float getCarbohydrate()
 * @method $this setCarbohydrate(float $carbohydrate)
 * @method float getFat()
 * @method $this setFat(float $fat)
 * @method float getDefaultValue()
 * @method $this setDefaultValue(float $defaultValue)
 */
class Comestible implements UserReferenceInterface, RelatedObjectInterface
{
    use AccessorTrait;
    use UserReferenceTrait;

    /**
     * @var int
     * @ORM\Column(name="id", type="string")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     */
    protected $id;

    /**
     * @var string
     * @ORM\Column(name="name", type="string", nullable=false)
     */
    protected $name;

    /**
     * @var float
     * @ORM\Column(name="calorie", type="decimal", precision=10, scale=4, nullable=false)
     */
    protected $calorie = 0;

    /**
     * @var float
     * @ORM\Column(name="protein", type="decimal", precision=10, scale=4, nullable=false)
     */
    protected $protein = 0;

    /**
     * @var float
     * @ORM\Column(name="carbohydrate", type="decimal", precision=10, scale=4, nullable=false)
     */
    protected $carbohydrate = 0;

    /**
     * @var float
     * @ORM\Column(name="fat", type="decimal", precision=10, scale=4, nullable=false)
     */
    protected $fat = 0;

    /**
     * @var float
     * @ORM\Column(name="default_value", type="decimal", precision=10, scale=4, nullable=true)
     */
    protected $defaultValue;

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
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return (string) $this->getName();
    }

    protected function _initProps()
    {
        $this->_prop((new Prop('id', Hint::INT))->method(Get::PREFIX));
        $this->_prop((new Prop('name', Hint::STRING))->method(Get::PREFIX)->method(Set::PREFIX));
        $this->_prop((new Prop('calorie', Hint::NUMERIC))->method(Get::PREFIX)->method(Set::PREFIX));
        $this->_prop((new Prop('protein', Hint::NUMERIC))->method(Get::PREFIX)->method(Set::PREFIX));
        $this->_prop((new Prop('carbohydrate', Hint::NUMERIC))->method(Get::PREFIX)->method(Set::PREFIX));
        $this->_prop((new Prop('fat', Hint::NUMERIC))->method(Get::PREFIX)->method(Set::PREFIX));
        $this->_prop((new Prop('defaultValue', Hint::NUMERIC))->method(Get::PREFIX)->method(Set::PREFIX));
        $this->_prop((new Prop('createdAt', '\DateTime'))->method(Get::PREFIX));
        $this->_prop((new Prop('updatedAt', '\DateTime'))->method(Get::PREFIX));
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
     * @return string
     */
    public function getRoleNamePart()
    {
        return 'comestible';
    }
}
