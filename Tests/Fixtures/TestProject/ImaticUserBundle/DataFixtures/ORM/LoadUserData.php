<?php declare(strict_types=1);
namespace Imatic\Bundle\UserBundle\Tests\Fixtures\TestProject\ImaticUserBundle\DataFixtures\ORM;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Imatic\Bundle\UserBundle\Tests\Fixtures\TestProject\ImaticUserBundle\Entity\User;

/**
 * @author Miloslav Nenadal <miloslav.nenadal@imatic.cz>
 */
class LoadUserData extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $adam = new User();
        $adam->setUsername('adam');
        $adam->setEmail('adam@example.com');
        $adam->setPassword('1234');
        $adam->setSalt('');
        $adam->setEnabled(true);
        $adam->setRoles([
            'ROLE_SUPER_ADMIN',
            'ROLE_IMATIC_USER_USER_ADMIN',
        ]);
        $manager->persist($adam);

        $eva = new User();
        $eva->setUsername('eva');
        $eva->setEmail('eva@example.com');
        $eva->setPassword('1234');
        $eva->setSalt('');
        $eva->setEnabled(true);
        $manager->persist($eva);

        $manager->flush();
    }
}
