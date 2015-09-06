<?php

namespace Dominikzogg\EnergyCalculator\Entity;

use Doctrine\ORM\Mapping as ORM;
use Saxulum\Accessor\Accessors\Get;
use Saxulum\Accessor\Accessors\Set;
use Saxulum\Accessor\AccessorTrait;
use Saxulum\Hint\Hint;
use Saxulum\Accessor\Prop;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity()
 * @ORM\Table(name="comestible_within_day")
 * @ORM\HasLifecycleCallbacks
 * @method int getId()
 * @method Day getDay()
 * @method $this setDay(Day $day)
 * @method Comestible getComestible()
 * @method $this setComestible(Comestible $comestible)
 * @method float getAmount()
 * @method $this setAmount(float $amount)
 */
class ComestibleWithinDay
{
    use AccessorTrait;

    /**
     * @var int
     * @ORM\Column(name="id", type="string")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
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
        return (string) $this->getComestible()->getName();
    }

    protected function _initProps()
    {
        $this->_prop((new Prop('id', Hint::INT))->method(Get::PREFIX));
        $this->_prop(
            (new Prop('day', 'Dominikzogg\EnergyCalculator\Entity\Day', true, 'comestiblesWithinDay', Prop::REMOTE_MANY))
                ->method(Get::PREFIX)
                ->method(Set::PREFIX)
        );
        $this->_prop(
            (new Prop('comestible', 'Dominikzogg\EnergyCalculator\Entity\Comestible'))
                ->method(Get::PREFIX)
                ->method(Set::PREFIX)
        );
        $this->_prop((new Prop('amount', Hint::NUMERIC))->method(Get::PREFIX)->method(Set::PREFIX));
        $this->_prop((new Prop('createdAt', '\DateTime'))->method(Get::PREFIX));
        $this->_prop((new Prop('updatedAt', '\DateTime'))->method(Get::PREFIX));
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
        return $this->getComestible()->getCalorie() * $this->getAmount() / 100;
    }

    /**
     * @return float
     */
    public function getProtein()
    {
        return $this->getComestible()->getProtein() * $this->getAmount() / 100;
    }

    /**
     * @return float
     */
    public function getCarbohydrate()
    {
        return $this->getComestible()->getCarbohydrate() * $this->getAmount() / 100;
    }

    /**
     * @return float
     */
    public function getFat()
    {
        return $this->getComestible()->getFat() * $this->getAmount() / 100;
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
}
