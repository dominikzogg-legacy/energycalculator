<?php

namespace Dominikzogg\EnergyCalculator\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Mapping\ClassMetadata;

/**
 * @ORM\Entity()
 * @ORM\Table(name="comestible")
 */
class Comestible implements UserReferenceInterface
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
     * @var string
     * @ORM\Column(name="name", type="string", nullable=false)
     */
    protected $name;

    /**
     * @var float
     * @ORM\Column(name="calorie", type="decimal", precision=8, scale=4, nullable=false)
     */
    protected $calorie = 0;

    /**
     * @var float
     * @ORM\Column(name="protein", type="decimal", precision=8, scale=4, nullable=false)
     */
    protected $protein = 0;

    /**
     * @var float
     * @ORM\Column(name="fat", type="decimal", precision=8, scale=4, nullable=false)
     */
    protected $fat = 0;

    /**
     * @var float
     * @ORM\Column(name="carbohydrate", type="decimal", precision=8, scale=4, nullable=false)
     */
    protected $carbohydrate = 0;

    /**
     * @return string
     */
    public function __toString()
    {
        return (string) $this->getName();
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param  string   $name
     * @return $this
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return float
     */
    public function getCalorie()
    {
        return $this->calorie;
    }

    /**
     * @param float $calorie
     * @return $this
     */
    public function setCalorie($calorie)
    {
        $this->calorie = $calorie;

        return $this;
    }

    /**
     * @return float
     */
    public function getProtein()
    {
        return $this->protein;
    }

    /**
     * @param float $protein
     * @return $this
     */
    public function setProtein($protein)
    {
        $this->protein = $protein;
        return $this;
    }

    /**
     * @return float
     */
    public function getFat()
    {
        return $this->fat;
    }

    /**
     * @param float $fat
     * @return $this
     */
    public function setFat($fat)
    {
        $this->fat = $fat;
        return $this;
    }

    /**
     * @return float
     */
    public function getCarbohydrate()
    {
        return $this->carbohydrate;
    }

    /**
     * @param float $carbohydrate
     * @return $this
     */
    public function setCarbohydrate($carbohydrate)
    {
        $this->carbohydrate = $carbohydrate;
        return $this;
    }

    /**
     * @param ClassMetadata $metadata
     */
    public static function loadValidatorMetadata(ClassMetadata $metadata)
    {

    }
}
