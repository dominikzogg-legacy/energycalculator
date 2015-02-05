<?php

namespace Dominikzogg\EnergyCalculator\Controller;

use Doctrine\Common\Persistence\ManagerRegistry;
use Dominikzogg\EnergyCalculator\Entity\User;
use Saxulum\Crud\Controller\CrudTrait;
use Saxulum\Crud\Pagination\PaginatorInterface;
use Symfony\Component\Form\FormFactory;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\SecurityContextInterface;

abstract class AbstractCRUDController
{
    use CrudTrait;

    /**
     * @var FormFactory
     */
    protected $formFactory;

    /**
     * @var ManagerRegistry
     */
    protected $doctrine;

    /**
     * @var PaginatorInterface
     */
    protected $paginator;

    /**
     * @var \Twig_Environment
     */
    protected $twig;

    /**
     * @var UrlGeneratorInterface
     */
    protected $urlGenerator;

    /**
     * @var SecurityContextInterface
     */
    protected $security;

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
     * @return User|Null|string
     */
    protected function getUser()
    {
        if (is_null($this->security->getToken())) {
            return null;
        }

        $user = $this->security->getToken()->getUser();

        if ($user instanceof User) {
            $user = $this->doctrine->getManager()->getRepository(get_class($user))->find($user->getId());
        }

        return $user;
    }

    /**
     * @return string
     */
    protected function crudTemplatePattern()
    {
        return '@DominikzoggEnergyCalculator/%s/%s.html.twig';
    }
}
