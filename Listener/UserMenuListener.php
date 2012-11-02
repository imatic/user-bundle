<?php

namespace Imatic\Bundle\UserBundle\Listener;

use Symfony\Component\Security\Core\SecurityContextInterface;
use Symfony\Component\Translation\TranslatorInterface;
use Imatic\Bundle\ViewBundle\Event\ConfigureMenuEvent;

class UserMenuListener
{
    /**
     * @var SecurityContextInterface
     */
    protected $securityContext;
    /**
     * @var TranslatorInterface
     */
    protected $translator;
    /**
     * @var boolean
     */
    protected $loggedIn;

    /**
     * @param SecurityContextInterface $securityContext
     * @param TranslatorInterface $translator
     */
    public function __construct(SecurityContextInterface $securityContext, TranslatorInterface $translator)
    {
        $this->securityContext = $securityContext;
        $this->translator = $translator;
        $this->isLoggedIn = false;
        if ($this->securityContext->getToken()) {
            $this->isLoggedIn = $this->securityContext->isGranted('IS_AUTHENTICATED_FULLY');
        }
    }

    /**
     * @param ConfigureMenuEvent $event
     */
    public function onConfigure(ConfigureMenuEvent $event)
    {
        $t = $this->translator;
        $factory = $event->getFactory();
        $menu = $event->getMenu();

        $factory->addDivider($menu, true);
        if ($this->isLoggedIn) {
            $userMenu = $factory->createDropdown($menu, $this->securityContext->getToken()->getUser());
            $userMenu->addChild($t->trans('profile.show.headline', array(), 'ImaticUserBundle'), array('route' => 'fos_user_profile_show'));
            $userMenu->addChild($t->trans('profile.password.headline', array(), 'ImaticUserBundle'), array('route' => 'fos_user_change_password'));
            $factory->addDivider($userMenu);
            $userMenu->addChild($t->trans('layout.logout', array(), 'FOSUserBundle'), array('route' => 'fos_user_security_logout'));
        } else {
            $menu->addChild($t->trans('layout.login', array(), 'FOSUserBundle'), array('route' => 'fos_user_security_login'));
        }
    }
}