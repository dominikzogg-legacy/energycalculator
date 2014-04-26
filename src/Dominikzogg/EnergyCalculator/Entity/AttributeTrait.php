<?php

namespace Dominikzogg\EnergyCalculator\Entity;

trait AttributeTrait
{
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
     * @return float
     */
    abstract public function getCalorie();

    /**
     * @return float
     */
    abstract public function getProtein();

    /**
     * @return float
     */
    abstract public function getCarbohydrate();

    /**
     * @return float
     */
    abstract public function getFat();

    protected function resetValues()
    {
        $this->calorie = null;
        $this->protein = null;
        $this->carbohydrate = null;
        $this->fat = null;
    }
}