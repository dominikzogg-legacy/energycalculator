<?php

namespace Dominikzogg\EnergyCalculator\Form;

use Dominikzogg\EnergyCalculator\Entity\Day;
use Dominikzogg\EnergyCalculator\Entity\User;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Translation\Translator;

class DayType extends AbstractType
{
    /**
     * @var User
     */
    protected $user;

    /**
     * @var Translator
     */
    protected $translator;

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
        $addButtonText = $this->translator->trans('day.label.comestiblesWithinDay_collection.add', array(), 'messages');
        $deleteButtonText = $this->translator->trans('day.label.comestiblesWithinDay_collection.remove', array(), 'messages');

        $builder
            ->add('date', 'simpledate')
            ->add('weight', 'number', array('required' => false))
            ->add('comestiblesWithinDay', 'bootstrap_collection', array(
                'type' => new ComestibleWithinDayType($this->user, $this->translator),
                'allow_add' => true,
                'add_button_text' => $addButtonText,
                'allow_delete' => true,
                'delete_button_text' => $deleteButtonText,
                'sub_widget_col' => 12,
                'button_col' => '12 col-lg-offset-2',
                'by_reference' => false,
                'error_bubbling' => false
            ))
        ;
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        parent::setDefaultOptions($resolver);
        $resolver->setDefaults(array(
            'data_class' => Day::class,
        ));
    }

    public function getName()
    {
        return 'day';
    }
}
