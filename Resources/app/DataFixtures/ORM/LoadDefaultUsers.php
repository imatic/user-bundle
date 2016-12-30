<?php

namespace Imatic\Bundle\UserBundle\DataFixtures\ORM;

use AppUserBundle\Entity\User;
use AppUserBundle\Entity\Group;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Bridge\Doctrine\Tests\Fixtures\ContainerAwareFixture;
use FOS\UserBundle\Model\UserManagerInterface;

class LoadDefaultUsers extends ContainerAwareFixture
{
    public function load(ObjectManager $manager)
    {
        $this->loadDefaultUsersAndGroups($manager);
    }

    public function loadDefaultUsersAndGroups(ObjectManager $manager)
    {
        /** @var $roleHierarchy array */
        $roleHierarchy = $this->container->getParameter('security.role_hierarchy.roles');

        $adminRoles = array_keys($roleHierarchy);
        $userRoles = array(User::ROLE_DEFAULT);

        $adminGroup = $this->createGroup('Administrators', $adminRoles);
        $userGroup = $this->createGroup('Users', $userRoles);

        $adminUser = $this->createUser('admin', 'adminpass123', 'admin@example.com', array($adminGroup));
        $manager->persist($adminUser);

        $userUser = $this->createUser('user', 'userpass123', 'user@example.com', array($userGroup));
        $manager->persist($userUser);

        $manager->flush();
    }

    /**
     * @param string $username
     * @param string $password
     * @param string $email
     * @param array  $groups
     * @param array  $roles
     * @param bool   $enabled
     *
     * @return User
     */
    protected function createUser($username, $password, $email, array $groups = array(), array $roles = array(), $enabled = true)
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

        // Encode user password
        /** @var $userManager UserManagerInterface */
        $userManager = $this->container->get('fos_user.user_manager');
        $userManager->updatePassword($user);

        return $user;
    }

    /**
     * @param string $name
     * @param array  $roles
     *
     * @return Group
     */
    protected function createGroup($name, array $roles = array())
    {
        $group = new Group();
        $group->setName($name);

        foreach ($roles as $role) {
            $group->addRole($role);
        }

        return $group;
    }
}
