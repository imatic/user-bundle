<?php declare(strict_types=1);
namespace Imatic\Bundle\UserBundle\Security\Role;

class ObjectRole extends Role
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
    private $property;

    /** @var string */
    private $action;

    /**
     * @param string $vendor
     * @param string $bundle
     * @param string $type
     * @param string $name
     * @param string $property
     * @param string $action
     */
    public function __construct($vendor, $bundle, $type, $name, $property, $action)
    {
        $this->vendor = (string) $vendor;
        $this->bundle = (string) $bundle;
        $this->type = (string) $type;
        $this->name = (string) $name;
        $this->property = (string) $property;
        $this->action = (string) $action;
    }

    /**
     * {@inheritdoc}
     */
    public function getRole()
    {
        return \strtoupper(\sprintf(
            'ROLE_%s_%s_%s_%s.%s_%s',
            $this->vendor,
            $this->bundle,
            $this->type,
            $this->name,
            $this->property,
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
     * {@inheritdoc}
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
    public function getProperty()
    {
        return $this->property;
    }

    /**
     * {@inheritdoc}
     */
    public function getAction()
    {
        return $this->action;
    }

    /**
     * {@inheritdoc}
     */
    public function getLabel()
    {
        return $this->property;
    }

    /**
     * {@inheritdoc}
     */
    public function getDomain()
    {
        return \sprintf('%s%sBundle%s', $this->vendor, $this->bundle, $this->name);
    }

    /**
     * {@inheritdoc}
     */
    public function __toString(): string
    {
        return $this->getRole();
    }
}
