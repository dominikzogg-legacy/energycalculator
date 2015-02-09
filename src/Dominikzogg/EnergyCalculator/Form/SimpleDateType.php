<?php

namespace Dominikzogg\EnergyCalculator\Form;

use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class SimpleDateType extends AbstractType
{
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        parent::setDefaultOptions($resolver);
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
