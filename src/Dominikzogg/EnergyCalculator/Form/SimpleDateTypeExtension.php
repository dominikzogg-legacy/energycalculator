<?php

namespace Dominikzogg\EnergyCalculator\Form;

use Symfony\Component\Form\AbstractExtension;

class SimpleDateTypeExtension extends AbstractExtension
{
    protected function loadTypes()
    {
        return array(
            new SimpleDateType(),
        );
    }
}