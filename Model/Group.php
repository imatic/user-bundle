<?php

namespace Imatic\Bundle\UserBundle\Model;

/**
 * Group.
 *
 * @author Viliam Husár <viliam.husar@imatic.cz>
 */
class Group implements GroupInterface
{
    /**
     * @var $id
     */
    protected $id;

    /**
     * @var string
     */
    protected $name;

    /**
     * @var array
     */
    protected $roles;

    /**
     * Constructor.
     *
     * @param string $name
     * @param array $roles
     */
    public function __construct($name, $roles = array())
    {
        $this
            ->setName($name)
            ->setRoles($roles);
    }

    /**
     * Returns ID.
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }


    /**
     * Sets name.
     *
     * @param string $name
     *
     * @return $this
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Returns name.
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Sets roles.
     *
     * @param array $roles
     *
     * @return $this
     */
    public function setRoles(array $roles)
    {
        $this->roles = array();

        foreach ($roles as $role) {
            $this->addRole($role);
        }

        return $this;
    }

    /**
     * Adds role.
     *
     * @param string $role
     *
     * @return $this
     */
    public function addRole($role)
    {
        $role = (string) $role;
        $role = strtoupper ($role);

        if (!$this->hasRole($role)) {
            $this->roles[] = $role;
        }

        return $this;
    }

    /**
     * Removes role.
     *
     * @param string $role
     *
     * @return $this
     */
    public function removeRole($role)
    {
        $role = (string) $role;

        if (false !== $key = array_search(strtoupper($role), $this->roles, true)) {
            unset($this->roles[$key]);
            $this->roles = array_values($this->roles);
        }

        return $this;
    }

    /**
     * Returns roles.
     *
     * @return array
     */
    public function getRoles()
    {
        return $this->roles;
    }

    /**
     * Returns true if user has role.
     *
     * @param string $role
     *
     * @return bool
     */
    public function hasRole($role)
    {
        $role = (string) $role;

        return in_array(strtoupper($role), $this->roles, true);
    }
}
