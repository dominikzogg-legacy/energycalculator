<?php

namespace Dominikzogg\EnergyCalculator\Form;

use Dominikzogg\EnergyCalculator\Entity\Comestible;
use Dominikzogg\EnergyCalculator\Entity\ComestibleWithinDay;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Translation\Translator;

class ComestibleWithinDayType extends AbstractType
{
    /**
     * @var Translator
     */
    protected $translator;

    /**
     * @param Translator $translator
     */
    public function __construct(Translator $translator)
    {
        $this->translator = $translator;
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('comestible', 'ajax_choice', array(
                'class' => get_class(new Comestible()),
                'route' => 'comestible_choice',
                'property' => 'name',
                'required' => false,
                'attr' => array(
                    'placeholder' => $this->translator->trans('day.edit.label.comestiblesWithinDay_collection.comestible_default')
                )
            ))
            ->add('amount', 'number', array('required' => true))
        ;
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        parent::setDefaultOptions($resolver);
        $resolver->setDefaults(array(
            'data_class' => get_class(new ComestibleWithinDay()),
        ));
    }

    public function getName()
    {
        return 'comestible_within_day';
    }
}
