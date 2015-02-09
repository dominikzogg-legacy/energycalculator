<?php

namespace Dominikzogg\EnergyCalculator\Controller;

use Doctrine\Common\Persistence\ManagerRegistry;
use Saxulum\Crud\Controller\CrudTrait;
use Saxulum\Crud\Pagination\PaginatorInterface;
use Symfony\Component\Form\FormFactory;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\SecurityContextInterface;

abstract class AbstractCRUDController extends AbstractController
{
    use CrudTrait;

    /**
     * @var PaginatorInterface
     */
    protected $paginator;

    /**
     * @var UrlGeneratorInterface
     */
    protected $urlGenerator;

    /**
     * @return FormFactory
     */
    protected function crudFormFactory()
    {
        return $this->formFactory;
    }

    /**
     * @return ManagerRegistry
     */
    protected function crudDoctrine()
    {
        return $this->doctrine;
    }

    /**
     * @return PaginatorInterface
     */
    protected function crudPaginator()
    {
        return $this->paginator;
    }

    /**
     * @return \Twig_Environment
     */
    protected function crudTwig()
    {
        return $this->twig;
    }

    /**
     * @return UrlGeneratorInterface
     */
    protected function crudUrlGenerator()
    {
        return $this->urlGenerator;
    }

    /**
     * @return SecurityContextInterface
     */
    protected function crudSecurity()
    {
        return $this->security;
    }

    /**
     * @return string
     */
    protected function crudTemplatePattern()
    {
        return '@DominikzoggEnergyCalculator/%s/%s.html.twig';
    }
}
