<?php

namespace Dominikzogg\EnergyCalculator\Form;

use Saxulum\UserProvider\Form\AbstractUserType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class UserType extends AbstractUserType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);

        $builder->remove('enabled');
        $builder->add('enabled', 'checkbox', array(
            'required' => false,
            'attr' => array(
                'align_with_widget' => true,
            ),
        ));
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        parent::setDefaultOptions($resolver);
        $resolver->setDefaults(array(
            'translation_domain' => 'messages',
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'user_edit';
    }
}
