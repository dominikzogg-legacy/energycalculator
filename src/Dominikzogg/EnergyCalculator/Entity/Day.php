<?php

namespace Dominikzogg\EnergyCalculator\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="Dominikzogg\EnergyCalculator\Repository\DayRepository")
 * @ORM\Table(name="day", uniqueConstraints={
 *      @ORM\UniqueConstraint(name="day_per_user_idx", columns={"date", "user_id"})
 * })
 * @UniqueEntity(fields={"date", "user"}, message="day.unique")
 */
class Day implements UserReferenceInterface
{
    use UserReferenceTrait;
    use AttributeTrait;

    /**
     * @var int
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var \DateTime
     * @ORM\Column(name="date", type="date", nullable=false)
     */
    protected $date;

    /**
     * @var float
     * @ORM\Column(name="weight", type="decimal", precision=10, scale=4, nullable=true)
     * @Assert\Range(
     *      min=0,
     *      max=500,
     *      minMessage="day.weight.range.minmessage",
     *      maxMessage="day.weight.range.maxmessage",
     *      invalidMessage="day.weight.range.invalidmessage"
     * )
     */
    protected $weight;

    /**
     * @var float
     * @ORM\Column(name="abdominal_circumference", type="decimal", precision=10, scale=4, nullable=true)
     * @Assert\Range(
     *      min=0,
     *      max=500,
     *      minMessage="day.abdominalCircumference.range.minmessage",
     *      maxMessage="day.abdominalCircumference.range.maxmessage",
     *      invalidMessage="day.abdominalCircumference.range.invalidmessage"
     * )
     */
    protected $abdominalCircumference;

    /**
     * @var ComestibleWithinDay[]|Collection
     * @ORM\OneToMany(targetEntity="ComestibleWithinDay", mappedBy="day", cascade={"persist"})
     * @Assert\Valid()
     */
    protected $comestiblesWithinDay;

    public function __construct()
    {
        $this->date = new \DateTime();
        $this->comestiblesWithinDay = new ArrayCollection();
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return (string) $this->getDate()->format('d.m.Y');
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return \DateTime
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * @param \DateTime $date
     * @return $this
     */
    public function setDate($date)
    {
        $this->date = $date;
        return $this;
    }

    /**
     * @return float
     */
    public function getWeight()
    {
        return $this->weight;
    }

    /**
     * @param float $weight
     * @return $this
     */
    public function setWeight($weight)
    {
        $this->weight = $weight;
        return $this;
    }

    /**
     * @return float
     */
    public function getAbdominalCircumference()
    {
        return $this->abdominalCircumference;
    }

    /**
     * @param float $abdominalCircumference
     * @return $this
     */
    public function setAbdominalCircumference($abdominalCircumference)
    {
        $this->abdominalCircumference = $abdominalCircumference;
        return $this;
    }

    /**
     * @param ComestibleWithinDay $comestibleWithinDay
     * @param bool $stopPropagation
     * @return $this
     */
    public function addComestibleWithinDay(ComestibleWithinDay $comestibleWithinDay, $stopPropagation = false)
    {
        $this->comestiblesWithinDay->add($comestibleWithinDay);
        $this->resetValues();
        if(!$stopPropagation) {
            $comestibleWithinDay->setDay($this, true);
        }
        return $this;
    }

    /**
     * @param ComestibleWithinDay $comestibleWithinDay
     * @param bool $stopPropagation
     * @return $this
     */
    public function removeComestibleWithinDay(ComestibleWithinDay $comestibleWithinDay, $stopPropagation = false)
    {
        $this->comestiblesWithinDay->removeElement($comestibleWithinDay);
        $this->resetValues();
        if(!$stopPropagation) {
            $comestibleWithinDay->setDay(null, true);
        }
        return $this;
    }

    /**
     * @param ComestibleWithinDay[] $comestiblesWithinDay
     * @return $this
     */
    public function setComestiblesWithinDay($comestiblesWithinDay)
    {
        foreach($this->comestiblesWithinDay as $comestibleWithinDay) {
            $this->removeComestibleWithinDay($comestibleWithinDay);
        }
        foreach($comestiblesWithinDay as $comestibleWithinDay) {
            $this->addComestibleWithinDay($comestibleWithinDay);
        }
        return $this;
    }

    /**
     * @return ComestibleWithinDay[]|Collection
     */
    public function getComestiblesWithinDay()
    {
        return $this->comestiblesWithinDay;
    }

    /**
     * @return float
     */
    public function getCalorie()
    {
        if($this->calorie === null) {
            $this->calorie = 0;
            foreach($this->getComestiblesWithinDay() as $comestiblesWithinDay) {
                $this->calorie += $comestiblesWithinDay->getCalorie();
            }
        }

        return $this->calorie;
    }

    /**
     * @return float
     */
    public function getProtein()
    {
        if($this->protein === null) {
            $this->protein = 0;
            foreach($this->getComestiblesWithinDay() as $comestiblesWithinDay) {
                $this->protein += $comestiblesWithinDay->getProtein();
            }
        }

        return $this->protein;
    }

    /**
     * @return float
     */
    public function getCarbohydrate()
    {
        if($this->carbohydrate === null) {
            $this->carbohydrate = 0;
            foreach($this->getComestiblesWithinDay() as $comestiblesWithinDay) {
                $this->carbohydrate += $comestiblesWithinDay->getCarbohydrate();
            }
        }

        return $this->carbohydrate;
    }

    /**
     * @return float
     */
    public function getFat()
    {
        if($this->fat === null) {
            $this->fat = 0;
            foreach($this->getComestiblesWithinDay() as $comestiblesWithinDay) {
                $this->fat += $comestiblesWithinDay->getFat();
            }
        }

        return $this->fat;
    }
}
