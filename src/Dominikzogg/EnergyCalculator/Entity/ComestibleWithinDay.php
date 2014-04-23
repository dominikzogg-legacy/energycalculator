<?php

namespace Dominikzogg\EnergyCalculator\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity()
 * @ORM\Table(name="comestible_within_day")
 */
class ComestibleWithinDay
{
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
     * @ORM\OneToOne(targetEntity="Comestible")
     * @ORM\JoinColumn(name="comestible_id", referencedColumnName="id")
     */
    protected $comestible;

    /**
     * @var float
     * @ORM\Column(name="amount", type="decimal", precision=10, scale=4, nullable=false)
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
        return $this;
    }
}
