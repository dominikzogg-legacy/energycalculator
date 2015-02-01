<?php

namespace Dominikzogg\EnergyCalculator\Form;

use Doctrine\Common\Persistence\ManagerRegistry;
use Dominikzogg\EnergyCalculator\Form\Transformer\EntityIdTransformer;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * Class AjaxChoiceType
 * @package Dominikzogg\EnergyCalculator\Form
 */
class AjaxChoiceType extends AbstractType
{
    protected $registry;

    public function __construct(ManagerRegistry $registry)
    {
        $this->registry = $registry;
    }


    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->resetViewTransformers();
        parent::buildForm($builder, $options);
        $transformer = new EntityIdTransformer($this->registry->getManagerForClass($options['class']), $options['class'], $options['property'], $options['multiple']);
        $builder->addModelTransformer($transformer);
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        parent::setDefaultOptions($resolver);
        $resolver->setDefaults(array(
            'multiple' => false
        ));
        $resolver->setRequired(
            array(
                'class',
                'route',
                'property'
            )
        );
    }

    /**
     * @param FormView $view
     * @param FormInterface $form
     * @param array $options
     */
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        parent::buildView($view, $form, $options);
        $view->vars['options'] = $options;
    }

    /**
     * Returns the name of this type.
     *
     * @return string The name of this type
     */
    public function getName()
    {
        return 'ajax_choice';
    }

    /**
     * @return string
     */
    public function getParent()
    {
        return 'text';
    }
}