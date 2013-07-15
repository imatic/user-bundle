<?php
namespace Imatic\Bundle\UserBundle\Security\Role;

use Symfony\Component\Security\Core\Role\RoleInterface;

class Role implements RoleInterface
{
    /** @var string */
    private $vendor;

    /** @var string */
    private $bundle;

    /** @var string */
    private $type;

    /** @var string */
    private $name;

    /** @var string */
    private $action;

    /** @var string */
    private $property;

    /**
     * @param string $vendor
     * @param string $bundle
     * @param string $type
     * @param string $name
     * @param string $action
     * @param string $property
     */
    public function __construct($vendor, $bundle, $type, $name, $action = '', $property = '')
    {
        $this->vendor = (string) $vendor;
        $this->bundle = (string) $bundle;
        $this->type = (string) $type;
        $this->name = (string) $name;
        $this->action = (string) $action;
        $this->property = (string) $property;
    }

    /**
     * @return string
     */
    public function getRole()
    {
        return strtoupper(sprintf(
            'ROLE_%s_%s_%s_%s_%s',
            $this->vendor,
            $this->bundle,
            $this->type,
            $this->getFullName(),
            $this->action
        ));
    }

    /**
     * @return string
     */
    public function getVendor()
    {
        return $this->vendor;
    }

    /**
     * @return string
     */
    public function getBundle()
    {
        return $this->bundle;
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getAction()
    {
        return $this->action;
    }

    /**
     * @return string
     */
    public function getProperty()
    {
        return $this->property;
    }

    /**
     * @return string
     */
    public function getAbsoluteName()
    {
        return sprintf('%s%s%s', $this->vendor, $this->bundle == '' ? '' : $this->bundle . 'Bundle', $this->getName());
    }

    /**
     * @return string
     */
    public function getFullName()
    {
        return $this->name . ($this->property === '' ? '' : '.' . $this->property);
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->getRole();
    }
}