<?php declare(strict_types=1);
namespace Imatic\Bundle\UserBundle\Security\Role;

class ObjectRole extends Role
{
    public function __construct(
        private string $vendor,
        private string $bundle,
        private string $type,
        private string $name,
        private string $property,
        private string $action
    ) {
    }

    /**
     * {@inheritdoc}
     */
    public function getRole(): string
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

    public function getVendor(): string
    {
        return $this->vendor;
    }

    public function getBundle(): string
    {
        return $this->bundle;
    }

    /**
     * {@inheritdoc}
     */
    public function getType(): string
    {
        return $this->type;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getProperty(): string
    {
        return $this->property;
    }

    /**
     * {@inheritdoc}
     */
    public function getAction(): string
    {
        return $this->action;
    }

    /**
     * {@inheritdoc}
     */
    public function getLabel(): string
    {
        return $this->property;
    }

    /**
     * {@inheritdoc}
     */
    public function getDomain(): string
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
