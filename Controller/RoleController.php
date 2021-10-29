<?php declare(strict_types=1);
namespace Imatic\Bundle\UserBundle\Controller;

use Imatic\Bundle\UserBundle\Manager\GroupManager;
use Imatic\Bundle\UserBundle\Manager\UserManager;
use Imatic\Bundle\UserBundle\Model\GroupInterface;
use Imatic\Bundle\UserBundle\Model\UserInterface;
use Imatic\Bundle\UserBundle\Security\Role\Provider\RoleProviderInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration as Config;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

/**
 * @Config\Route("/imatic/user/role")
 * @Config\Security("has_role('ROLE_IMATIC_USER_USER_ADMIN')")
 */
class RoleController extends AbstractController
{
    const TYPE_USER = 'user';

    const TYPE_GROUP = 'group';

    /**
     * @param $type
     * @param int $id
     *
     * @Config\Route(
     *     path="/display/{type}/{id}",
     *     requirements={"type"="user|group", "id"="\d+"}
     * )
     * @Config\Template("@ImaticUser/Role/display.html.twig")
     */
    public function displayAction($type, $id, RoleProviderInterface $roleProvider)
    {
        $roleMap = [];

        foreach ($roleProvider->getRoles() as $role) {
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
     * @Config\Route(
     *     path="/switch/{type}/{id}/{role}",
     *     requirements={"type"="user|group", "id"="\d+"}
     * )
     */
    public function switchAction(Request $request, $type, $id, $role)
    {
        if (
            !$this->isGranted(\sprintf('ROLE_IMATIC_USER_ADMIN_%s_ROLE', \strtoupper($type)))
            && !$this->isGranted('ROLE_SUPER_ADMIN')
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
     * @param string $type
     *
     * @return UserManager|GroupManager
     */
    private function getManager($type)
    {
        return $this->get(\sprintf('imatic_user.manager.%s', $type));
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
        $object = $type === static::TYPE_USER
            ? $manager->findUserBy(['id' => $id])
            : $manager->findGroupBy(['id' => $id]);

        if (!$object) {
            throw $this->createNotFoundException(\sprintf('The %s with ID "%d" was not found.', $type, $id));
        }

        return $object;
    }

    /**
     * @param UserInterface|GroupInterface $object
     */
    private function updateObject($object): void
    {
        if ($object instanceof UserInterface) {
            $this->getManager(static::TYPE_USER)->updateUser($object);
        } else {
            $this->getManager(static::TYPE_GROUP)->updateGroup($object);
        }
    }

    public static function getSubscribedServices()
    {
        return \array_merge(
            [
                'imatic_user.manager.user' => UserManager::class,
                'imatic_user.manager.group' => GroupManager::class,
            ],
            parent::getSubscribedServices(),
        );
    }
}
