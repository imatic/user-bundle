<?php declare(strict_types=1);
namespace Imatic\Bundle\UserBundle\Model;

/**
 * Group interface.
 */
interface GroupInterface
{
    public function addRole(string $role): static;

    public function getId(): mixed;

    public function getName(): string;

    public function hasRole(string $role): bool;

    public function getRoles(): array;

    public function removeRole(string $role): static;

    public function setName(string $name): static;

    public function setRoles(array $roles): static;
}
