<?php

namespace Dominikzogg\EnergyCalculator\Controller;

use Doctrine\ORM\EntityRepository;
use Knp\Component\Pager\Paginator;
use Saxulum\RouteController\Annotation\DI;
use Saxulum\RouteController\Annotation\Route;
use Saxulum\UserProvider\Controller\AbstractUserController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

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
    /**
     * @var Paginator
     */
    protected $paginator;

    /**
     * @DI(serviceIds={"knp_paginator"})
     * @param Paginator $paginator
     */
    public function setPaginator(Paginator $paginator)
    {
        $this->paginator = $paginator;
    }

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
        if (!$this->security->isGranted('ROLE_ADMIN')) {
            throw new AccessDeniedException("permission denied to show users");
        }

        /** @var EntityRepository $repo */
        $repo = $this
            ->doctrine
            ->getManagerForClass($this->entityClass)
            ->getRepository($this->entityClass)
        ;

        $qb = $repo->createQueryBuilder('e');
        $qb->addOrderBy("e.username", 'ASC');

        $entities = $this->paginator->paginate($qb, $request->query->get('page', 1), 20);

        return $this->render($this->listTemplate, array(
            'entities' => $entities,
            'listroute' => $this->listRoute,
            'editroute' => $this->editRoute,
            'showroute' => $this->showRoute,
            'deleteroute' => $this->deleteRoute,
            'transprefix' => $this->transPrefix,
        ));
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