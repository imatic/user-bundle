<?php
namespace Imatic\Bundle\UserBundle\Admin;

use FOS\UserBundle\Model\UserManagerInterface;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Route\RouteCollection;
use Sonata\AdminBundle\Show\ShowMapper;

/**
 * {@inheritDoc}
 */
class UserAdmin extends Admin
{
    /** {@inheritDoc} */
    protected $translationDomain = 'ImaticUserBundleUser';

    /** {@inheritDoc} */
    protected $formOptions = ['validation_groups' => 'Profile'];

    /** @var UserManagerInterface */
    private $userManager;

    /**
     * {@inheritDoc}
     */
    public function configure()
    {
        $this->setTemplate('roles', 'ImaticUserBundle:Admin:user_roles.html.twig');
        $this->securityInformation += ['ROLES' => ['ROLES']];
    }

    /**
     * @return UserManagerInterface
     */
    public function getUserManager()
    {
        return $this->userManager;
    }

    /**
     * @param UserManagerInterface $userManager
     */
    public function setUserManager(UserManagerInterface $userManager)
    {
        $this->userManager = $userManager;
    }

    /**
     * @param  object $user
     * @return void
     * @TODO: use doctrine lifecycle event
     */
    public function preUpdate($user)
    {
        $userManager = $this->getUserManager();
        $userManager->updateCanonicalFields($user);
        $userManager->updatePassword($user);
    }

    /**
     * {@inheritDoc}
     */
    protected function configureRoutes(RouteCollection $routeCollection)
    {
        $routeCollection->add('roles', '{id}/roles');
    }

    /**
     * {@inheritDoc}
     */
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->with($this->trans('Group personal'), ['collapsed' => false])
                ->add('username')
                ->add('plainPassword', 'password', ['label' => 'Password', 'required' => false])
                ->add('email')
            ->end()
            ->with($this->trans('Group access'), ['collapsed' => true])
                ->add('enabled', null, ['required' => false, 'label_render' => false])
                ->add('groups', null, ['expanded' => true, 'multiple' => true])
            ->end();
    }

    /**
     * {@inheritDoc}
     */
    protected function configureShowFields(ShowMapper $showMapper)
    {
        $showMapper
            ->add('username')
            ->add('email')
            ->add('enabled')
            ->add('lastLogin');
    }

    /**
     * {@inheritDoc}
     */
    protected function configureDatagridFilters(DatagridMapper $dataGridMapper)
    {
        $dataGridMapper
            ->add('username')
            ->add('email')
            ->add('enabled');
    }

    /**
     * {@inheritDoc}
     */
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->addIdentifier('username')
            ->add('email')
            ->add('lastLogin')
            ->add('enabled', null, ['editable' => true])
            ->add('_action', 'actions', ['actions' => [
                'view' => [],
                'edit' => [],
                'roles' => ['template' => 'ImaticUserBundle:Admin:Field/roles.html.twig'],
                'impersonating' => ['template' => 'ImaticUserBundle:Admin:Field/impersonating.html.twig']
            ]]);
    }
}