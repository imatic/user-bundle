<?php declare(strict_types=1);
namespace Imatic\Bundle\UserBundle\DataFixtures\ORM;

use AppUserBundle\Entity\Group;
use AppUserBundle\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Imatic\Bundle\UserBundle\Manager\UserManager;

class LoadDefaultUsers extends Fixture
{
    private UserManager $userManager;
    private array $roleHierarchy;

    public function __construct(UserManager $userManager, array $roleHierarchy)
    {
        $this->userManager = $userManager;
        $this->roleHierarchy = $roleHierarchy;
    }

    public function load(ObjectManager $manager): void
    {
        $this->loadDefaultUsersAndGroups($manager);
    }

    public function loadDefaultUsersAndGroups(ObjectManager $manager): void
    {
        $adminRoles = \array_keys($this->roleHierarchy);
        $userRoles = [User::ROLE_DEFAULT];

        $adminGroup = $this->createGroup('Administrators', $adminRoles);
        $userGroup = $this->createGroup('Users', $userRoles);

        $adminUser = $this->createUser('admin', 'adminpass123', 'admin@example.com', [$adminGroup]);
        $manager->persist($adminUser);

        $userUser = $this->createUser('user', 'userpass123', 'user@example.com', [$userGroup]);
        $manager->persist($userUser);

        $manager->flush();
    }

    protected function createUser(string $username, string $password, string $email, array $groups = [], array $roles = [], bool $enabled = true): User
    {
        $user = new User();
        $user->setUsername($username);
        $user->setEmail($email);
        $user->setPlainPassword($password);
        $user->setEnabled($enabled);

        // Add roles
        $user->setRoles($roles);

        // Add groups
        foreach ($groups as $group) {
            $user->addGroup($group);
        }

        $this->userManager->updatePassword($user);

        return $user;
    }

    protected function createGroup(string $name, array $roles = []): Group
    {
        $group = new Group();
        $group->setName($name);

        foreach ($roles as $role) {
            $group->addRole($role);
        }

        return $group;
    }
}
