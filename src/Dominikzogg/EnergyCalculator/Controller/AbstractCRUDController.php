<?php

namespace Dominikzogg\EnergyCalculator\Controller;

use Doctrine\Common\Persistence\ManagerRegistry;
use Dominikzogg\EnergyCalculator\Entity\User;
use Dominikzogg\EnergyCalculator\Entity\UserReferenceInterface;
use Symfony\Component\Form\Form;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormFactory;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Generator\UrlGenerator;
use Symfony\Component\Security\Core\SecurityContext;
use Symfony\Component\Translation\Translator;

abstract class AbstractCRUDController
{
    protected $entityClass;
    protected $formTypeClass;
    protected $listRoute;
    protected $editRoute;
    protected $deleteRoute;
    protected $listTemplate;
    protected $editTemplate;
    protected $transPrefix;

    /**
     * @var ManagerRegistry
     */
    protected $doctrine;

    /**
     * @var FormFactory
     */
    protected $formFactory;

    /**
     * @var SecurityContext
     */
    protected $security;

    /**
     * @var Translator
     */
    protected $translator;

    /**
     * @var \Twig_Environment
     */
    protected $twig;

    /**
     * @var UrlGenerator
     */
    protected $urlGenerator;

    /**
     * @param ManagerRegistry $doctrine
     * @param FormFactory $formFactory
     * @param SecurityContext $security
     * @param Translator $translator
     * @param \Twig_Environment $twig
     * @param UrlGenerator $urlGenerator
     */
    public function __construct(
        ManagerRegistry $doctrine,
        FormFactory $formFactory,
        SecurityContext $security,
        Translator $translator,
        \Twig_Environment $twig,
        UrlGenerator $urlGenerator
    ) {
        $this->doctrine = $doctrine;
        $this->formFactory = $formFactory;
        $this->security = $security;
        $this->translator = $translator;
        $this->twig = $twig;
        $this->urlGenerator = $urlGenerator;
    }

    /**
     * @param  string               $type
     * @param  null                 $data
     * @param  array                $options
     * @param  FormBuilderInterface $parent
     * @return Form
     */
    protected function createForm($type = 'form', $data = null, array $options = array(), FormBuilderInterface $parent = null)
    {
        return $this->formFactory->createBuilder($type, $data, $options, $parent)->getForm();
    }

    /**
     * @param $view
     * @param  array  $parameters
     * @return string
     */
    protected function renderView($view, array $parameters = array())
    {
        return $this->twig->render($view, $parameters);
    }

    /**
     * @return string
     */
    protected function listAction()
    {
        $entity = new $this->entityClass;

        if($entity instanceof UserReferenceInterface) {
            $entities = $this->doctrine->getManager()->getRepository($this->entityClass)->findBy(array(
                'user' => $this->getUser()->getId()
            ));
        } else {
            $entities = $this->doctrine->getManager()->getRepository($this->entityClass)->findAll();
        }

        return $this->renderView($this->listTemplate, array(
            'entities' => $entities,
            'editroute' => $this->editRoute,
            'listroute' => $this->listRoute,
            'deleteroute' => $this->deleteRoute,
            'transprefix' => $this->transPrefix,
        ));
    }

    /**
     * @param Request $request
     * @param $id
     * @return string|RedirectResponse
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     */
    protected function editAction(Request $request, $id)
    {
        if (!is_null($id)) {
            $entity = $this->doctrine->getManager()->getRepository($this->entityClass)->find($id);
            if (is_null($entity)) {
                throw new NotFoundHttpException("entity with id {$id} not found!");
            }
        } else {
            $entity = new $this->entityClass;
            if($entity instanceof UserReferenceInterface) {
                $entity->setUser($this->getUser());
            }
        }

        $formType = new $this->formTypeClass;
        if(method_exists($formType, 'setUser')) {
            $formType->setUser($this->getUser());
        }
        if(method_exists($formType, 'setTranslator')) {
            $formType->setTranslator($this->translator);
        }

        $form = $this->createForm($formType, $entity);

        if ('POST' == $request->getMethod()) {
            $form->submit($request);
            if ($form->isValid()) {
                $this->doctrine->getManager()->persist($entity);
                $this->doctrine->getManager()->flush();

                if($request->request->get('saveandclose', false)) {
                    return new RedirectResponse($this->urlGenerator->generate($this->listRoute, array(), true), 302);
                }

                if($request->request->get('saveandnew', false)) {
                    return new RedirectResponse($this->urlGenerator->generate($this->editRoute, array(), true), 302);
                }

                return new RedirectResponse($this->urlGenerator->generate($this->editRoute, array('id' => $entity->getId()), true), 302);
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
     * @param $id
     * @return RedirectResponse
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     */
    protected function deleteAction($id)
    {
        $entity = $this->doctrine->getManager()->getRepository($this->entityClass)->find($id);

        if (is_null($entity)) {
            throw new NotFoundHttpException("entity with id {$id} not found!");
        }

        $this->doctrine->getManager()->remove($entity);
        $this->doctrine->getManager()->flush();

        return new RedirectResponse($this->urlGenerator->generate($this->listRoute), 302);
    }

    /**
     * @return User|Null|string
     */
    protected function getUser()
    {
        if (is_null($this->security->getToken())) {
            return null;
        }

        $user = $this->security->getToken()->getUser();

        if ($user instanceof User) {
            $user = $this->doctrine->getManager()->getRepository(get_class($user))->find($user->getId());
        }

        return $user;
    }
}
