<?php

namespace Dominikzogg\EnergyCalculator\Controller;

use Saxulum\RouteController\Annotation\DI;
use Saxulum\RouteController\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

/**
 * @Route("/{_locale}/comestible")
 * @DI(serviceIds={
 *      "doctrine",
 *      "form.factory",
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
     */
    public function listAction()
    {
        return parent::listAction(array(), array('name' => 'ASC'));
    }

    /**
     * @Route("/edit/{id}", bind="comestible_edit", values={"id"=null}, asserts={"id"="\d+"})
     * @param Request $request
     * @param $id
     * @return string|\Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function editAction(Request $request, $id)
    {
        return parent::editAction($request, $id);
    }

    /**
     * @Route("/delete/{id}", bind="comestible_delete", values={"id"=null}, asserts={"id"="\d+"}, method="GET")
     * @param $id
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function deleteAction($id)
    {
        return parent::deleteAction($id);
    }
}