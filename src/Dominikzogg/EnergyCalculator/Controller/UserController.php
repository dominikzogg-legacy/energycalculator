<?php

namespace Dominikzogg\EnergyCalculator\Controller;

use Doctrine\Common\Persistence\ManagerRegistry;
use Dominikzogg\EnergyCalculator\Entity\User;
use Dominikzogg\EnergyCalculator\Form\UserType;
use Knp\Component\Pager\Paginator;
use Saxulum\RouteController\Annotation\DI;
use Saxulum\RouteController\Annotation\Route;
use Saxulum\UserProvider\Manager\UserManager;
use Symfony\Component\Form\FormFactory;
use Symfony\Component\Form\FormTypeInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\SecurityContextInterface;

/**
 * @Route("/{_locale}/user")
 * @DI(serviceIds={
 *      "form.factory",
 *      "doctrine",
 *      "knp_paginator",
 *      "twig",
 *      "url_generator",
 *      "security",
 *      "saxulum.userprovider.manager"
 * })
 */
class UserController extends AbstractCRUDController
{
    /**
     * @var UserManager
     */
    protected $userManager;

    /**
     * @param FormFactory $formFactory
     * @param ManagerRegistry $doctrine
     * @param Paginator $paginator
     * @param \Twig_Environment $twig
     * @param UrlGeneratorInterface $urlGenerator
     * @param SecurityContextInterface $security
     */
    public function __construct(
        FormFactory $formFactory,
        ManagerRegistry $doctrine,
        Paginator $paginator,
        \Twig_Environment $twig,
        UrlGeneratorInterface $urlGenerator,
        SecurityContextInterface $security,
        UserManager $userManager
    ) {
        $this->formFactory = $formFactory;
        $this->doctrine = $doctrine;
        $this->paginator = $paginator;
        $this->twig = $twig;
        $this->urlGenerator = $urlGenerator;
        $this->security = $security;
        $this->userManager = $userManager;
    }

    /**
     * @Route("/", bind="user_list", method="GET")
     * @param Request $request
     * @return Response
     */
    public function listAction(Request $request)
    {
        return parent::crudListObjects($request);
    }

    /**
     * @Route("/create", bind="user_create")
     * @param Request $request
     * @return Response|RedirectResponse
     */
    public function createAction(Request $request)
    {
        return self::crudCreateObject($request);
    }

    /**
     * @Route("/edit/{id}", bind="user_edit", asserts={"id"="\d+"})
     * @param Request $request
     * @param $id
     * @return Response|RedirectResponse
     */
    public function editAction(Request $request, $id)
    {
        return self::crudEditObject($request, $id);
    }

    /**
     * @Route("/view/{id}", bind="user_view", asserts={"id"="\d+"})
     * @param Request $request
     * @param $id
     * @return Response|RedirectResponse
     */
    public function viewAction(Request $request, $id)
    {
        return self::crudViewObject($request, $id);
    }

    /**
     * @param User $object
     * @return void
     */
    protected function crudPrePersist($object)
    {
        $this->userManager->update($object);
    }

    /**
     * @param User $object
     * @return void
     */
    protected function crudPreUpdate($object)
    {
        $this->userManager->update($object);
    }

    /**
     * @Route("/delete/{id}", bind="user_delete", asserts={"id"="\d+"}, method="GET")
     * @param Request $request
     * @param $id
     * @return RedirectResponse
     */
    public function deleteAction(Request $request, $id)
    {
        return parent::crudDeleteObject($request, $id);
    }

    /**
     * @return FormTypeInterface
     */
    protected function crudCreateFormType()
    {
        return new UserType($this->crudObjectClass());
    }

    /**
     * @return FormTypeInterface
     */
    protected function crudEditFormType()
    {
        return new UserType($this->crudObjectClass());
    }

    /**
     * @return string
     */
    protected function crudName()
    {
        return 'user';
    }

    /**
     * @return string
     */
    protected function crudObjectClass()
    {
        return User::class;
    }
}
