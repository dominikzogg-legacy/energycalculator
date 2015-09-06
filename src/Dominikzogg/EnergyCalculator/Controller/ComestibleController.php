<?php

namespace Dominikzogg\EnergyCalculator\Controller;

use Doctrine\Common\Persistence\ManagerRegistry;
use Dominikzogg\EnergyCalculator\Entity\Comestible;
use Dominikzogg\EnergyCalculator\Form\ComestibleListType;
use Dominikzogg\EnergyCalculator\Form\ComestibleType;
use Dominikzogg\EnergyCalculator\Repository\ComestibleRepository;
use Knp\Component\Pager\Paginator;
use Saxulum\Crud\Listing\ListingFactory;
use Saxulum\RouteController\Annotation\DI;
use Saxulum\RouteController\Annotation\Route;
use Symfony\Component\Form\FormFactory;
use Symfony\Component\Form\FormTypeInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PropertyAccess\PropertyAccessor;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Security\Core\SecurityContextInterface;

/**
 * @Route("/{_locale}/comestible", asserts={"_locale"="([a-z]{2}|[a-z]{2}_[A-Z]{2})"})
 * @DI(serviceIds={
 *      "security.authorization_checker",
 *      "security.token_storage",
 *      "saxulum.crud.listing.factory",
 *      "doctrine",
 *      "form.factory",
 *      "knp_paginator",
 *      "url_generator",
 *      "twig"
 * })
 */
class ComestibleController extends AbstractCRUDController
{
    /**
     * @param AuthorizationCheckerInterface $authorizationChecker
     * @param TokenStorageInterface $tokenStorage
     * @param ListingFactory $listingFactory
     * @param ManagerRegistry          $doctrine
     * @param FormFactory              $formFactory
     * @param Paginator                $paginator
     * @param UrlGeneratorInterface    $urlGenerator
     * @param \Twig_Environment        $twig
     */
    public function __construct(
        AuthorizationCheckerInterface $authorizationChecker,
        TokenStorageInterface $tokenStorage,
        ListingFactory $listingFactory,
        ManagerRegistry $doctrine,
        FormFactory $formFactory,
        Paginator $paginator,
        UrlGeneratorInterface $urlGenerator,
        \Twig_Environment $twig
    ) {
        $this->authorizationChecker = $authorizationChecker;
        $this->tokenStorage = $tokenStorage;
        $this->listingFactory = $listingFactory;
        $this->doctrine = $doctrine;
        $this->formFactory = $formFactory;
        $this->paginator = $paginator;
        $this->urlGenerator = $urlGenerator;
        $this->twig = $twig;
    }

    /**
     * @Route("/", bind="comestible_list", method="GET")
     * @param  Request  $request
     * @return Response
     */
    public function listAction(Request $request)
    {
        return parent::crudListObjects($request);
    }

    /**
     * @Route("/create", bind="comestible_create")
     * @param  Request                   $request
     * @return Response|RedirectResponse
     */
    public function createAction(Request $request)
    {
        return parent::crudCreateObject($request);
    }

    /**
     * @Route("/edit/{id}", bind="comestible_edit", asserts={"id"="[0-9a-f]{1,24}"})
     * @param  Request                   $request
     * @param $id
     * @return Response|RedirectResponse
     */
    public function editAction(Request $request, $id)
    {
        return parent::crudEditObject($request, $id);
    }

    /**
     * @Route("/delete/{id}", bind="comestible_delete", asserts={"id"="[0-9a-f]{1,24}"}, method="GET")
     * @param  Request          $request
     * @param $id
     * @return RedirectResponse
     */
    public function deleteAction(Request $request, $id)
    {
        return parent::crudDeleteObject($request, $id);
    }

    /**
     * @Route("/choice", bind="comestible_choice", method="GET")
     * @param  Request          $request
     * @return RedirectResponse
     */
    public function choiceAction(Request $request)
    {
        $choiceLabel = $request->query->get('choice_label', 'name');
        $search = urldecode($request->query->get('q', ''));

        /** @var ComestibleRepository $repo */
        $repo = $this->crudRepositoryForClass($this->crudObjectClass());
        $propertyAccessor = new PropertyAccessor();
        $data = array();
        foreach ($repo->searchComestibleOfUser($this->getUser(), $search) as $comestible) {
            $data[] = array(
                'id' => $comestible->getId(),
                'text' => $propertyAccessor->getValue($comestible, $choiceLabel),
                'default' => $comestible->getDefaultValue(),
            );
        }

        return new JsonResponse($data);
    }

    /**
     * @return int
     */
    protected function crudListPerPage()
    {
        return 20;
    }

    /**
     * @param Request $request
     *
     * @return FormTypeInterface
     */
    protected function crudListFormType(Request $request)
    {
        return new ComestibleListType();
    }

    /**
     * @param  Request $request
     * @param  array   $formData
     * @return array
     */
    protected function crudListFormDataEnrich(Request $request, array $formData)
    {
        return array_replace_recursive($formData, array(
            'user' => $this->getUser()->getId(),
        ));
    }

    /**
     * @param  Request    $request
     * @return Comestible
     */
    protected function crudCreateFactory(Request $request)
    {
        $objectClass = $this->crudObjectClass();

        /** @var Comestible $object */
        $object = new $objectClass();
        $object->setUser($this->getUser());

        return $object;
    }

    /**
     * @param Request $request
     * @param object $object
     *
     * @return FormTypeInterface
     */
    protected function crudCreateFormType(Request $request, $object)
    {
        return new ComestibleType();
    }

    /**
     * @return string
     */
    protected function crudEditRole()
    {
        return strtoupper('edit');
    }

    /**
     * @param Request $request
     * @param object $object
     *
     * @return FormTypeInterface
     */
    protected function crudEditFormType(Request $request, $object)
    {
        return new ComestibleType();
    }

    /**
     * @return string
     */
    protected function crudViewRoute()
    {
        return '';
    }

    /**
     * @return string
     */
    protected function crudDeleteRole()
    {
        return strtoupper('delete');
    }

    /**
     * @return string
     */
    protected function crudName()
    {
        return 'comestible';
    }

    /**
     * @return string
     */
    protected function crudObjectClass()
    {
        return Comestible::class;
    }
}
