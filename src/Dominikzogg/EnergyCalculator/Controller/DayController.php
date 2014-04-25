<?php

namespace Dominikzogg\EnergyCalculator\Controller;

use Dominikzogg\EnergyCalculator\Entity\Day;
use Saxulum\RouteController\Annotation\DI;
use Saxulum\RouteController\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * @Route("/{_locale}/day")
 * @DI(serviceIds={
 *      "doctrine",
 *      "form.factory",
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
     */
    public function listAction()
    {
        return parent::listAction();
    }

    /**
     * @Route("/edit/{id}", bind="day_edit", values={"id"=null}, asserts={"id"="\d+"})
     * @param Request $request
     * @param $id
     * @return string|\Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function editAction(Request $request, $id)
    {
        return parent::editAction($request, $id);
    }

    /**
     * @Route("/show/{id}", bind="day_show", values={"id"=null}, asserts={"id"="\d+"}, method="GET")
     * @param $id
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function showAction($id)
    {
        return parent::showAction($id);
    }

    /**
     * @Route("/delete/{id}", bind="day_delete", values={"id"=null}, asserts={"id"="\d+"}, method="GET")
     * @param $id
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function deleteAction($id)
    {
        return parent::deleteAction($id);
    }
}