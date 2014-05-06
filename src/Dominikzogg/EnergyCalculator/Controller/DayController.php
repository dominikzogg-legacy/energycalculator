<?php

namespace Dominikzogg\EnergyCalculator\Controller;

use Saxulum\RouteController\Annotation\DI;
use Saxulum\RouteController\Annotation\Route;
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
    protected $entityClass = 'Dominikzogg\\EnergyCalculator\\Entity\\Day';
    protected $formTypeClass = 'Dominikzogg\\EnergyCalculator\\Form\\DayType';
    protected $listRoute = 'day_list';
    protected $editRoute = 'day_edit';
    protected $showRoute = 'day_show';
    protected $deleteRoute = 'day_delete';
    protected $listTemplate = '@DominikzoggEnergyCalculator/BaseCRUD/list.html.twig';
    protected $editTemplate = '@DominikzoggEnergyCalculator/BaseCRUD/edit.html.twig';
    protected $showTemplate = '@DominikzoggEnergyCalculator/Day/show.html.twig';
    protected $transPrefix = 'day';

    /**
     * @Route("/", bind="day_list", method="GET")
     * @param Request $request
     * @return Response
     */
    public function listAction(Request $request)
    {
        return parent::listEntities($request, array(), array('date' => 'DESC'), 7);
    }

    /**
     * @Route("/edit/{id}", bind="day_edit", values={"id"=null}, asserts={"id"="\d+"})
     * @param Request $request
     * @param $id
     * @return Response|RedirectResponse
     */
    public function editAction(Request $request, $id)
    {
        return parent::editEntity($request, $id);
    }

    /**
     * @Route("/show/{id}", bind="day_show", asserts={"id"="\d+"}, method="GET")
     * @param $id
     * @return Response
     */
    public function showAction($id)
    {
        return parent::showEntity($id);
    }

    /**
     * @Route("/delete/{id}", bind="day_delete", asserts={"id"="\d+"}, method="GET")
     * @param $id
     * @return RedirectResponse
     */
    public function deleteAction($id)
    {
        return parent::deleteEntity($id);
    }
}