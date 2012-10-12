<?php

namespace Imatic\Bundle\UserBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;

class ImaticUserBundle extends Bundle
{
    public function getParent()
    {
        return 'FOSUserBundle';
    }
}
