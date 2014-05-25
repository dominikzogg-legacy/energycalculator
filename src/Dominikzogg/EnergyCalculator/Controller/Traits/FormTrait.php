<?php

namespace Dominikzogg\EnergyCalculator\Controller\Traits;

use Symfony\Component\Form\Form;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormFactory;

trait FormTrait
{
    /**
     * @var FormFactory
     */
    protected $formFactory;

    /**
     * @param  string               $type
     * @param  null                 $data
     * @param  array                $options
     * @param  FormBuilderInterface $parent
     * @return Form
     */
    protected function createForm($type = 'form', $data = null, array $options = array(), FormBuilderInterface $parent = null)
    {
        return $this->formFactory->createBuilder($type, $data, $options, $parent)->getForm();
    }
}