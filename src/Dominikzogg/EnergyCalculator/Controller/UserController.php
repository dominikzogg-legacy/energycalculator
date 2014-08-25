<?php

namespace Dominikzogg\EnergyCalculator\Controller;

use Saxulum\RouteController\Annotation\DI;
use Saxulum\RouteController\Annotation\Route;
use Saxulum\UserProvider\Manager\UserManager;
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
    protected $entityClass = 'Dominikzogg\\EnergyCalculator\\Entity\\User';
    protected $formTypeClass = 'Dominikzogg\\EnergyCalculator\\Form\\UserType';
    protected $listRoute = 'user_list';
    protected $editRoute = 'user_edit';
    protected $deleteRoute = 'user_delete';
    protected $listTemplate = '@DominikzoggEnergyCalculator/BaseCRUD/list.html.twig';
    protected $editTemplate = '@DominikzoggEnergyCalculator/BaseCRUD/edit.html.twig';
    protected $transPrefix = 'user';

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
        return parent::listEntities($request, array(), array('username' => 'ASC'), 20);
    }

    /**
     * @Route("/edit/{id}", bind="user_edit", values={"id"=null}, asserts={"id"="\d+"})
     * @param Request $request
     * @param $id
     * @return Response|RedirectResponse
     */
    public function editAction(Request $request, $id)
    {
        return self::editEntity($request, $id);
    }

    /**
     * @param Request $request
     * @param $id
     * @return Response|RedirectResponse
     * @throws NotFoundHttpException
     */
    protected function editEntity(Request $request, $id)
    {
        if (!is_null($id)) {
            $entity = $this->getRepositoryForClass($this->entityClass)->find($id);
            if (is_null($entity)) {
                throw new NotFoundHttpException("entity with id {$id} not found!");
            }
            if(!$this->security->isGranted('ROLE_ADMIN') &&
                $entity->getUser()->getId() !== $this->getUser()->getId()) {
                throw new AccessDeniedException("permission denied to edit entity with {$id}");
            }
        } else {
            $entity = new $this->entityClass;
        }

        $formType = new $this->formTypeClass($this->entityClass);

        if(method_exists($formType, 'setTranslator')) {
            $formType->setTranslator($this->translator);
        }

        $form = $this->createForm($formType, $entity);

        if ('POST' == $request->getMethod()) {
            $form->submit($request);
            if ($form->isValid()) {
                $em = $this->getManagerForClass($this->entityClass);

                $this->userManager->update($entity);

                $em->persist($entity);
                $em->flush();

                if($request->request->get('saveandclose', false)) {
                    return new RedirectResponse($this->urlGenerator->generate($this->listRoute, array(), true), 302);
                }

                if($request->request->get('saveandnew', false)) {
                    return new RedirectResponse($this->urlGenerator->generate($this->editRoute, array(), true), 302);
                }

                return new RedirectResponse($this->urlGenerator->generate($this->editRoute, array('id' => $entity->getId()), true), 302);
            }
        }

        return $this->render($this->editTemplate, array(
            'entity' => $entity,
            'form' => $form->createView(),
            'listroute' => $this->listRoute,
            'editroute' => $this->editRoute,
            'showroute' => $this->showRoute,
            'deleteroute' => $this->deleteRoute,
            'transprefix' => $this->transPrefix,
        ));
    }

    /**
     * @Route("/delete/{id}", bind="user_delete", asserts={"id"="\d+"}, method="GET")
     * @param $id
     * @return RedirectResponse
     */
    public function deleteAction($id)
    {
        return parent::deleteEntity($id);
    }
}