<?php

namespace Dominikzogg\EnergyCalculator\Form;

use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class DateRangeType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('from', 'simpledate')
            ->add('to', 'simpledate')
            ->add('update', 'submit')
        ;
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);

        $resolver->setDefaults(array(
            'method' => 'GET',
            'csrf_protection' => false,
        ));
    }

    public function getName()
    {
        return 'daterange';
    }
}
