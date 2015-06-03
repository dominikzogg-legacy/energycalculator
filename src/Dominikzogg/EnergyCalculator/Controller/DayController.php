<?php

namespace Dominikzogg\EnergyCalculator\Controller;

use Doctrine\Common\Persistence\ManagerRegistry;
use Dominikzogg\EnergyCalculator\Entity\Comestible;
use Dominikzogg\EnergyCalculator\Entity\Day;
use Dominikzogg\EnergyCalculator\Form\DayListType;
use Dominikzogg\EnergyCalculator\Form\DayType;
use Dominikzogg\EnergyCalculator\Repository\ComestibleRepository;
use Knp\Component\Pager\Paginator;
use Saxulum\Crud\Listing\ListingFactory;
use Saxulum\Crud\Pagination\KnpPaginationAdapter;
use Saxulum\RouteController\Annotation\DI;
use Saxulum\RouteController\Annotation\Route;
use Symfony\Component\Form\FormFactory;
use Symfony\Component\Form\FormTypeInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Translation\TranslatorInterface;

/**
 * @Route("/{_locale}/day")
 * @DI(serviceIds={
 *      "security.authorization_checker",
 *      "security.token_storage",
 *      "saxulum.crud.listing.factory",
 *      "doctrine",
 *      "form.factory",
 *      "knp_paginator",
 *      "url_generator",
 *      "twig",
 *      "translator"
 * })
 */
class DayController extends AbstractCRUDController
{
    /**
     * @var TranslatorInterface
     */
    protected $translator;

    /**
     * @param AuthorizationCheckerInterface $authorizationChecker
     * @param TokenStorageInterface $tokenStorage
     * @param ListingFactory $listingFactory
     * @param ManagerRegistry          $doctrine
     * @param FormFactory              $formFactory
     * @param Paginator                $paginator
     * @param UrlGeneratorInterface    $urlGenerator
     * @param \Twig_Environment        $twig
     * @param TranslatorInterface      $translator
     */
    public function __construct(
        AuthorizationCheckerInterface $authorizationChecker,
        TokenStorageInterface $tokenStorage,
        ListingFactory $listingFactory,
        ManagerRegistry $doctrine,
        FormFactory $formFactory,
        Paginator $paginator,
        UrlGeneratorInterface $urlGenerator,
        \Twig_Environment $twig,
        TranslatorInterface $translator
    ) {
        $this->authorizationChecker = $authorizationChecker;
        $this->tokenStorage = $tokenStorage;
        $this->listingFactory = $listingFactory;
        $this->doctrine = $doctrine;
        $this->formFactory = $formFactory;
        $this->paginator = new KnpPaginationAdapter($paginator);
        $this->urlGenerator = $urlGenerator;
        $this->twig = $twig;
        $this->translator = $translator;
    }

    /**
     * @Route("/", bind="day_list", method="GET")
     * @param  Request  $request
     * @return Response
     */
    public function listAction(Request $request)
    {
        return parent::crudListObjects($request);
    }

    /**
     * @Route("/create", bind="day_create")
     * @param  Request                   $request
     * @return Response|RedirectResponse
     */
    public function createAction(Request $request)
    {
        return parent::crudCreateObject($request);
    }

    /**
     * @Route("/edit/{id}", bind="day_edit", asserts={"id"="\d+"})
     * @param  Request                   $request
     * @param $id
     * @return Response|RedirectResponse
     */
    public function editAction(Request $request, $id)
    {
        return parent::crudEditObject($request, $id);
    }

    /**
     * @Route("/view/{id}", bind="day_view", asserts={"id"="\d+"}, method="GET")
     * @param  Request  $request
     * @param $id
     * @return Response
     */
    public function viewAction(Request $request, $id)
    {
        return parent::crudViewObject($request, $id);
    }

    /**
     * @Route("/delete/{id}", bind="day_delete", asserts={"id"="\d+"}, method="GET")
     * @param  Request          $request
     * @param $id
     * @return RedirectResponse
     */
    public function deleteAction(Request $request, $id)
    {
        return parent::crudDeleteObject($request, $id);
    }

    /**
     * @return int
     */
    protected function crudListPerPage()
    {
        return 7;
    }

    /**
     * @return FormTypeInterface
     */
    protected function crudListFormType()
    {
        return new DayListType();
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
     * @param  Request $request
     * @return Day
     */
    protected function crudCreateFactory(Request $request)
    {
        $objectClass = $this->crudObjectClass();

        /** @var Day $object */
        $object = new $objectClass();
        $object->setUser($this->getUser());

        return $object;
    }

    /**
     * @return FormTypeInterface
     */
    protected function crudCreateFormType()
    {
        return $this->getEditForm();
    }

    /**
     * @return string
     */
    protected function crudEditRole()
    {
        return strtoupper('edit');
    }

    /**
     * @return FormTypeInterface
     */
    protected function crudEditFormType()
    {
        return $this->getEditForm();
    }

    /**
     * @return DayType
     * @throws \Exception
     */
    protected function getEditForm()
    {
        /** @var ComestibleRepository $repo */
        $repo = $this->crudRepositoryForClass(Comestible::class);

        return new DayType(
            $this->getUser(),
            $this->translator,
            $repo->searchComestibleOfUserQb($this->getUser())
        );
    }

    /**
     * @return string
     */
    protected function crudViewRole()
    {
        return strtoupper('view');
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
        return 'day';
    }

    /**
     * @return string
     */
    protected function crudObjectClass()
    {
        return Day::class;
    }
}
