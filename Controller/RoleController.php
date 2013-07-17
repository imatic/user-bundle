<?php
namespace Imatic\Bundle\UserBundle\Controller;

use FOS\UserBundle\Model\UserInterface;
use FOS\UserBundle\Model\UserManagerInterface;
use FOS\UserBundle\Model\GroupInterface;
use FOS\UserBundle\Model\GroupManagerInterface;
use Imatic\Bundle\UserBundle\Security\Role\ChainRoleProvider;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * @Route("/imatic/user/role")
 */
class RoleController extends Controller
{
    const TYPE_USER = 'user';
    const TYPE_GROUP = 'group';

    /**
     * @param string $user
     * @param int $id
     * @return array
     * @Template
     */
    public function displayAction($type, $id)
    {
        $roleMap = [];

        foreach ($this->getRoleProvider()->getRoles() as $role) {
            $roleMap[$role->getType()][$role->getDomain()][] = $role;
        }

        return [
            'object' => $this->findObject($type, $id),
            'objectType' => $type,
            'roleMap' => $roleMap
        ];
    }

    /**
     * @param Request $request
     * @param string $type
     * @param int $id
     * @param string $role
     * @param bool $enabled
     * @return Response
     * @Route("/switch/{type}/{id}/{role}", requirements={"type"="user|group", "id"="\d+"})
     */
    public function switchAction(Request $request, $type, $id, $role)
    {
        $object = $this->findObject($type, $id);

        if ($request->get('allowed')) {
            $object->addRole($role);
        } else {
            $object->removeRole($role);
        }

        $this->updateObject($object);

        return new Response();
    }

    /**
     * @return ChainRoleProvider
     */
    private function getRoleProvider()
    {
        return $this->get('imatic.role_provider');
    }

    /**
     * @param string $type
     * @return UserManagerInterface|GroupManagerInterface
     */
    private function getManager($type)
    {
        return $this->get(sprintf('fos_user.%s_manager', $type));
    }

    /**
     * @param string $type
     * @param int $id
     * @return UserInterface|GroupInterface
     */
    private function findObject($type, $id)
    {
        $manager = $this->getManager($type);
        $object = $type == static::TYPE_USER
            ? $manager->findUserBy(['id' => $id])
            : $manager->findGroupBy(['id' => $id])
        ;

        if (!$object) {
            throw $this->createNotFoundException(sprintf('The %s with ID "%d" was not found.', $type, $id));
        }

        return $object;
    }

    /**
     * @param UserInterface|GroupInterface $object
     */
    private function updateObject($object)
    {
        if ($object instanceof UserInterface) {
            $this->getManager(static::TYPE_USER)->updateUser($object);
        } else {
            $this->getManager(static::TYPE_GROUP)->updateGroup($object);
        }
    }
}