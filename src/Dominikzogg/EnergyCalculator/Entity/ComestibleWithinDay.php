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
 * @ORM\Table(name="comestible_within_day")
 * @method int getId()
 * @method Day getDay()
 * @method $this setDay(Day $day, $stopPropagation = false)
 * @method Comestible getComestible()
 * @method float getAmount()
 */
class ComestibleWithinDay
{
    use AccessorTrait {
        __call as __traitCall;
    }
    use AttributeTrait;

    /**
     * @var int
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var Day
     * @ORM\ManyToOne(targetEntity="Day", inversedBy="comestiblesWithinDay")
     * @ORM\JoinColumn(name="day_id", referencedColumnName="id", onDelete="CASCADE")
     */
    protected $day;

    /**
     * @var Comestible
     * @ORM\ManyToOne(targetEntity="Comestible")
     * @ORM\JoinColumn(name="comestible_id", referencedColumnName="id")
     * @Assert\NotNull(message="day.comestibleWithinDay.comestible.notnull")
     */
    protected $comestible;

    /**
     * @var float
     * @ORM\Column(name="amount", type="decimal", precision=10, scale=4, nullable=false)
     * @Assert\NotNull(message="day.comestibleWithinDay.amount.notnull")
     * @Assert\GreaterThanOrEqual(value=0, message="day.comestibleWithinDay.amount.greaterthanorequal")
     */
    protected $amount = 0;

    /**
     * @return string
     */
    public function __toString()
    {
        return (string) $this->getComestible()->getName();
    }

    /**
     * @param $name
     * @param array $arguments
     * @return mixed
     */
    public function __call($name, array $arguments)
    {
        $resetValueCalls = array(
            'setComestible',
            'setAmount',
        );

        $returnValue = $this->__traitCall($name, $arguments);

        if(in_array($name, $resetValueCalls)) {
            $this->resetValues();
        }

        return $returnValue;
    }

    protected function initializeProperties()
    {
        $this->prop((new Prop('id'))->method(Get::PREFIX));
        $this->prop(
            (new Prop('day', 'Dominikzogg\EnergyCalculator\Entity\Day', true, 'comestiblesWithinDay', Prop::REMOTE_MANY))
                ->method(Get::PREFIX)
                ->method(Set::PREFIX)
        );
        $this->prop(
            (new Prop('comestible', 'Dominikzogg\EnergyCalculator\Entity\Comestible'))
                ->method(Get::PREFIX)
                ->method(Set::PREFIX)
        );
        $this->prop((new Prop('amount', Hint::HINT_NUMERIC))->method(Get::PREFIX)->method(Set::PREFIX));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->getComestible()->getName();
    }

    /**
     * @return float
     */
    public function getCalorie()
    {
        if($this->calorie === null) {
            $this->calorie = $this->getComestible()->getCalorie() * $this->getAmount() / 100;
        }

        return $this->calorie;
    }

    /**
     * @return float
     */
    public function getProtein()
    {
        if($this->protein === null) {
            $this->protein = $this->getComestible()->getProtein() * $this->getAmount() / 100;
        }

        return $this->protein;
    }

    /**
     * @return float
     */
    public function getCarbohydrate()
    {
        if($this->carbohydrate === null) {
            $this->carbohydrate = $this->getComestible()->getCarbohydrate() * $this->getAmount() / 100;
        }

        return $this->carbohydrate;
    }

    /**
     * @return float
     */
    public function getFat()
    {
        if($this->fat === null) {
            $this->fat = $this->getComestible()->getFat() * $this->getAmount() / 100;
        }

        return $this->fat;
    }
}
