<?php

namespace Dominikzogg\EnergyCalculator\Controller;

use Dominikzogg\EnergyCalculator\Entity\Comestible;
use Dominikzogg\EnergyCalculator\Form\ComestibleListType;
use Dominikzogg\EnergyCalculator\Form\ComestibleType;
use Saxulum\RouteController\Annotation\DI;
use Saxulum\RouteController\Annotation\Route;
use Symfony\Component\Form\FormTypeInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * @Route("/{_locale}/comestible")
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
class ComestibleController extends AbstractCRUDController
{
    /**
     * @Route("/", bind="comestible_list", method="GET")
     * @param Request $request
     * @return Response
     */
    public function listAction(Request $request)
    {
        return parent::listObjects($request);
    }

    /**
     * @Route("/create", bind="comestible_create")
     * @param Request $request
     * @return Response|RedirectResponse
     */
    public function createAction(Request $request)
    {
        return parent::createObject($request);
    }

    /**
     * @Route("/edit/{id}", bind="comestible_edit", asserts={"id"="\d+"})
     * @param Request $request
     * @param $id
     * @return Response|RedirectResponse
     */
    public function editAction(Request $request, $id)
    {
        return parent::editObject($request, $id);
    }

    /**
     * @Route("/delete/{id}", bind="comestible_delete", asserts={"id"="\d+"}, method="GET")
     * @param Request $request
     * @param $id
     * @return RedirectResponse
     */
    public function deleteAction(Request $request, $id)
    {
        return parent::deleteObject($request, $id);
    }

    /**
     * @return int
     */
    protected function getPerPage()
    {
        return 20;
    }

    /**
     * @return FormTypeInterface|null
     */
    protected function getListFormType()
    {
        return new ComestibleListType();
    }

    /**
     * @return array
     */
    protected function getListDefaultData()
    {
        return array(
            'user' => $this->getUser()->getId()
        );
    }

    /**
     * @return Comestible
     */
    protected function getCreateObject()
    {
        $objectClass = $this->getObjectClass();

        /** @var Comestible $object */
        $object = new $objectClass;
        $object->setUser($this->getUser());

        return $object;
    }

    /**
     * @return FormTypeInterface
     */
    protected function getCreateFormType()
    {
        return new ComestibleType();
    }

    /**
     * @param Comestible $object
     * @return bool
     */
    protected function getEditIsGranted($object)
    {
        if(!$this->security->isGranted($this->getEditRole(), $object)) {
            return false;
        }

        if($object->getUser() != $this->getUser()) {
            return false;
        }

        return true;
    }

    /**
     * @return FormTypeInterface
     */
    protected function getEditFormType()
    {
        return new ComestibleType();
    }

    /**
     * @return string
     */
    protected function getViewRoute()
    {
        return '';
    }

    /**
     * @param Comestible $object
     * @return bool
     */
    protected function getDeleteIsGranted($object)
    {
        if(!$this->security->isGranted($this->getDeleteRole(), $object)) {
            return false;
        }

        if($object->getUser() != $this->getUser()) {
            return false;
        }

        return true;
    }

    /**
     * @return string
     */
    protected function getName()
    {
        return 'comestible';
    }

    /**
     * @return string
     */
    protected function getObjectClass()
    {
        return Comestible::class;
    }
}
