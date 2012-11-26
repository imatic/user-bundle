<?php
namespace Imatic\Bundle\UserBundle\DataFixtures\ORM;

use Doctrine\Common\Persistence\ObjectManager;
use App\Bundle\UserBundle\Entity\User;
use App\Bundle\UserBundle\Entity\Group;
use Symfony\Bridge\Doctrine\Tests\Fixtures\ContainerAwareFixture;
use FOS\UserBundle\Model\UserManagerInterface;

/**
 * Load all fixtures
 */
class LoadAll extends ContainerAwareFixture
{
    /**
     * @param ObjectManager $manager
     */
    public function load(ObjectManager $manager)
    {
        $this->loadDefaultUsersAndGroups($manager);
    }

    /**
     * @param ObjectManager $manager
     */
    public function loadDefaultUsersAndGroups(ObjectManager $manager)
    {
        /** @var $roleHierarchy array */
        $roleHierarchy = $this->container->getParameter('security.role_hierarchy.roles');

        $adminRoles = array_keys($roleHierarchy);
        $userRoles = array(User::ROLE_DEFAULT);

        $adminGroup = $this->createGroup('Administrators', 'System administrators', $adminRoles);
        $userGroup = $this->createGroup('Users', 'Standard users', $userRoles);

        $adminUser = $this->createUser('admin', 'adminpass123', 'admin@example.com', 'Admin', array($adminGroup));
        $manager->persist($adminUser);

        $userUser = $this->createUser('user', 'userpass123', 'user@example.com', 'User', array($userGroup));
        $manager->persist($userUser);

        $manager->flush();
    }

    /**
     * @param string $username
     * @param string $password
     * @param string $email
     * @param string $fullName
     * @param array $roles
     * @param array $groups
     * @param bool $enabled
     * @return User
     */
    protected function createUser($username, $password, $email, $fullName, array $groups = array(), array $roles = array(), $enabled = true)
    {
        $user = new User();
        $user->setUsername($username);
        $user->setFullname($fullName);
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
     * @param string $description
     * @param array $roles
     * @return Group
     */
    protected function createGroup($name, $description, array $roles = array())
    {
        $group = new Group();
        $group->setName($name);
        $group->setDescription($description);

        foreach ($roles as $role) {
            $group->addRole($role);
        }

        return $group;
    }
}
