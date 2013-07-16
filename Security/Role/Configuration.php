<?php
namespace Imatic\Bundle\UserBundle\Security\Role;

/**
 * @author Marek Stipek <marek.stipek@imatic.cz>
 */
class Configuration
{
    /**
     * @var array
     */
    private $excludes;

    /**
     * @var array
     */
    private $includes;

    /**
     * @var array
     */
    private $groups;

    /**
     * @param array $excludes
     * @param array $includes
     * @param array $groups
     */
    public function __construct(array $excludes = [], array $includes = [], array $groups = [])
    {
        $this->excludes = $excludes;
        $this->includes = $includes;
        $this->groups = $groups;
    }

    /**
     * @return array
     */
    public function getExcludes()
    {
        return $this->excludes;
    }

    /**
     * @return array
     */
    public function getIncludes()
    {
        return $this->includes;
    }

    /**
     * @return array
     */
    public function getGroups()
    {
        return $this->groups;
    }
}
