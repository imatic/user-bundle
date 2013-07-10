<?php
namespace Imatic\Bundle\UserBundle\Security\Role;

class Configuration
{
    /** @var string[] */
    private $excludes;

    /** @var string[] */
    private $includes;

    /** @var array */
    private $groups;

    /**
     * @param string[] $excludes
     * @param string[] $includes
     * @param array $groups
     */
    public function __construct(array $excludes = [], array $includes = [], array $groups = [])
    {
        $this->excludes = $excludes;
        $this->includes = $includes;
        $this->groups = $groups;
    }

    /**
     * @return string[]
     */
    public function getExcludes()
    {
        return $this->excludes;
    }

    /**
     * @return string[]
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