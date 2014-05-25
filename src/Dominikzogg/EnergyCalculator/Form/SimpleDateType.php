<?php

namespace Dominikzogg\EnergyCalculator\Form;

use Symfony\Component\Form\Extension\Core\Type\DateTimeType as BaseDateTimeType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class SimpleDateType extends BaseDateTimeType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);

        $builder->addEventListener(FormEvents::PRE_SUBMIT, function(FormEvent $event) {
            $data = $formData = $event->getData();
            if(is_string($data)) {
                try {
                    $dateTime = new \DateTime($data);
                } catch(\Exception $e) {
                    $dateTime = new \DateTime();
                }
                $data = $dateTime->format('d.m.Y');
                $event->setData($data);
            }
        });
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        parent::setDefaultOptions($resolver);
        $resolver->replaceDefaults(array(
            'widget' => 'single_text',
            'format' => 'dd.MM.yyyy',
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'simpledate';
    }
}