<?php

namespace Dominikzogg\EnergyCalculator\Form;

use Dominikzogg\EnergyCalculator\Entity\Comestible;
use Dominikzogg\EnergyCalculator\Entity\ComestibleWithinDay;
use Dominikzogg\EnergyCalculator\Entity\User;
use Dominikzogg\EnergyCalculator\Repository\ComestibleRepository;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Translation\Translator;

class ComestibleWithinDayType extends AbstractType
{
    /**
     * @var User
     */
    protected $user;

    /**
     * @var Translator
     */
    protected $translator;

    /**
     * @param User       $user
     * @param Translator $translator
     */
    public function __construct(User $user, Translator $translator)
    {
        $this->user = $user;
        $this->translator = $translator;
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('comestible', 'ajax_entity', array(
                'class' => Comestible::class,
                'route' => 'comestible_choice',
                'property' => 'name',
                'query_builder' => function (ComestibleRepository $er) {
                    return $er->searchComestibleOfUserQb($this->user);
                },
                'required' => false,
                'attr' => array(
                    'placeholder' => $this->translator->trans('day.edit.label.comestiblesWithinDay_collection.comestible_default'),
                ),
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
            'data_class' => ComestibleWithinDay::class,
        ));
    }

    public function getName()
    {
        return 'comestible_within_day';
    }
}
