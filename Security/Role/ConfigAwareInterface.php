<?php declare(strict_types=1);
namespace Imatic\Bundle\UserBundle\Security\Role;

interface ConfigAwareInterface
{
    public function setConfig(array $config): self;
}
