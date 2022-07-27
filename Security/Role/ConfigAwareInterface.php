<?php declare(strict_types=1);
namespace Imatic\Bundle\UserBundle\Security\Role;

interface ConfigAwareInterface
{
    /**
     * @param array $config
     *
     * @return $this
     */
    public function setConfig(array $config);
}
