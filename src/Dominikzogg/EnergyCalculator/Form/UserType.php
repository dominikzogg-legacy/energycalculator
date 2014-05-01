<?php

namespace Dominikzogg\EnergyCalculator\Form;

use Saxulum\UserProvider\Form\AbstractUserType;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class UserType extends AbstractUserType
{
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        parent::setDefaultOptions($resolver);
        $resolver->setDefaults(array(
            'translation_domain' => 'messages'
        ));
    }
}