<?php

namespace Dominikzogg\EnergyCalculator\Form;

use Symfony\Component\Form\Extension\Core\Type\DateTimeType as BaseDateTimeType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormError;
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
                if($data) {
                    try {
                        $data = $this->getData(new \DateTime($data));
                    } catch (\Exception $e){
                        $event->getForm()->addError(
                            new FormError('This value is not a valid date.', null, array(
                                '%dateformat%' => $this->getDateFormat()
                            ))
                        );
                    }
                } else {
                    $data = $this->getData();
                }
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
            'attr' => array(
                'data-provide' => 'datepicker',
                'data-date-format' => 'dd.mm.yyyy',
            )
        ));
    }

    /**
     * @param \DateTime $dateTime
     * @return string
     */
    protected function getData(\DateTime $dateTime = null)
    {
        return null !== $dateTime ? $dateTime->format($this->getDateFormat()) : '';
    }

    /**
     * @return string
     */
    protected function getDateFormat()
    {
        return 'd.m.Y';
    }
    
    /**
     * @return string
     */
    public function getName()
    {
        return 'simpledate';
    }
}
