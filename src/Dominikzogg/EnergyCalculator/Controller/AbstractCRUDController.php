<?php

namespace Dominikzogg\EnergyCalculator\Controller;

use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\Common\Persistence\ObjectRepository;
use Dominikzogg\EnergyCalculator\Controller\Traits\FormTrait;
use Dominikzogg\EnergyCalculator\Controller\Traits\DoctrineTrait;
use Dominikzogg\EnergyCalculator\Controller\Traits\SecurityTrait;
use Dominikzogg\EnergyCalculator\Controller\Traits\TwigTrait;
use Dominikzogg\EnergyCalculator\Repository\QueryBuilderForFilterFormInterface;
use Knp\Component\Pager\Paginator;
use Saxulum\PaginationProvider\Pagination\SlidingPagination;
use Symfony\Component\Form\FormFactory;
use Symfony\Component\Form\FormTypeInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Generator\UrlGenerator;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Security\Core\SecurityContext;
use Symfony\Component\Translation\Translator;

abstract class AbstractCRUDController
{
    use DoctrineTrait;
    use FormTrait;
    use SecurityTrait;
    use TwigTrait;

    const TWIG_NAMESPACE = '@DominikzoggEnergyCalculator';

    /**
     * @var Paginator
     */
    protected $paginator;

    /**
     * @var Translator
     */
    protected $translator;

    /**
     * @var UrlGenerator
     */
    protected $urlGenerator;

    /**
     * @param ManagerRegistry $doctrine
     * @param FormFactory $formFactory
     * @param Paginator $paginator
     * @param SecurityContext $security
     * @param Translator $translator
     * @param \Twig_Environment $twig
     * @param UrlGenerator $urlGenerator
     */
    public function __construct(
        ManagerRegistry $doctrine,
        FormFactory $formFactory,
        Paginator $paginator,
        SecurityContext $security,
        Translator $translator,
        \Twig_Environment $twig,
        UrlGenerator $urlGenerator
    ) {
        $this->doctrine = $doctrine;
        $this->formFactory = $formFactory;
        $this->paginator = $paginator;
        $this->security = $security;
        $this->translator = $translator;
        $this->twig = $twig;
        $this->urlGenerator = $urlGenerator;
    }

    /**
     * @param Request $request
     * @param array $templateVars
     * @return Response
     */
    public function listObjects(Request $request, array $templateVars = array())
    {
        if(!$this->getListIsGranted()) {
            throw new AccessDeniedException("You need the permission to list entities!");
        }

        if(null !== $formType = $this->getListFormType()) {
            $form = $this->createForm($formType);
            $form->handleRequest($request);
            $formData = $form->getData();
        }

        if(!isset($formData) || null === $formData) {
            $formData = array();
        }

        $formData = array_replace_recursive($formData, $this->getListDefaultData());

        /** @var QueryBuilderForFilterFormInterface $repo */
        $repo = $this->getRepositoryForClass($this->getObjectClass());
        $qb = $repo->getQueryBuilderForFilterForm($formData);

        /** @var SlidingPagination $pagination */
        $pagination = $this->paginator->paginate(
            $qb,
            $request->query->get('page', 1),
            $request->query->get('perPage', $this->getPerPage())
        );

        $baseTemplateVars = array(
            'request' => $request,
            'form' => isset($form) ? $form->createView() : null,
            'pagination' => $pagination,
            'listRoute' => $this->getListRoute(),
            'createRoute' => $this->getCreateRoute(),
            'editRoute' => $this->getEditRoute(),
            'viewRoute' => $this->getViewRoute(),
            'deleteRoute' => $this->getDeleteRoute(),
            'identifier' => $this->getIdentifier(),
            'transPrefix' => $this->getName(),
        );

        return $this->render(
            $this->getListTemplate(),
            array_replace_recursive($baseTemplateVars, $templateVars)
        );
    }

    /**
     * @param Request $request
     * @param array   $templateVars
     * @return Response|RedirectResponse
     */
    protected function createObject(Request $request, array $templateVars = array())
    {
        if(!$this->getCreateIsGranted()) {
            throw new AccessDeniedException("You need the permission to create an object!");
        }

        $object = $this->getCreateObject();
        $form = $this->createForm($this->getCreateFormType(), $object);

        if('POST' === $request->getMethod()) {
            $form->handleRequest($request);
            if($form->isValid()) {
                $this->prePersist($object);

                $em = $this->getManagerForClass($this->getObjectClass());
                $em->persist($object);
                $em->flush();

                $this->postPersist($object);

                $this->addFlashMessage($request, 'success', $this->getName() . '.create.flash.success');

                return new RedirectResponse($this->getCreateRedirectUrl($object), 302);
            } else {
                $this->addFlashMessage($request, 'error', $this->getName() . '.create.flash.error');
            }
        }

        $baseTemplateVars = array(
            'request' => $request,
            'object' => $object,
            'form' => $form->createView(),
            'createRoute' => $this->getCreateRoute(),
            'listRoute' => $this->getListRoute(),
            'editRoute' => $this->getEditRoute(),
            'viewRoute' => $this->getViewRoute(),
            'deleteRoute' => $this->getDeleteRoute(),
            'identifier' => $this->getIdentifier(),
            'transPrefix' => $this->getName(),
        );

        return $this->render(
            $this->getCreateTemplate(),
            array_replace_recursive($baseTemplateVars, $templateVars)
        );
    }

    /**
     * @param Request $request
     * @param int $id
     * @param array   $templateVars
     * @return Response|RedirectResponse
     */
    protected function editObject(Request $request, $id, array $templateVars = array())
    {
        /** @var ObjectRepository $repo */
        $repo = $this->getRepositoryForClass($this->getObjectClass());
        $object = $repo->find($id);

        if(null === $object) {
            throw new NotFoundHttpException("There is no object with this id");
        }

        if(!$this->getEditIsGranted($object)) {
            throw new AccessDeniedException("You need the permission to edit this object!");
        }

        $form = $this->createForm($this->getEditFormType(), $object);

        if('POST' === $request->getMethod()) {
            $form->handleRequest($request);
            if($form->isValid()) {
                $this->preUpdate($object);

                $em = $this->getManagerForClass($this->getObjectClass());
                $em->persist($object);
                $em->flush();

                $this->postUpdate($object);

                $this->addFlashMessage($request, 'success', $this->getName() . '.edit.flash.success');
            } else {
                $this->addFlashMessage($request, 'error', $this->getName() . '.edit.flash.error');
            }
        }

        $baseTemplateVars = array(
            'request' => $request,
            'object' => $object,
            'form' => $form->createView(),
            'createRoute' => $this->getCreateRoute(),
            'listRoute' => $this->getListRoute(),
            'editRoute' => $this->getEditRoute(),
            'viewRoute' => $this->getViewRoute(),
            'deleteRoute' => $this->getDeleteRoute(),
            'identifier' => $this->getIdentifier(),
            'transPrefix' => $this->getName(),
        );

        return $this->render(
            $this->getEditTemplate(),
            array_replace_recursive($baseTemplateVars, $templateVars)
        );
    }

    /**
     * @param Request $request
     * @param int $id
     * @param array   $templateVars
     * @return Response|RedirectResponse
     */
    protected function viewObject(Request $request, $id, array $templateVars = array())
    {
        /** @var ObjectRepository $repo */
        $repo = $this->getRepositoryForClass($this->getObjectClass());
        $object = $repo->find($id);

        if(null === $object) {
            throw new NotFoundHttpException("There is no object with this id");
        }

        if(!$this->getViewIsGranted($object)) {
            throw new AccessDeniedException("You need the permission to view this object!");
        }

        $baseTemplateVars = array(
            'request' => $request,
            'object' => $object,
            'createRoute' => $this->getCreateRoute(),
            'listRoute' => $this->getListRoute(),
            'editRoute' => $this->getEditRoute(),
            'viewRoute' => $this->getViewRoute(),
            'deleteRoute' => $this->getDeleteRoute(),
            'identifier' => $this->getIdentifier(),
            'transPrefix' => $this->getName(),
        );

        return $this->render(
            $this->getViewTemplate(),
            array_replace_recursive($baseTemplateVars, $templateVars)
        );
    }

    /**
     * @param Request $request
     * @param int $id
     * @return Response|RedirectResponse
     */
    protected function deleteObject(Request $request, $id)
    {
        /** @var ObjectRepository $repo */
        $repo = $this->getRepositoryForClass($this->getObjectClass());
        $object = $repo->find($id);

        if(null === $object) {
            throw new NotFoundHttpException("There is no object with this id");
        }

        if(!$this->getDeleteIsGranted($object)) {
            throw new AccessDeniedException("You need the permission to delete this object!");
        }

        $this->preRemove($object);

        $em = $this->getManagerForClass($this->getObjectClass());
        $em->remove($object);
        $em->flush();

        $this->postRemove($object);

        $this->addFlashMessage($request, 'success', $this->getName() . '.delete.flash.success');

        return new RedirectResponse($this->getListRedirectUrl(), 302);
    }

    /**
     * @param Request $request
     * @param string  $type
     * @param string  $message
     */
    protected function addFlashMessage(Request $request, $type, $message)
    {
        /** @var Session $session */
        $session = $request->getSession();
        $session->getFlashBag()->add($type, $message);
    }

    /**
     * @param string $name
     * @param array  $parameters
     * @return string
     */
    protected function generateRoute($name, array $parameters = array())
    {
        return $this->urlGenerator->generate($name, $parameters, UrlGeneratorInterface::ABSOLUTE_URL);
    }

    /**
     * @return string
     * @throws \Exception
     */
    protected function getIdentifier()
    {
        $em = $this->getManagerForClass($this->getObjectClass());
        $meta = $em->getClassMetadata($this->getObjectClass());

        $identifier = $meta->getIdentifier();

        if(1 !== count($identifier)) {
            throw new \Exception('There are multiple fields define the identifier, which is not supported!');
        }

        return reset($identifier);
    }

    /**
     * @return string
     * @throws \Exception
     */
    protected function getIdentifierMethod()
    {
        $identifier = $this->getIdentifier();

        return 'get'. ucfirst($identifier);
    }

    /**
     * @return int
     */
    protected function getPerPage()
    {
        return 10;
    }

    /**
     * @return string
     */
    protected function getListRoute()
    {
        return $this->getName() . '_list';
    }

    /**
     * @return bool
     */
    protected function getListIsGranted()
    {
        return $this->security->isGranted($this->getListRole());
    }

    /**
     * @return string
     */
    protected function getListRole()
    {
        return 'ROLE_' . strtoupper($this->getName()) . '_LIST';
    }

    /**
     * @return FormTypeInterface|null
     */
    protected function getListFormType()
    {
        return null;
    }

    /**
     * @return array
     */
    protected function getListDefaultData()
    {
        return array();
    }

    /**
     * @return string
     */
    protected function getListTemplate()
    {
        return static::TWIG_NAMESPACE .'/' . ucfirst($this->getName()) . '/list.html.twig';
    }

    /**
     * @return string
     */
    protected function getListRedirectUrl()
    {
        return $this->generateRoute($this->getListRoute());
    }

    /**
     * @return string
     */
    protected function getCreateRoute()
    {
        return $this->getName() . '_create';
    }

    /**
     * @return bool
     */
    protected function getCreateIsGranted()
    {
        return $this->security->isGranted($this->getCreateRole());
    }

    /**
     * @return string
     */
    protected function getCreateRole()
    {
        return 'ROLE_' . strtoupper($this->getName()) . '_CREATE';
    }

    /**
     * @return object
     */
    protected function getCreateObject()
    {
        $objectClass = $this->getObjectClass();

        return new $objectClass;
    }

    /**
     * @return FormTypeInterface
     */
    abstract protected function getCreateFormType();

    /**
     * @param object
     * @return string
     */
    protected function getCreateRedirectUrl($object)
    {
        $identifierMethod = $this->getIdentifierMethod();

        return $this->generateRoute($this->getEditRoute(), array('id' => $object->$identifierMethod()));
    }

    /**
     * @return string
     */
    protected function getCreateTemplate()
    {
        return static::TWIG_NAMESPACE .'/' . ucfirst($this->getName()) . '/create.html.twig';
    }

    /**
     * @return string
     */
    protected function getEditRoute()
    {
        return $this->getName() . '_edit';
    }

    /**
     * @param object
     * @return bool
     */
    protected function getEditIsGranted($object)
    {
        return $this->security->isGranted($this->getEditRole(), $object);
    }

    /**
     * @return string
     */
    protected function getEditRole()
    {
        return 'ROLE_' . strtoupper($this->getName()) . '_EDIT';
    }

    /**
     * @return FormTypeInterface
     */
    abstract protected function getEditFormType();

    /**
     * @return string
     */
    protected function getEditTemplate()
    {
        return static::TWIG_NAMESPACE .'/' . ucfirst($this->getName()) . '/edit.html.twig';
    }

    /**
     * @return string
     */
    protected function getViewRoute()
    {
        return $this->getName() . '_view';
    }

    /**
     * @param object
     * @return bool
     */
    protected function getViewIsGranted($object)
    {
        return $this->security->isGranted($this->getViewRole(), $object);
    }

    /**
     * @return string
     */
    protected function getViewRole()
    {
        return 'ROLE_' . strtoupper($this->getName()) . '_VIEW';
    }

    /**
     * @return string
     */
    protected function getViewTemplate()
    {
        return static::TWIG_NAMESPACE .'/' . ucfirst($this->getName()) . '/view.html.twig';
    }

    /**
     * @return string
     */
    protected function getDeleteRoute()
    {
        return $this->getName() . '_delete';
    }

    /**
     * @param $object
     * @return bool
     */
    protected function getDeleteIsGranted($object)
    {
        return $this->security->isGranted($this->getDeleteRole(), $object);
    }

    /**
     * @return string
     */
    protected function getDeleteRole()
    {
        return 'ROLE_' . strtoupper($this->getName()) . '_DELETE';
    }

    /**
     * @param object $object
     * @return void
     */
    protected function prePersist($object) {}

    /**
     * @param object $object
     * @return void
     */
    protected function postPersist($object) {}

    /**
     * @param object $object
     * @return void
     */
    protected function preUpdate($object) {}

    /**
     * @param object $object
     * @return void
     */
    protected function postUpdate($object) {}

    /**
     * @param object $object
     * @return void
     */
    protected function preRemove($object) {}

    /**
     * @param object $object
     * @return void
     */
    protected function postRemove($object) {}

    /**
     * @return string
     */
    abstract protected function getName();

    /**
     * @return string
     */
    abstract protected function getObjectClass();
}
