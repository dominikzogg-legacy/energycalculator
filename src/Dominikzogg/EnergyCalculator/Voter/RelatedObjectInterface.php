<?php

namespace Dominikzogg\EnergyCalculator\Voter;

interface RelatedObjectInterface
{
    /**
     * @return RelatedObjectInterface[]
     */
    public function getSecurityRelatedObjects();

    /**
     * @return string
     */
    public function getRoleNamePart();
}