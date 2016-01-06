<?php

namespace Dominikzogg\EnergyCalculator\Form;

use Doctrine\ORM\QueryBuilder;
use Dominikzogg\EnergyCalculator\Entity\Comestible;
use Dominikzogg\EnergyCalculator\Entity\ComestibleWithinDay;
use Dominikzogg\EnergyCalculator\Entity\User;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Translation\TranslatorInterface;

class ComestibleWithinDayType extends AbstractType
{
    /**
     * @var User
     */
    protected $user;

    /**
     * @var TranslatorInterface
     */
    protected $translator;

    /**
     * @var QueryBuilder
     */
    protected $comestibleQb;

    /**
     * @param User       $user
     * @param TranslatorInterface $translator
     */
    public function __construct(User $user, TranslatorInterface $translator, QueryBuilder $comestibleQb)
    {
        $this->user = $user;
        $this->translator = $translator;
        $this->comestibleQb = $comestibleQb;
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
                'choice_label' => 'name',
                'query_builder' => $this->comestibleQb,
                'required' => false,
                'attr' => array(
                    'data-ajax' => true,
                    'placeholder' => $this->translator->trans('day.edit.label.comestibles_within_day_collection.comestible_default'),
                ),
            ))
            ->add('amount', 'number', array('required' => true))
        ;
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);

        $resolver->setDefaults(array(
            'data_class' => ComestibleWithinDay::class,
        ));
    }

    public function getBlockPrefix()
    {
        return 'comestible_within_day';
    }
}
