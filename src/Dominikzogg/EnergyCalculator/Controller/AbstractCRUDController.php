<?php

namespace Dominikzogg\EnergyCalculator\Controller;

use Doctrine\Common\Persistence\ManagerRegistry;
use Saxulum\Crud\Controller\CrudTrait;
use Saxulum\Crud\Listing\ListingFactory;
use Saxulum\Crud\Pagination\PaginatorInterface;
use Symfony\Component\Form\FormFactory;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

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
     * @var ListingFactory
     */
    protected $listingFactory;

    /**
     * @return AuthorizationCheckerInterface
     */
    protected function crudAuthorizationChecker()
    {
        return $this->authorizationChecker;
    }

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
     * @return ListingFactory
     */
    protected function crudListingFactory()
    {
        return $this->listingFactory;
    }

    /**
     * @return string
     */
    protected function crudTemplatePattern()
    {
        return '@DominikzoggEnergyCalculator/%s/%s.html.twig';
    }
}
