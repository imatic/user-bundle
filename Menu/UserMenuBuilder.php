<?php declare(strict_types=1);
namespace Imatic\Bundle\UserBundle\Menu;

use Imatic\Bundle\ViewBundle\Menu\Factory;
use Imatic\Bundle\ViewBundle\Menu\Helper;

class UserMenuBuilder
{
    public function getMenu(Factory $factory, Helper $helper): \Knp\Menu\ItemInterface
    {
        $menu = $factory->createItem($helper->getUser()->getUsername());
        $helper->setDropdown($menu);
        $menu->addChild($helper->trans('User Profile', [], 'ImaticUserBundle'), ['route' => 'user_profile_show']);
        $menu->addChild($helper->trans('Change password', [], 'ImaticUserBundle'), ['route' => 'user_change_password'])
            ->setAttribute('divider', true);
        if ($helper->isUserGranted('ROLE_PREVIOUS_ADMIN')) {
            $menu->addChild($helper->trans('Switch user exit', [], 'ImaticUserBundleUser'), ['route' => 'homepage', 'routeParameters' => ['_switch_user' => '_exit']]);
        }
        $menu->addChild($helper->trans('layout.logout', [], 'ImaticUserBundle'), ['route' => 'user_security_logout']);

        return $menu;
    }

    public function getMenuAnon(Factory $factory, Helper $helper): \Knp\Menu\ItemInterface
    {
        $menu = $factory->createItem($helper->trans('layout.login', [], 'ImaticUserBundle'), ['route' => 'user_security_login']);

        return $menu;
    }
}
