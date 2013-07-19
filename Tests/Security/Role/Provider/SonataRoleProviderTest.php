<?php
namespace Imatic\Bundle\UserBundle\Tests\Security\Role\Provider;

use Imatic\Bundle\UserBundle\Security\Role\Provider\SonataRoleProvider;
use Imatic\Bundle\UserBundle\Security\Role\SonataRole;
use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Admin\Pool;
use Sonata\AdminBundle\Security\Handler\SecurityHandlerInterface;

class SonataRoleProviderTest extends \PHPUnit_Framework_TestCase
{
    /** @var SonataRoleProvider */
    private $roleProvider;

    /**
     * {@inheritDoc}
     */
    protected function setUp()
    {
        $this->roleProvider = new SonataRoleProvider($this->createPoolMock());
    }

    public function testGetRoles()
    {
        $this->assertEquals(
            [
                new SonataRole('Imatic', 'User', 'tests', 'Security_Role_Provider_AdminMock', 'EDIT', 'ROLE_A_EDIT'),
                new SonataRole('Imatic', 'User', 'tests', 'Security_Role_Provider_AdminMock', 'LIST', 'ROLE_A_LIST'),
                new SonataRole('Imatic', 'User', 'tests', 'Security_Role_Provider_AdminMock', 'EDIT', 'ROLE_B_EDIT'),
                new SonataRole('Imatic', 'User', 'tests', 'Security_Role_Provider_AdminMock', 'LIST', 'ROLE_B_LIST')
            ],
            $this->roleProvider->getRoles()
        );
    }

    /**
     * @return Pool|\PHPUnit_Framework_MockObject_MockObject
     */
    private function createPoolMock()
    {
        $poolMock = $this
            ->getMockBuilder('Sonata\AdminBundle\Admin\Pool')
            ->disableOriginalConstructor()
            ->getMock()
        ;
        $poolMock
            ->expects($this->any())
            ->method('getAdminServiceIds')
            ->will($this->returnValue(['admin_a', 'admin_b']))
        ;
        $poolMock
            ->expects($this->any())
            ->method('getInstance')
            ->will($this->returnValueMap([
                ['admin_a', $this->createAdminMock('A')],
                ['admin_b', $this->createAdminMock('B')]
            ]))
        ;

        return $poolMock;
    }

    /**
     * @param string $name
     * @return AdminMock|\PHPUnit_Framework_MockObject_MockObject
     */
    private function createAdminMock($name)
    {
        return new AdminMock($this->createSecurityHandlerMock($name), ['EDIT' => '', 'LIST' => '']);
    }

    /**
     * @param string $name
     * @return SecurityHandlerInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private function createSecurityHandlerMock($name)
    {
        $securityHandlerMock = $this->getMock('Sonata\AdminBundle\Security\Handler\SecurityHandlerInterface');
        $securityHandlerMock
            ->expects($this->any())
            ->method('getBaseRole')
            ->will($this->returnValue(sprintf('ROLE_%s_%%s', $name)))
        ;

        return $securityHandlerMock;
    }
}

class AdminMock extends Admin
{
    /**
     * @param SecurityHandlerInterface $securityHandler
     * @param array $securityInformation
     */
    public function __construct(SecurityHandlerInterface $securityHandler, array $securityInformation)
    {
        $this->securityHandler = $securityHandler;
        $this->securityInformation = $securityInformation;
    }

    /**
     * @return SecurityHandlerInterface
     */
    public function getSecurityHandler()
    {
        return $this->securityHandler;
    }

    /**
     * @return array
     */
    public function getSecurityInformation()
    {
        return $this->securityInformation;
    }
}