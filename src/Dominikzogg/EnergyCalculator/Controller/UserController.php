<?php

namespace Dominikzogg\EnergyCalculator\Controller;

use Dominikzogg\EnergyCalculator\Entity\User;
use Dominikzogg\EnergyCalculator\Form\UserType;
use Saxulum\RouteController\Annotation\DI;
use Saxulum\RouteController\Annotation\Route;
use Saxulum\UserProvider\Manager\UserManager;
use Symfony\Component\Form\FormTypeInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

/**
 * @Route("/{_locale}/user")
 * @DI(serviceIds={
 *      "doctrine",
 *      "form.factory",
 *      "knp_paginator",
 *      "security",
 *      "translator",
 *      "twig",
 *      "url_generator"
 * })
 */
class UserController extends AbstractCRUDController
{
    /**
     * @var UserManager
     */
    protected $userManager;

    /**
     * @DI(serviceIds={"saxulum.userprovider.manager"})
     * @param UserManager $userManager
     */
    public function setUserManager(UserManager $userManager)
    {
        $this->userManager = $userManager;
    }

    /**
     * @Route("/", bind="user_list", method="GET")
     * @param Request $request
     * @return Response
     */
    public function listAction(Request $request)
    {
        return parent::listObjects($request);
    }

    /**
     * @Route("/create", bind="user_create")
     * @param Request $request
     * @return Response|RedirectResponse
     */
    public function createAction(Request $request)
    {
        return self::createObject($request);
    }

    /**
     * @Route("/edit/{id}", bind="user_edit", asserts={"id"="\d+"})
     * @param Request $request
     * @param $id
     * @return Response|RedirectResponse
     */
    public function editAction(Request $request, $id)
    {
        return self::editObject($request, $id);
    }

    /**
     * @param User $object
     * @return void
     */
    protected function prePersist($object)
    {
        $this->userManager->update($object);
    }

    /**
     * @param User $object
     * @return void
     */
    protected function preUpdate($object)
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
        return parent::deleteObject($request, $id);
    }

    /**
     * @return FormTypeInterface
     */
    protected function getCreateFormType()
    {
        return new UserType($this->getObjectClass());
    }

    /**
     * @return FormTypeInterface
     */
    protected function getEditFormType()
    {
        return new UserType($this->getObjectClass());
    }

    /**
     * @return string
     */
    protected function getName()
    {
        return 'user';
    }

    /**
     * @return string
     */
    protected function getObjectClass()
    {
        return User::class;
    }
}