<?php

namespace Dominikzogg\EnergyCalculator\Form;

use Doctrine\ORM\EntityRepository;
use Dominikzogg\EnergyCalculator\Entity\Comestible;
use Dominikzogg\EnergyCalculator\Entity\ComestibleWithinDay;
use Dominikzogg\EnergyCalculator\Entity\User;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class ComestibleWithinDayType extends AbstractType
{
    /**
     * @var User
     */
    protected $user;

    /**
     * @param User $user
     */
    public function __construct(User $user)
    {
        $this->user = $user;
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $user = $this->user;

        $builder
            ->add('comestible', 'entity', array(
                'class' => get_class(new Comestible()),
                'property' => 'name',
                'query_builder' => function(EntityRepository $er) use ($user) {
                    $qb = $er->createQueryBuilder('c');
                    $qb->where('c.user = :user');
                    $qb->setParameter('user', $user->getId());
                    $qb->orderBy('c.name');

                    return $qb;
                },
                'attr' => array(
                    'data-live-search' => true
                )
            ))
            ->add('amount', 'number')
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
