<?php

namespace Dominikzogg\EnergyCalculator\Controller;

use Saxulum\RouteController\Annotation\DI;
use Saxulum\RouteController\Annotation\Route;
use Saxulum\UserProvider\Controller\AbstractUserController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * @Route("/{_locale}/admin/user")
 * @DI(serviceIds={
 *      "doctrine",
 *      "form.factory",
 *      "security.encoder.digest",
 *      "security",
 *      "translator",
 *      "twig",
 *      "url_generator"
 * })
 */
class UserController extends AbstractUserController
{
    protected $entityClass = 'Dominikzogg\\EnergyCalculator\\Entity\\User';
    protected $formTypeClass = 'Dominikzogg\\EnergyCalculator\\Form\\UserType';
    protected $listRoute = 'user_list';
    protected $editRoute = 'user_edit';
    protected $showRoute = 'user_show';
    protected $deleteRoute = 'user_delete';
    protected $listTemplate = '@DominikzoggEnergyCalculator/BaseCRUD/list.html.twig';
    protected $editTemplate = '@DominikzoggEnergyCalculator/BaseCRUD/edit.html.twig';
    protected $showTemplate = '@DominikzoggEnergyCalculator/User/show.html.twig';
    protected $transPrefix = 'user';

    /**
     * @Route("/", bind="user_list", method="GET")
     * @param Request $request
     * @return Response
     */
    public function listAction(Request $request)
    {
        return parent::listAction($request);
    }

    /**
     * @Route("/show/{id}", bind="user_show", asserts={"id"="\d+"}, method="GET")
     * @param Request $request
     * @param int $id
     * @return Response
     * @throws NotFoundHttpException
     */
    public function showAction(Request $request, $id)
    {
        return parent::showAction($request, $id);
    }

    /**
     * @Route("/edit/{id}", bind="user_edit", values={"id"=null}, asserts={"id"="\d+"})
     * @param Request $request
     * @param $id
     * @return Response|RedirectResponse
     * @throws NotFoundHttpException
     */
    public function editAction(Request $request, $id)
    {
        return parent::editAction($request, $id);
    }

    /**
     * @Route("/delete/{id}", bind="user_delete", asserts={"id"="\d+"}, method="GET")
     * @param Request $request
     * @param int $id
     * @return RedirectResponse
     * @throws \ErrorException
     * @throws NotFoundHttpException
     */
    public function deleteAction(Request $request, $id)
    {
        return parent::deleteAction($request, $id);
    }
}