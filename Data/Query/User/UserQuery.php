<?php declare(strict_types=1);
namespace Imatic\Bundle\UserBundle\Data\Query\User;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\QueryBuilder;
use Imatic\Bundle\DataBundle\Data\Driver\DoctrineORM\QueryObjectInterface;
use Imatic\Bundle\DataBundle\Data\Query\SingleResultQueryObjectInterface;

class UserQuery implements QueryObjectInterface, SingleResultQueryObjectInterface
{
    public function __construct(
        private int $id, 
        private string $class
    )
    {
    }

    public function build(EntityManager $em): QueryBuilder
    {
        return (new QueryBuilder($em))
            ->select('u')
            ->from($this->class, 'u')
            ->where('u.id = :id')
            ->setParameter('id', $this->id);
    }
}
