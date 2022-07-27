<?php declare(strict_types=1);
namespace Imatic\Bundle\UserBundle\Data\Query\User;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\QueryBuilder;
use Imatic\Bundle\DataBundle\Data\Driver\DoctrineORM\QueryObjectInterface;
use Imatic\Bundle\DataBundle\Data\Query\SingleResultQueryObjectInterface;

class UserQuery implements QueryObjectInterface, SingleResultQueryObjectInterface
{
    /**
     * @var string
     */
    private $class;

    /**
     * @var int
     */
    private $id;

    public function __construct($id, $class)
    {
        $this->class = $class;
        $this->id = $id;
    }

    /**
     * @param EntityManager $em
     *
     * @return QueryBuilder
     */
    public function build(EntityManager $em): QueryBuilder
    {
        return (new QueryBuilder($em))
            ->select('u')
            ->from($this->class, 'u')
            ->where('u.id = :id')
            ->setParameter('id', $this->id);
    }
}
