<?php declare(strict_types=1);
namespace Imatic\Bundle\UserBundle\Model;

/**
 * Group interface.
 *
 * @author Viliam Husár <viliam.husar@imatic.cz>
 */
interface GroupInterface
{
    /**
     * @param string $role
     *
     * @return static
     */
    public function addRole($role);

    /**
     * @return mixed
     */
    public function getId();

    /**
     * @return string
     */
    public function getName();

    /**
     * @param string $role
     *
     * @return bool
     */
    public function hasRole($role);

    /**
     * @return array
     */
    public function getRoles();

    /**
     * @param string $role
     *
     * @return static
     */
    public function removeRole($role);

    /**
     * @param string $name
     *
     * @return static
     */
    public function setName($name);

    /**
     * @return static
     */
    public function setRoles(array $roles);
}
