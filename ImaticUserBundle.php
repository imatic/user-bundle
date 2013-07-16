<?php
namespace Imatic\Bundle\UserBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;

class ImaticUserBundle extends Bundle
{
    /**
     * {@inheritDoc}
     */
    public function getParent()
    {
        return 'FOSUserBundle';
    }
}
