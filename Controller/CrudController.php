<?php
namespace Imatic\Bundle\UserBundle\Controller;

use Sonata\AdminBundle\Controller\CRUDController as BaseCrudController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class CrudController extends BaseCrudController
{
    /**
     * @param $id
     * @return Response
     * @throws NotFoundHttpException
     * @throws AccessDeniedException
     */
    public function rolesAction($id)
    {
        if (!$object = $this->admin->getObject($id)) {
            throw $this->createNotFoundException();
        }

        if (!$this->admin->isGranted('ROLES', $object)) {
            throw new AccessDeniedException();
        }

        return $this->render($this->admin->getTemplate('roles'), [
            'action' => 'roles',
            'object' => $this->admin->getObject($id)
        ]);
    }
}