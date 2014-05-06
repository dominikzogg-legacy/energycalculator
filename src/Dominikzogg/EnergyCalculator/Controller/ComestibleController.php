<?php

namespace Dominikzogg\EnergyCalculator\Controller;

use Saxulum\RouteController\Annotation\DI;
use Saxulum\RouteController\Annotation\Route;
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
    protected $entityClass = 'Dominikzogg\\EnergyCalculator\\Entity\\Comestible';
    protected $formTypeClass = 'Dominikzogg\\EnergyCalculator\\Form\\ComestibleType';
    protected $listRoute = 'comestible_list';
    protected $editRoute = 'comestible_edit';
    protected $deleteRoute = 'comestible_delete';
    protected $listTemplate = '@DominikzoggEnergyCalculator/BaseCRUD/list.html.twig';
    protected $editTemplate = '@DominikzoggEnergyCalculator/BaseCRUD/edit.html.twig';
    protected $transPrefix = 'comestible';

    /**
     * @Route("/", bind="comestible_list", method="GET")
     * @param Request $request
     * @return Response
     */
    public function listAction(Request $request)
    {
        return parent::listEntities($request, array(), array('name' => 'ASC'), 20);
    }

    /**
     * @Route("/edit/{id}", bind="comestible_edit", values={"id"=null}, asserts={"id"="\d+"})
     * @param Request $request
     * @param $id
     * @return Response|RedirectResponse
     */
    public function editAction(Request $request, $id)
    {
        return parent::editEntity($request, $id);
    }

    /**
     * @Route("/delete/{id}", bind="comestible_delete", asserts={"id"="\d+"}, method="GET")
     * @param $id
     * @return RedirectResponse
     */
    public function deleteAction($id)
    {
        return parent::deleteEntity($id);
    }
}