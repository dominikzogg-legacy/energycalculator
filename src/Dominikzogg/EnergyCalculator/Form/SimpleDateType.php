<?php

namespace Dominikzogg\EnergyCalculator\Form;

use Symfony\Component\OptionsResolver\OptionsResolver;

class SimpleDateType extends AbstractType
{
    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);

        $resolver->setDefaults(array(
            'widget' => 'single_text',
            'format' => 'dd.MM.yyyy',
            'attr' => array(
                'data-provide' => 'datepicker',
                'data-date-format' => 'dd.mm.yyyy',
            ),
        ));
    }

    /**
     * @return string
     */
    public function getParent()
    {
        return 'date';
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'simpledate';
    }
}
