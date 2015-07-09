<?php

namespace Imatic\Bundle\UserBundle\Controller;

use Imatic\Bundle\ControllerBundle\Controller\Api\ApiTrait;
use Imatic\Bundle\DataBundle\Data\Command\CommandResultInterface;
use Imatic\Bundle\UserBundle\Data\Query\User\UserListQuery;
use Imatic\Bundle\UserBundle\Data\Query\User\UserQuery;
use Imatic\Bundle\UserBundle\Data\Filter\User\UserFilter;
use Imatic\Bundle\UserBundle\Model\UserInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration as Config;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;

/**
 * @Config\Route("/user")
 * @Config\Security("has_role('ROLE_IMATIC_USER_USER_ADMIN')")
 */
class UserController implements ContainerAwareInterface
{
    use ContainerAwareTrait;
    use ApiTrait;

    /**
     * @Config\Route("")
     * @Config\Method("GET")
     */
    public function listAction()
    {
        return $this
            ->listing(new UserListQuery($this->container->getParameter('imatic_user.entity.user.class')))
            ->filter('imatic_user.user')
            ->setTemplateName('ImaticUserBundle:User:list.html.twig')
            ->getResponse();
    }

    /**
     * @Config\Route("/{id}", requirements={"id"="\d+"})
     * @Config\Method("GET")
     * @Config\Template()
     */
    public function showAction($id)
    {
        return $this
            ->show(new UserQuery($id, $this->container->getParameter('imatic_user.entity.user.class')))
            ->setTemplateName('ImaticUserBundle:User:show.html.twig')
            ->getResponse();
    }

    /**
     * @Config\Route("/{id}/edit", requirements={"id"="\d+"})
     * @Config\Method({"GET", "PUT"})
     */
    public function editAction($id)
    {
        return $this
            ->form('imatic_user_user')
            ->commandName('imatic.user.edit')
            ->edit(new UserQuery($id, $this->container->getParameter('imatic_user.entity.user.class')))
            ->successRedirect('imatic_user_user_show', ['id' => $id])
            ->setTemplateName('ImaticUserBundle:User:edit.html.twig')
            ->getResponse();
    }

    /**
     * @Config\Route("/create")
     * @Config\Method({"GET", "POST"})
     */
    public function createAction()
    {
        return $this
            ->form('imatic_user_user')
            ->commandName('imatic.user.create')
            ->successRedirect('imatic_user_user_show', function (CommandResultInterface $result, UserInterface $user) {
                return ['id' => $user->getId()];
            })
            ->setTemplateName('ImaticUserBundle:User:edit.html.twig')
            ->getResponse();
    }

    /**
     * @Config\Route("/{id}/delete", requirements={"id"="\d+"})
     * @Config\Method("DELETE")
     */
    public function deleteAction($id)
    {
        return $this
            ->command('imatic.user.delete', ['user' => $id])
            ->redirect('imatic_user_user_list')
            ->getResponse();
    }
}
