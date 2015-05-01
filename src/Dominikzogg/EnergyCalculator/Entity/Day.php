<?php

namespace Dominikzogg\EnergyCalculator\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Dominikzogg\EnergyCalculator\Voter\RelatedObjectInterface;
use Saxulum\Accessor\Accessors\Add;
use Saxulum\Accessor\Accessors\Get;
use Saxulum\Accessor\Accessors\Remove;
use Saxulum\Accessor\Accessors\Set;
use Saxulum\Accessor\AccessorTrait;
use Saxulum\Hint\Hint;
use Saxulum\Accessor\Prop;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="Dominikzogg\EnergyCalculator\Repository\DayRepository")
 * @ORM\Table(name="day", uniqueConstraints={
 *      @ORM\UniqueConstraint(name="day_per_user_idx", columns={"date", "user_id"})
 * })
 * @ORM\HasLifecycleCallbacks
 * @UniqueEntity(fields={"date", "user"}, message="day.unique")
 * @method float getId()
 * @method \DateTime getDate()
 * @method $this setDate(\DateTime $date)
 * @method float getWeight()
 * @method $this setWeight(float $weight)
 * @method $this addComestiblesWithinDay(ComestibleWithinDay $comestiblesWithinDay)
 * @method ComestibleWithinDay[] getComestiblesWithinDay()
 * @method $this removeComestiblesWithinDay(ComestibleWithinDay $comestiblesWithinDay)
 * @method $this setComestiblesWithinDay(array $comestiblesWithinDay)
 */
class Day implements UserReferenceInterface, RelatedObjectInterface
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
     * @var \DateTime
     * @ORM\Column(name="date", type="date", nullable=false)
     * @Assert\NotNull()
     * @Assert\Date()
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
     * @var ComestibleWithinDay[]|Collection
     * @ORM\OneToMany(targetEntity="ComestibleWithinDay", mappedBy="day", cascade={"persist"})
     * @Assert\Valid()
     */
    protected $comestiblesWithinDay;

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
        $this->date = new \DateTime();
        $this->comestiblesWithinDay = new ArrayCollection();
    }

    protected function _initProps()
    {
        $this->_prop((new Prop('id', Hint::NUMERIC))->method(Get::PREFIX));
        $this->_prop((new Prop('date', '\DateTime', true))->method(Get::PREFIX)->method(Set::PREFIX));
        $this->_prop((new Prop('weight', Hint::NUMERIC))->method(Get::PREFIX)->method(Set::PREFIX));
        $this->_prop(
            (new Prop('comestiblesWithinDay', 'Dominikzogg\EnergyCalculator\Entity\ComestibleWithinDay[]', true, 'day', Prop::REMOTE_ONE))
                ->method(Add::PREFIX)
                ->method(Get::PREFIX)
                ->method(Remove::PREFIX)
                ->method(Set::PREFIX)
        );
        $this->_prop((new Prop('createdAt', '\DateTime'))->method(Get::PREFIX));
        $this->_prop((new Prop('updatedAt', '\DateTime'))->method(Get::PREFIX));
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return (string) $this->getDate()->format('d.m.Y');
    }

    /**
     * @return float
     */
    public function getCalorie()
    {
        $calorie = 0;
        foreach ($this->getComestiblesWithinDay() as $comestiblesWithinDay) {
            $calorie += $comestiblesWithinDay->getCalorie();
        }

        return $calorie;
    }

    /**
     * @return float
     */
    public function getProtein()
    {
        $protein = 0;
        foreach ($this->getComestiblesWithinDay() as $comestiblesWithinDay) {
            $protein += $comestiblesWithinDay->getProtein();
        }

        return $protein;
    }

    /**
     * @return float
     */
    public function getCarbohydrate()
    {
        $carbohydrate = 0;
        foreach ($this->getComestiblesWithinDay() as $comestiblesWithinDay) {
            $carbohydrate += $comestiblesWithinDay->getCarbohydrate();
        }

        return $carbohydrate;
    }

    /**
     * @return float
     */
    public function getFat()
    {
        $fat = 0;
        foreach ($this->getComestiblesWithinDay() as $comestiblesWithinDay) {
            $fat += $comestiblesWithinDay->getFat();
        }

        return $fat;
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
        return 'day';
    }
}
