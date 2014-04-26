<?php

namespace Dominikzogg\EnergyCalculator\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity()
 * @ORM\Table(name="comestible_within_day")
 */
class ComestibleWithinDay
{
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
     */
    protected $comestible;

    /**
     * @var float
     * @ORM\Column(name="amount", type="decimal", precision=10, scale=4, nullable=false)
     */
    protected $amount = 0;

    /**
     * @var float
     */
    protected $calorie;

    /**
     * @var float
     */
    protected $protein;

    /**
     * @var float
     */
    protected $carbohydrate;

    /**
     * @var float
     */
    protected $fat;

    /**
     * @return string
     */
    public function __toString()
    {
        return (string) $this->getComestible()->getName();
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param Day $day
     * @param bool $stopPropagation
     * @return $this
     */
    public function setDay(Day $day = null, $stopPropagation = false)
    {
        if(!$stopPropagation) {
            if(!is_null($this->day)) {
                $this->day->removeComestibleWithinDay($this, true);
            }
            if(!is_null($day)) {
                $day->addComestibleWithinDay($this, true);
            }
        }
        $this->day = $day;
        return $this;
    }
    /**
     * @return Day
     */
    public function getDay()
    {
        return $this->day;
    }

    /**
     * @return Comestible
     */
    public function getComestible()
    {
        return $this->comestible;
    }

    /**
     * @param Comestible $comestible
     * @return $this
     */
    public function setComestible(Comestible $comestible)
    {
        $this->comestible = $comestible;
        $this->resetValues();
        return $this;
    }

    /**
     * @return float
     */
    public function getAmount()
    {
        return $this->amount;
    }

    /**
     * @param float $amount
     * @return $this
     */
    public function setAmount($amount)
    {
        $this->amount = $amount;
        $this->resetValues();
        return $this;
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
