<?php

namespace Dominikzogg\EnergyCalculator\Menu;

use Dominikzogg\EnergyCalculator\Entity\User;
use Knp\Menu\FactoryInterface;
use Knp\Menu\ItemInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Translation\TranslatorInterface;

class MenuBuilder
{
    /**
     * @var FactoryInterface
     */
    protected $menuFactory;

    /**
     * @var AuthorizationCheckerInterface
     */
    protected $authorizationChecker;

    /**
     * @var TokenStorageInterface
     */
    protected $tokenStorage;

    /**
     * @var TranslatorInterface
     */
    protected $translator;

    /**
     * @param FactoryInterface $menuFactory
     * @param AuthorizationCheckerInterface $authorizationChecker
     * @param TokenStorageInterface $tokenStorage
     * @param TranslatorInterface $translator
     */
    public function __construct(
        FactoryInterface $menuFactory,
        AuthorizationCheckerInterface $authorizationChecker,
        TokenStorageInterface $tokenStorage,
        TranslatorInterface $translator
    ) {
        $this->menuFactory = $menuFactory;
        $this->authorizationChecker = $authorizationChecker;
        $this->tokenStorage = $tokenStorage;
        $this->translator = $translator;
    }

    public function buildMenu(Request $request)
    {
        $menu = $this->menuFactory->createItem('root');

        if ($this->getUser() instanceof User) {
            $this->createManageMenu($menu, $request);
            $this->createChartMenu($menu, $request);

            if ($this->authorizationChecker->isGranted('ROLE_ADMIN')) {
                $this->createAdminMenu($menu, $request);
            }

            $this->createUserMenu($menu, $request);
        }

        return $menu;
    }

    /**
     * @param ItemInterface $menu
     * @param Request       $request
     */
    protected function createManageMenu(ItemInterface $menu, Request $request)
    {
        $menu->addChild($this->translator->trans('nav.day'), array(
            'route' => 'day_list',
        ));
        $menu->addChild($this->translator->trans('nav.comestible'), array(
            'route' => 'comestible_list',
        ));
    }

    /**
     * @param ItemInterface $menu
     * @param Request       $request
     */
    protected function createChartMenu(ItemInterface $menu, Request $request)
    {
        $chartMenu = $menu->addChild($this->translator->trans('nav.chart.title'));

        $chartMenu->addChild($this->translator->trans('nav.chart.weight'), array(
            'route' => 'chart_weight',
        ));

        $chartMenu->addChild($this->translator->trans('nav.chart.calorie'), array(
            'route' => 'chart_calorie',
        ));

        $chartMenu->addChild($this->translator->trans('nav.chart.energymix'), array(
            'route' => 'chart_energymix',
        ));
    }

    /**
     * @param ItemInterface $menu
     * @param Request       $request
     */
    protected function createAdminMenu(ItemInterface $menu, Request $request)
    {
        $menu->addChild($this->translator->trans('nav.user'), array(
            'route' => 'user_list',
        ));
    }

    /**
     * @param ItemInterface $menu
     * @param Request       $request
     */
    protected function createUserMenu(ItemInterface $menu, Request $request)
    {
        $menu->addChild($this->translator->trans('nav.logout'), array(
            'route' => 'logout',
        ));
    }

    /**
     * @return User|null
     */
    protected function getUser()
    {
        $token = $this->tokenStorage->getToken();

        if (is_null($token)) {
            return null;
        }

        return $token->getUser();
    }
}
