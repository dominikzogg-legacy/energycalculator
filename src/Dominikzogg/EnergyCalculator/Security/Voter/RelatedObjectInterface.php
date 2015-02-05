<?php

namespace Dominikzogg\EnergyCalculator\Security\Voter;

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