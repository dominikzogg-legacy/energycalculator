<?php

namespace Dominikzogg\EnergyCalculator\Controller;

use Dominikzogg\EnergyCalculator\Entity\User;
use Saxulum\RouteController\Annotation\DI;
use Saxulum\RouteController\Annotation\Route;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Encoder\PasswordEncoderInterface;

/**
 * @Route("/{_locale}/admin/user")
 * @DI(serviceIds={
 *      "doctrine",
 *      "form.factory",
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
     * @var PasswordEncoderInterface
     */
    protected $passwordEncoder;

    /**
     * @DI(serviceIds={"security.encoder.digest"})
     * @param PasswordEncoderInterface $passwordEncoder
     */
    public function setEncoderDigest(PasswordEncoderInterface $passwordEncoder)
    {
        $this->passwordEncoder = $passwordEncoder;
    }

    /**
     * @Route("/", bind="user_list", method="GET")
     */
    public function listAction()
    {
        return parent::listAction();
    }

    /**
     * @Route("/edit/{id}", bind="user_edit", values={"id"=null}, asserts={"id"="\d+"})
     * @param Request $request
     * @param $id
     * @return string|RedirectResponse
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     */
    public function editAction(Request $request, $id)
    {
        if (!is_null($id)) {
            // get user
            $entity = $this->doctrine->getManager()->getRepository($this->entityClass)->find($id);
            /** @var User $entity */

            if (is_null($entity)) {
                throw new NotFoundHttpException("user with id {$id} not found!");
            }
        } else {
            $entity = new $this->entityClass;
            /** @var User $entity */
            $entity->setSalt(uniqid(mt_rand()));
        }

        // create user form
        $form = $this->createForm(new $this->formTypeClass, $entity);

        if ('POST' == $request->getMethod()) {
            // submit request
            $form->submit($request);

            // check if the input is valid
            if ($form->isValid()) {
                if ($entity->updatePassword($this->passwordEncoder)) {
                    // you can't remove admin role on yourself
                    if ($entity->getId() == $this->getUser()->getId()) {
                        $entity->addRole(User::ROLE_ADMIN);
                    }

                    $this->doctrine->getManager()->persist($entity);
                    $this->doctrine->getManager()->flush();

                    if($request->request->get('saveandclose', false)) {
                        return new RedirectResponse($this->urlGenerator->generate($this->listRoute, array(), true), 302);
                    }

                    if($request->request->get('saveandnew', false)) {
                        return new RedirectResponse($this->urlGenerator->generate($this->editRoute, array(), true), 302);
                    }

                    return new RedirectResponse($this->urlGenerator->generate($this->editRoute, array('id' => $entity->getId())), 302);
                } else {
                    $form->addError(new FormError($this->translator->trans("No password set", array(), "frontend")));
                }
            }
        }

        return $this->renderView($this->editTemplate, array(
            'entity' => $entity,
            'form' => $form->createView(),
            'editroute' => $this->editRoute,
            'listroute' => $this->listRoute,
            'transprefix' => $this->transPrefix,
        ));
    }

    /**
     * @Route("/delete/{id}", bind="user_delete", values={"id"=null}, asserts={"id"="\d+"}, method="GET")
     * @param $id
     * @return RedirectResponse
     * @throws \ErrorException
     * @throws NotFoundHttpException
     */
    public function deleteAction($id)
    {
        // get the user
        $entity = $this->doctrine->getManager()->getRepository($this->entityClass)->find($id);
        /** @var User $entity */

        // check if user exists
        if (is_null($entity)) {
            throw new NotFoundHttpException("User with id {$id} not found!");
        }

        // check the user doesn't delete himself
        if ($entity->getId() == $this->getUser()->getId()) {
            throw new \ErrorException("You can't delete yourself!");
        }

        // remove the user
        $this->doctrine->getManager()->remove($entity);
        $this->doctrine->getManager()->flush();

        // redirect to the list
        return new RedirectResponse($this->urlGenerator->generate($this->listRoute), 302);
    }
}