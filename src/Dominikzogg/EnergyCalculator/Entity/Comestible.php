<?php

namespace Dominikzogg\EnergyCalculator\Entity;

use Doctrine\ORM\Mapping as ORM;
use Saxulum\Accessor\Accessors\Get;
use Saxulum\Accessor\Accessors\Set;
use Saxulum\Accessor\AccessorTrait;
use Saxulum\Accessor\Hint;
use Saxulum\Accessor\Prop;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity()
 * @ORM\Table(name="comestible")
 * @method int getId()
 * @method $this setName($name)
 * @method string getName()
 * @method $this setCalorie($calorie)
 * @method float getCalorie()
 * @method $this setProtein($protein)
 * @method float getProtein()
 * @method $this setCarbohydrate($carbohydrate)
 * @method float getCarbohydrate()
 * @method $this setFat($fat)
 * @method float getFat()
 * @method $this setDefaultValue($defaultValue)
 * @method string getDefaultValue()
 */
class Comestible implements UserReferenceInterface
{
    use AccessorTrait;
    use UserReferenceTrait;

    /**
     * @var int
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
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
     * @return string
     */
    public function __toString()
    {
        return (string) $this->getName();
    }

    protected function initializeProperties()
    {
        $this->prop((new Prop('id'))->method(Get::PREFIX));
        $this->prop((new Prop('name', Hint::HINT_STRING))->method(Get::PREFIX)->method(Set::PREFIX));
        $this->prop((new Prop('calorie', Hint::HINT_NUMERIC))->method(Get::PREFIX)->method(Set::PREFIX));
        $this->prop((new Prop('protein', Hint::HINT_NUMERIC))->method(Get::PREFIX)->method(Set::PREFIX));
        $this->prop((new Prop('carbohydrate', Hint::HINT_NUMERIC))->method(Get::PREFIX)->method(Set::PREFIX));
        $this->prop((new Prop('fat', Hint::HINT_NUMERIC))->method(Get::PREFIX)->method(Set::PREFIX));
        $this->prop((new Prop('defaultValue', Hint::HINT_NUMERIC))->method(Get::PREFIX)->method(Set::PREFIX));
    }
}
