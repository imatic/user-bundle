<?php

namespace Imatic\Bundle\UserBundle\Menu;

use Imatic\Bundle\ViewBundle\Menu\Factory;
use Imatic\Bundle\ViewBundle\Menu\Helper;

class UserMenuBuilder
{
    /**
     * @param  Factory                 $factory
     * @param  Helper                  $helper
     * @return \Knp\Menu\ItemInterface
     */
    public function getMenu(Factory $factory, Helper $helper)
    {
        $menu = $factory->createItem($helper->getUser()->getUsername());
        $helper->setDropdown($menu);
        $menu->addChild($helper->trans('User Profile', [], 'ImaticUserBundle'), ['route' => 'fos_user_profile_show']);
        $menu->addChild($helper->trans('Change Password', [], 'ImaticUserBundle'), ['route' => 'fos_user_change_password']);
        $helper->addDivider($menu);
        if ($helper->isUserGranted('ROLE_PREVIOUS_ADMIN')) {
            $menu->addChild($helper->trans('Switch user exit', [], 'ImaticUserBundleUser'), ['route' => 'homepage', 'routeParameters' => ['_switch_user' => '_exit']]);
        }
        $menu->addChild($helper->trans('layout.logout', [], 'FOSUserBundle'), ['route' => 'fos_user_security_logout']);

        return $menu;
    }

    /**
     * @param  Factory                 $factory
     * @param  Helper                  $helper
     * @return \Knp\Menu\ItemInterface
     */
    public function getMenuAnon(Factory $factory, Helper $helper)
    {
        $menu = $factory->createItem($helper->trans('layout.login', [], 'FOSUserBundle'), ['route' => 'fos_user_security_login']);

        return $menu;
    }
}
