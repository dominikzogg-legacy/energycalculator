<?php

namespace Dominikzogg\EnergyCalculator\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Saxulum\Accessor\Accessors\Add;
use Saxulum\Accessor\Accessors\Get;
use Saxulum\Accessor\Accessors\Remove;
use Saxulum\Accessor\Accessors\Set;
use Saxulum\Accessor\AccessorTrait;
use Saxulum\Accessor\Hint;
use Saxulum\Accessor\Prop;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="Dominikzogg\EnergyCalculator\Repository\DayRepository")
 * @ORM\Table(name="day", uniqueConstraints={
 *      @ORM\UniqueConstraint(name="day_per_user_idx", columns={"date", "user_id"})
 * })
 * @UniqueEntity(fields={"date", "user"}, message="day.unique")
 * @method int getId()
 * @method $this setDate(\DateTime $date = null)
 * @method \DateTime|null getDate()
 * @method $this setWeight($weight)
 * @method float getWeight()
 * @method $this setAbdominalCircumference($abdominalCircumference)
 * @method float getAbdominalCircumference()
 * @method ComestibleWithinDay[] getComestiblesWithinDay()
 */
class Day implements UserReferenceInterface
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

    protected function initializeProperties()
    {
        $this->prop((new Prop('id'))->method(Get::PREFIX));
        $this->prop((new Prop('date', '\DateTime', true))->method(Get::PREFIX)->method(Set::PREFIX));
        $this->prop((new Prop('weight', Hint::HINT_NUMERIC))->method(Get::PREFIX)->method(Set::PREFIX));
        $this->prop((new Prop('abdominalCircumference', Hint::HINT_NUMERIC))->method(Get::PREFIX)->method(Set::PREFIX));
        $this->prop(
            (new Prop('comestiblesWithinDay', 'Dominikzogg\EnergyCalculator\Entity\ComestibleWithinDay[]', true, 'day', Prop::REMOTE_ONE))
                ->method(Add::PREFIX)
                ->method(Get::PREFIX)
                ->method(Remove::PREFIX)
                ->method(Set::PREFIX)
        );
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
        foreach($this->getComestiblesWithinDay() as $comestiblesWithinDay) {
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
        foreach($this->getComestiblesWithinDay() as $comestiblesWithinDay) {
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
        foreach($this->getComestiblesWithinDay() as $comestiblesWithinDay) {
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
        foreach($this->getComestiblesWithinDay() as $comestiblesWithinDay) {
            $fat += $comestiblesWithinDay->getFat();
        }

        return $fat;
    }
}
