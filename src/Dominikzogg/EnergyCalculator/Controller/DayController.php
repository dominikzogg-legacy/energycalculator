<?php

namespace Dominikzogg\EnergyCalculator\Controller;

use Doctrine\Common\Persistence\ManagerRegistry;
use Dominikzogg\EnergyCalculator\Entity\Day;
use Dominikzogg\EnergyCalculator\Form\DayListType;
use Dominikzogg\EnergyCalculator\Form\DayType;
use Knp\Component\Pager\Paginator;
use Saxulum\RouteController\Annotation\DI;
use Saxulum\RouteController\Annotation\Route;
use Symfony\Component\Form\FormFactory;
use Symfony\Component\Form\FormTypeInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\SecurityContextInterface;
use Symfony\Component\Translation\TranslatorInterface;

/**
 * @Route("/{_locale}/day")
 * @DI(serviceIds={
 *      "form.factory",
 *      "doctrine",
 *      "knp_paginator",
 *      "twig",
 *      "url_generator",
 *      "security",
 *      "translator"
 * })
 */
class DayController extends AbstractCRUDController
{
    /**
     * @var TranslatorInterface
     */
    protected $translator;

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
        TranslatorInterface $translator
    ) {
        $this->formFactory = $formFactory;
        $this->doctrine = $doctrine;
        $this->paginator = $paginator;
        $this->twig = $twig;
        $this->urlGenerator = $urlGenerator;
        $this->security = $security;
        $this->translator = $translator;
    }

    /**
     * @Route("/", bind="day_list", method="GET")
     * @param Request $request
     * @return Response
     */
    public function listAction(Request $request)
    {
        return parent::crudListObjects($request);
    }

    /**
     * @Route("/create", bind="day_create")
     * @param Request $request
     * @return Response|RedirectResponse
     */
    public function createAction(Request $request)
    {
        return parent::crudCreateObject($request);
    }

    /**
     * @Route("/edit/{id}", bind="day_edit", asserts={"id"="\d+"})
     * @param Request $request
     * @param $id
     * @return Response|RedirectResponse
     */
    public function editAction(Request $request, $id)
    {
        return parent::crudEditObject($request, $id);
    }

    /**
     * @Route("/view/{id}", bind="day_view", asserts={"id"="\d+"}, method="GET")
     * @param Request $request
     * @param $id
     * @return Response
     */
    public function viewAction(Request $request, $id)
    {
        return parent::crudViewObject($request, $id);
    }

    /**
     * @Route("/delete/{id}", bind="day_delete", asserts={"id"="\d+"}, method="GET")
     * @param Request $request
     * @param $id
     * @return RedirectResponse
     */
    public function deleteAction(Request $request, $id)
    {
        return parent::crudDeleteObject($request, $id);
    }

    /**
     * @return int
     */
    protected function crudPaginatePerPage()
    {
        return 7;
    }

    /**
     * @return FormTypeInterface|null
     */
    protected function crudListFormType()
    {
        return new DayListType();
    }

    /**
     * @return array
     */
    protected function crudListDefaultData()
    {
        return array(
            'user' => $this->getUser()->getId()
        );
    }

    /**
     * @return Day
     */
    protected function crudCreateFactory()
    {
        $objectClass = $this->crudObjectClass();

        /** @var Day $object */
        $object = new $objectClass;
        $object->setUser($this->getUser());

        return $object;
    }

    /**
     * @return FormTypeInterface
     */
    protected function crudCreateFormType()
    {
        return new DayType($this->getUser(), $this->translator);
    }

    /**
     * @param Day $object
     * @return bool
     */
    protected function crudEditIsGranted($object)
    {
        if (!$this->security->isGranted($this->crudEditRole(), $object)) {
            return false;
        }

        if ($object->getUser() != $this->getUser()) {
            return false;
        }

        return true;
    }

    /**
     * @return FormTypeInterface
     */
    protected function crudEditFormType()
    {
        return new DayType($this->getUser(), $this->translator);
    }

    /**
     * @param Day $object
     * @return bool
     */
    protected function crudViewIsGranted($object)
    {
        if (!$this->security->isGranted($this->crudViewRole(), $object)) {
            return false;
        }

        if ($object->getUser() != $this->getUser()) {
            return false;
        }

        return true;
    }

    /**
     * @param Day $object
     * @return bool
     */
    protected function crudDeleteIsGranted($object)
    {
        if (!$this->security->isGranted($this->crudDeleteRole(), $object)) {
            return false;
        }

        if ($object->getUser() != $this->getUser()) {
            return false;
        }

        return true;
    }

    /**
     * @return string
     */
    protected function crudName()
    {
        return 'day';
    }

    /**
     * @return string
     */
    protected function crudObjectClass()
    {
        return Day::class;
    }
}
