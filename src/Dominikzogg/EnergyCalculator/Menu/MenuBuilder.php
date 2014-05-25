<?php

namespace Dominikzogg\EnergyCalculator\Menu;

use Dominikzogg\EnergyCalculator\Entity\User;
use Knp\Menu\FactoryInterface;
use Knp\Menu\ItemInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\SecurityContextInterface;
use Symfony\Component\Translation\TranslatorInterface;

class MenuBuilder
{
    /**
     * @var FactoryInterface
     */
    protected $menuFactory;

    /**
     * @var SecurityContextInterface
     */
    protected $securityContext;

    /**
     * @var TranslatorInterface
     */
    protected $translator;

    public function __construct(FactoryInterface $menuFactory, SecurityContextInterface $securityContext, TranslatorInterface $translator)
    {
        $this->menuFactory = $menuFactory;
        $this->securityContext = $securityContext;
        $this->translator = $translator;
    }

    public function buildMenu(Request $request)
    {
        $menu = $this->menuFactory->createItem('root');

        if ($this->getUser() instanceof User) {
            $this->createManageMenu($menu, $request);
            $this->createChartMenu($menu, $request);

            if ($this->securityContext->isGranted('ROLE_ADMIN')) {
                $this->createAdminMenu($menu, $request);
            }

            $this->createUserMenu($menu, $request);
        }

        return $menu;
    }

    /**
     * @param ItemInterface $menu
     * @param Request $request
     */
    protected function createManageMenu(ItemInterface $menu, Request $request)
    {
        $menu->addChild($this->translator->trans('nav.day'), array(
            'route' => 'day_list'
        ));
        $menu->addChild($this->translator->trans('nav.comestible'), array(
            'route' => 'comestible_list'
        ));
    }


    /**
     * @param ItemInterface $menu
     * @param Request $request
     */
    protected function createChartMenu(ItemInterface $menu, Request $request)
    {
        $chartMenu = $menu->addChild($this->translator->trans('nav.chart.title'));

        $chartMenu->addChild($this->translator->trans('nav.chart.weight'), array(
            'route' => 'chart_weight'
        ));
    }

    /**
     * @param ItemInterface $menu
     * @param Request $request
     */
    protected function createAdminMenu(ItemInterface $menu, Request $request)
    {
        $menu->addChild($this->translator->trans('nav.user'), array(
            'route' => 'user_list'
        ));
    }

    /**
     * @param ItemInterface $menu
     * @param Request $request
     */
    protected function createUserMenu(ItemInterface $menu, Request $request)
    {
        $menu->addChild($this->translator->trans('nav.logout'), array(
            'route' => 'logout'
        ));
    }

    /**
     * @return User|null
     */
    protected function getUser()
    {
        $token = $this->securityContext->getToken();

        if(is_null($token)) {
            return null;
        }

        return $token->getUser();
    }
}