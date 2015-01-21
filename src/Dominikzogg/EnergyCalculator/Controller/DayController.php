<?php

namespace Dominikzogg\EnergyCalculator\Controller;

use Dominikzogg\EnergyCalculator\Entity\Day;
use Dominikzogg\EnergyCalculator\Form\DayListType;
use Dominikzogg\EnergyCalculator\Form\DayType;
use Saxulum\RouteController\Annotation\DI;
use Saxulum\RouteController\Annotation\Route;
use Symfony\Component\Form\FormTypeInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * @Route("/{_locale}/day")
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
class DayController extends AbstractCRUDController
{
    /**
     * @Route("/", bind="day_list", method="GET")
     * @param Request $request
     * @return Response
     */
    public function listAction(Request $request)
    {
        return parent::listObjects($request);
    }

    /**
     * @Route("/create", bind="day_create")
     * @param Request $request
     * @return Response|RedirectResponse
     */
    public function createAction(Request $request)
    {
        return parent::createObject($request);
    }

    /**
     * @Route("/edit/{id}", bind="day_edit", asserts={"id"="\d+"})
     * @param Request $request
     * @param $id
     * @return Response|RedirectResponse
     */
    public function editAction(Request $request, $id)
    {
        return parent::editObject($request, $id);
    }

    /**
     * @Route("/view/{id}", bind="day_view", asserts={"id"="\d+"}, method="GET")
     * @param Request $request
     * @param $id
     * @return Response
     */
    public function viewAction(Request $request, $id)
    {
        return parent::viewObject($request, $id);
    }

    /**
     * @Route("/delete/{id}", bind="day_delete", asserts={"id"="\d+"}, method="GET")
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
        return 7;
    }

    /**
     * @return FormTypeInterface|null
     */
    protected function getListFormType()
    {
        return new DayListType();
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
     * @return Day
     */
    protected function getCreateObject()
    {
        $objectClass = $this->getObjectClass();

        /** @var Day $object */
        $object = new $objectClass;
        $object->setUser($this->getUser());

        return $object;
    }

    /**
     * @return FormTypeInterface
     */
    protected function getCreateFormType()
    {
        return new DayType($this->getUser(), $this->translator);
    }

    /**
     * @param Day $object
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
        return new DayType($this->getUser(), $this->translator);
    }

    /**
     * @param Day $object
     * @return bool
     */
    protected function getViewIsGranted($object)
    {
        if(!$this->security->isGranted($this->getViewRole(), $object)) {
            return false;
        }

        if($object->getUser() != $this->getUser()) {
            return false;
        }

        return true;
    }

    /**
     * @param Day $object
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
        return 'day';
    }

    /**
     * @return string
     */
    protected function getObjectClass()
    {
        return Day::class;
    }
}