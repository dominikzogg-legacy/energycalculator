<?php

namespace Dominikzogg\EnergyCalculator\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Mapping\ClassMetadata;

/**
 * @ORM\Entity()
 * @ORM\Table(name="day", uniqueConstraints={
 *      @ORM\UniqueConstraint(name="day_per_user_idx", columns={"date", "user_id"})
 * })
 */
class Day implements UserReferenceInterface
{
    use UserReferenceTrait;

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
     * @ORM\Column(name="weight_morning", type="decimal", precision=10, scale=4, nullable=true)
     */
    protected $weightMorning;

    /**
     * @var float
     * @ORM\Column(name="weight_evening", type="decimal", precision=10, scale=4, nullable=true)
     */
    protected $weightEvening;

    /**
     * @var ComestibleWithinDay[]|Collection
     * @ORM\OneToMany(targetEntity="ComestibleWithinDay", mappedBy="day", cascade={"persist"})
     */
    protected $comestiblesWithinDay;

    public function __construct()
    {
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
    public function getWeightMorning()
    {
        return $this->weightMorning;
    }

    /**
     * @param float $weightMorning
     * @return $this
     */
    public function setWeightMorning($weightMorning)
    {
        $this->weightMorning = $weightMorning;
        return $this;
    }

    /**
     * @return float
     */
    public function getWeightEvening()
    {
        return $this->weightEvening;
    }

    /**
     * @param float $weightEvening
     * @return $this
     */
    public function setWeightEvening($weightEvening)
    {
        $this->weightEvening = $weightEvening;
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
     * @param ClassMetadata $metadata
     */
    public static function loadValidatorMetadata(ClassMetadata $metadata)
    {
        $metadata->addConstraint(new UniqueEntity(array(
            'fields'  => array('date', 'user'),
            'message' => 'This date for this user allready exist.',
        )));
    }
}
