<?php

namespace Imatic\Bundle\UserBundle\Admin;

use Sonata\AdminBundle\Admin\Admin as BaseAdmin;

class Admin extends BaseAdmin
{
    public function getFormTheme()
    {
        return array_merge(
            parent::getFormTheme(),
            array('ImaticUserBundle:Admin:form_theme.html.twig')
        );
    }
}
