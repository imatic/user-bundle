<?php declare(strict_types=1);
namespace Imatic\Bundle\UserBundle\Data\Query\User;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\QueryBuilder;
use Imatic\Bundle\DataBundle\Data\Driver\DoctrineORM\QueryObjectInterface;
use Imatic\Bundle\DataBundle\Data\Query\DisplayCriteria\FilterableQueryObjectInterface;
use Imatic\Bundle\DataBundle\Data\Query\DisplayCriteria\SortableQueryObjectInterface;

class UserListQuery implements QueryObjectInterface, SortableQueryObjectInterface, FilterableQueryObjectInterface
{
    public function __construct(
        private string $class
    )
    {
    }

    public function build(EntityManager $em): QueryBuilder
    {
        return (new QueryBuilder($em))
            ->select('u')
            ->from($this->class, 'u');
    }

    public function getSorterMap(): array
    {
        return [
            'username' => 'u.username',
            'enabled' => 'u.enabled',
            'email' => 'u.email',
            'lastLogin' => 'u.lastLogin',
        ];
    }

    public function getDefaultSort(): array
    {
        return ['username' => 'ASC'];
    }

    public function getFilterMap(): array
    {
        return [
            'username' => 'u.username',
        ];
    }
}
