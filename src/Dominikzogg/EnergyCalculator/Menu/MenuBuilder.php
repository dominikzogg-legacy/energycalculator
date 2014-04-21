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

    public function buildAdminMenu(Request $request)
    {
        $menu = $this->menuFactory->createItem('root');

        if (!is_null($this->getUser())) {
            $this->createManageMenu($menu, $request);

            if ($this->securityContext->isGranted('ROLE_ADMIN')) {
                $this->createAdminMenu($menu, $request);
            }
        }

        return $menu;
    }

    /**
     * @param ItemInterface $menu
     * @param Request $request
     */
    protected function createManageMenu(ItemInterface $menu, Request $request)
    {
        $fittingMenu = $menu->addChild($this->translator->trans('nav.manage.title'));
        $fittingMenu->addChild($this->translator->trans('nav.manage.comestible'), array(
            'route' => 'comestible_list'
        ));
    }

    /**
     * @param ItemInterface $menu
     * @param Request $request
     */
    protected function createAdminMenu(ItemInterface $menu, Request $request)
    {
        $userMenu = $menu->addChild($this->translator->trans('nav.administration.title'));
        $userMenu->addChild($this->translator->trans('nav.administration.user'), array(
            'route' => 'user_list'
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