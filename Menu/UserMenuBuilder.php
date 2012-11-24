<?php

namespace Imatic\Bundle\UserBundle\Menu;

use Imatic\Bundle\ViewBundle\Menu\Factory;
use Imatic\Bundle\ViewBundle\Menu\Helper;

class UserMenuBuilder
{

    /**
     * @param Factory $factory
     * @param Helper $helper
     * @return \Knp\Menu\ItemInterface
     */
    public function getMenu(Factory $factory, Helper $helper)
    {
//        if ($helper->isUserLogged()) {
////            $menu = $factory->createItem();
//
//            $userMenu = $helper->createDropdown($menu, $this->securityContext->getToken()->getUser());
//            $userMenu->addChild($helper->trans('User Profile', array(), 'ImaticUserBundle'), array('route' => 'fos_user_profile_show'));
//            $userMenu->addChild($helper->trans('Change Password', array(), 'ImaticUserBundle'), array('route' => 'fos_user_change_password'));
//            $helper->addDivider($userMenu);
//            if ($helper->isUserGranted('ROLE_PREVIOUS_ADMIN')) {
//                $userMenu->addChild($helper->trans('Switch user exit', array(), 'ImaticUserBundleUser'), array('route' => 'homepage', 'routeParameters' => array('_switch_user' => '_exit')));
//            }
//            $userMenu->addChild($helper->trans('layout.logout', array(), 'FOSUserBundle'), array('route' => 'fos_user_security_logout'));
//        } else {
//            $menu = $factory->createItem($helper->trans('layout.login', array(), 'FOSUserBundle'), array('route' => 'fos_user_security_login'));
//        }

        $menu = $factory->createItem($helper->trans('layout.login', array(), 'FOSUserBundle'), array('route' => 'fos_user_security_login'));
        return $menu;
    }
}