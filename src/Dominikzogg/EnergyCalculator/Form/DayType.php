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

    /**
     * @param User $user
     */
    public function setUser(User $user)
    {
        $this->user = $user;
    }

    /**
     * @param Translator $translator
     */
    public function setTranslator(Translator $translator)
    {
        $this->translator = $translator;
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $addButtonText = 'add';
        $deleteButtonText = 'remove';
        if($this->translator !== null) {
            $addButtonText = $this->translator->trans('day.label.comestiblesWithinDay_collection.add', array(), 'messages');
            $deleteButtonText = $this->translator->trans('day.label.comestiblesWithinDay_collection.remove', array(), 'messages');
        }

        $builder
            ->add('date', 'date', array(
                'widget' => 'single_text',
                'input' => 'datetime',
                'format' => 'dd.MM.yyyy',
            ))
            ->add('weight', 'number', array('required' => false))
            ->add('abdominalCircumference', 'number', array('required' => false))
            ->add('comestiblesWithinDay', 'bootstrap_collection', array(
                'type' => new ComestibleWithinDayType($this->user),
                'allow_add' => true,
                'add_button_text' => $addButtonText,
                'allow_delete' => true,
                'delete_button_text' => $deleteButtonText,
                'sub_widget_col' => 12,
                'button_col' => '12 col-lg-offset-2',
                'by_reference' => false,
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
            'data_class' => get_class(new Day()),
        ));
    }

    public function getName()
    {
        return 'day';
    }
}
