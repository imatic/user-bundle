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
        $menu->addChild($helper->trans('User Profile', array(), 'ImaticUserBundle'), array('route' => 'fos_user_profile_show'));
        $menu->addChild($helper->trans('Change Password', array(), 'ImaticUserBundle'), array('route' => 'fos_user_change_password'));
        $helper->addDivider($menu);
        if ($helper->isUserGranted('ROLE_PREVIOUS_ADMIN')) {
            $menu->addChild($helper->trans('Switch user exit', array(), 'ImaticUserBundleUser'), array('route' => 'homepage', 'routeParameters' => array('_switch_user' => '_exit')));
        }
        $menu->addChild($helper->trans('layout.logout', array(), 'FOSUserBundle'), array('route' => 'fos_user_security_logout'));

        return $menu;
    }

    /**
     * @param  Factory                 $factory
     * @param  Helper                  $helper
     * @return \Knp\Menu\ItemInterface
     */
    public function getMenuAnon(Factory $factory, Helper $helper)
    {
        $menu = $factory->createItem($helper->trans('layout.login', array(), 'FOSUserBundle'), array('route' => 'fos_user_security_login'));

        return $menu;
    }
}
