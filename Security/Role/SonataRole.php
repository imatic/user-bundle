<?php
namespace Imatic\Bundle\UserBundle\Security\Role;

class SonataRole extends ObjectRole
{
    /** @var string */
    private $role;

    /**
     * @param string $vendor
     * @param string $bundle
     * @param string $type
     * @param string $name
     * @param string $action
     * @param string $role
     */
    public function __construct($vendor, $bundle, $type, $name, $action, $role)
    {
        parent::__construct($vendor, $bundle, $type, $name, '', $action);
        $this->role = (string) $role;
    }

    /**
     * {@inheritDoc}
     */
    public function getRole()
    {
        return $this->role;
    }

    /**
     * {@inheritDoc}
     */
    public function getAction()
    {
        return '';
    }

    /**
     * {@inheritDoc}
     */
    public function getLabel()
    {
        return parent::getAction();
    }
}