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
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormTypeInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\SecurityContextInterface;

/**
 * @Route("/{_locale}/user")
 * @DI(serviceIds={
 *      "security",
 *      "doctrine",
 *      "form.factory",
 *      "knp_paginator",
 *      "url_generator",
 *      "twig",
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
     * @param SecurityContextInterface $security
     * @param ManagerRegistry          $doctrine
     * @param FormFactory              $formFactory
     * @param Paginator                $paginator
     * @param UrlGeneratorInterface    $urlGenerator
     * @param \Twig_Environment        $twig
     * @param UserManager              $userManager
     */
    public function __construct(
        SecurityContextInterface $security,
        ManagerRegistry $doctrine,
        FormFactory $formFactory,
        Paginator $paginator,
        UrlGeneratorInterface $urlGenerator,
        \Twig_Environment $twig,
        UserManager $userManager
    ) {
        $this->security = $security;
        $this->doctrine = $doctrine;
        $this->formFactory = $formFactory;
        $this->paginator = $paginator;
        $this->urlGenerator = $urlGenerator;
        $this->twig = $twig;
        $this->userManager = $userManager;
    }

    /**
     * @Route("/", bind="user_list", method="GET")
     * @param  Request  $request
     * @return Response
     */
    public function listAction(Request $request)
    {
        return parent::crudListObjects($request);
    }

    /**
     * @Route("/create", bind="user_create")
     * @param  Request                   $request
     * @return Response|RedirectResponse
     */
    public function createAction(Request $request)
    {
        return self::crudCreateObject($request);
    }

    /**
     * @Route("/edit/{id}", bind="user_edit", asserts={"id"="\d+"})
     * @param  Request                   $request
     * @param $id
     * @return Response|RedirectResponse
     */
    public function editAction(Request $request, $id)
    {
        return self::crudEditObject($request, $id);
    }

    /**
     * @Route("/view/{id}", bind="user_view", asserts={"id"="\d+"})
     * @param  Request                   $request
     * @param $id
     * @return Response|RedirectResponse
     */
    public function viewAction(Request $request, $id)
    {
        return self::crudViewObject($request, $id);
    }

    /**
     * @Route("/delete/{id}", bind="user_delete", asserts={"id"="\d+"}, method="GET")
     * @param  Request          $request
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
     * @param  User          $object
     * @param  FormInterface $form
     * @param  Request       $request
     * @return void
     */
    protected function crudCreatePrePersist($object, FormInterface $form, Request $request)
    {
        $this->userManager->update($object);
    }

    /**
     * @return FormTypeInterface
     */
    protected function crudEditFormType()
    {
        return new UserType($this->crudObjectClass());
    }

    /**
     * @param  User          $object
     * @param  FormInterface $form
     * @param  Request       $request
     * @return void
     */
    protected function crudEditPrePersist($object, FormInterface $form, Request $request)
    {
        $this->userManager->update($object);
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
