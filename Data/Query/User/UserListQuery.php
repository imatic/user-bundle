<?php
namespace Imatic\Bundle\UserBundle\Data\Query\User;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\QueryBuilder;
use Imatic\Bundle\DataBundle\Data\Driver\DoctrineORM\QueryObjectInterface;
use Imatic\Bundle\DataBundle\Data\Query\DisplayCriteria\FilterableQueryObjectInterface;
use Imatic\Bundle\DataBundle\Data\Query\DisplayCriteria\SortableQueryObjectInterface;

class UserListQuery implements QueryObjectInterface, SortableQueryObjectInterface, FilterableQueryObjectInterface
{
    /**
     * @var string
     */
    private $class;

    public function __construct($class)
    {
        $this->class = $class;
    }

    /**
     * @param EntityManager $em
     *
     * @return QueryBuilder
     */
    public function build(EntityManager $em)
    {
        return (new QueryBuilder($em))
            ->select('u')
            ->from($this->class, 'u');
    }

    /**
     * @return array
     */
    public function getSorterMap()
    {
        return [
            'username' => 'u.username',
            'enabled' => 'u.enabled',
            'email' => 'u.email',
            'lastLogin' => 'u.lastLogin',
        ];
    }

    /**
     * @return array
     */
    public function getDefaultSort()
    {
        return ['username' => 'ASC'];
    }

    public function getFilterMap()
    {
        return [
            'username' => 'u.username',
        ];
    }
}
