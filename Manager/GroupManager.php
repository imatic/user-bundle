<?php
declare(strict_types=1);
namespace Imatic\Bundle\UserBundle\Manager;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ObjectRepository;
use Imatic\Bundle\UserBundle\Model\GroupInterface;

class GroupManager
{
    private EntityManagerInterface $om;
    private string $groupClass;

    public function __construct(EntityManagerInterface $om, string $groupClass)
    {
        $this->om = $om;
        $this->groupClass = $groupClass;
    }

    private function getRepository(): ObjectRepository
    {
        return $this->om->getRepository($this->groupClass);
    }

    public function findGroupBy(array $criteria)
    {
        return $this->getRepository()->findOneBy($criteria);
    }

    public function updateGroup(GroupInterface $group, bool $andFlush = true): void
    {
        $this->om->persist($group);
        if ($andFlush) {
            $this->om->flush();
        }
    }
}
