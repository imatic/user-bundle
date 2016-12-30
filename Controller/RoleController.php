<?php

namespace Imatic\Bundle\UserBundle\Controller;

use FOS\UserBundle\Model\GroupInterface;
use FOS\UserBundle\Model\GroupManagerInterface;
use FOS\UserBundle\Model\UserInterface;
use FOS\UserBundle\Model\UserManagerInterface;
use Imatic\Bundle\UserBundle\Security\Role\Provider\ChainRoleProvider;
use Sensio\Bundle\FrameworkExtraBundle\Configuration as Config;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Security\Core\SecurityContext;

/**
 * @Config\Route("/imatic/user/role")
 * @Config\Security("has_role('ROLE_IMATIC_USER_USER_ADMIN')")
 */
class RoleController extends Controller
{
    const TYPE_USER = 'user';

    const TYPE_GROUP = 'group';

    /**
     * @param $type
     * @param int $id
     *
     * @return array
     * @Sensio\Bundle\FrameworkExtraBundle\Configuration\Route(
     *     path="/display/{type}/{id}",
     *     requirements={"type"="user|group", "id"="\d+"}
     * )
     * @Sensio\Bundle\FrameworkExtraBundle\Configuration\Template
     */
    public function displayAction($type, $id)
    {
        $roleMap = [];

        foreach ($this->getRoleProvider()->getRoles() as $role) {
            $roleMap[$role->getType()][$role->getDomain()][$role->getLabel()][] = $role;
        }

        return [
            'object' => $this->findObject($type, $id),
            'objectType' => $type,
            'roleMap' => $roleMap,
        ];
    }

    /**
     * @param Request $request
     * @param string  $type
     * @param int     $id
     * @param string  $role
     *
     * @return Response
     *
     * @throws AccessDeniedException
     * @Sensio\Bundle\FrameworkExtraBundle\Configuration\Route(
     *     path="/switch/{type}/{id}/{role}",
     *     requirements={"type"="user|group", "id"="\d+"}
     * )
     */
    public function switchAction(Request $request, $type, $id, $role)
    {
        if (
            !$this->getSecurityContext()->isGranted(sprintf('ROLE_IMATIC_USER_ADMIN_%s_ROLE', strtoupper($type)))
            && !$this->getSecurityContext()->isGranted('ROLE_SUPER_ADMIN')
        ) {
            throw new AccessDeniedException();
        }

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
        return $this->get('imatic_user.role_provider');
    }

    /**
     * @param string $type
     *
     * @return UserManagerInterface|GroupManagerInterface
     */
    private function getManager($type)
    {
        return $this->get(sprintf('fos_user.%s_manager', $type));
    }

    /**
     * @return SecurityContext
     */
    private function getSecurityContext()
    {
        return $this->get('security.context');
    }

    /**
     * @param string $type
     * @param int    $id
     *
     * @return UserInterface|GroupInterface
     */
    private function findObject($type, $id)
    {
        $manager = $this->getManager($type);
        $object = $type == static::TYPE_USER
            ? $manager->findUserBy(['id' => $id])
            : $manager->findGroupBy(['id' => $id]);

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
