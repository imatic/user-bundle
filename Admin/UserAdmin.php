<?php
namespace Imatic\Bundle\UserBundle\Admin;

use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Show\ShowMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Form\FormMapper;
use FOS\UserBundle\Model\UserManagerInterface;

class UserAdmin extends Admin
{
    /**
     * @var UserManagerInterface
     */
    protected $userManager;

    protected $translationDomain = 'ImaticUserBundleUser';

    protected $formOptions = array(
        'validation_groups' => 'Profile'
    );

    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->with($this->trans('Group personal'), array('collapsed' => false))
            ->add('fullname')
            ->add('username')
            ->add('plainPassword', 'password', array('label' => 'Password', 'required' => false))
            ->add('email')
            ->end()
            ->with($this->trans('Group access'), array('collapsed' => true))
            ->add('enabled', null, array('required' => false, 'label_render' => false))
            ->add('groups', null, array('expanded' => true, 'multiple' => true))
            ->add('roles', 'sonata_security_roles', array('expanded' => true, 'multiple' => true, 'required' => false))
            ->end();
    }

    protected function configureShowFields(ShowMapper $showMapper)
    {
        $showMapper
            ->add('fullname')
            ->add('username')
            ->add('email')
            ->add('enabled')
            ->add('lastLogin')
            ->add('roles', null, array('template' => 'ImaticUserBundle:Admin:Field/roles.html.twig'));
    }

    protected function configureDatagridFilters(DatagridMapper $dataGridMapper)
    {
        $dataGridMapper
            ->add('fullname')
            ->add('username')
            ->add('email')
            ->add('enabled');
    }

    protected function configureListFields(ListMapper $listMapper)
    {
        $actions = array(
            'view' => array(),
            'edit' => array(),
        );
        if ($this->isGranted('ROLE_ALLOWED_TO_SWITCH')) {
            $actions['impersonating'] = array('template' => 'ImaticUserBundle:Admin:Field/impersonating.html.twig');
        }

        $listMapper
            ->add('fullname')
            ->addIdentifier('username')
            ->add('email')
            ->add('lastLogin')
            ->add('enabled', null, array('editable' => true))
            ->add('_action', 'actions', array('actions' => $actions)
        );
    }

    /**
     * @todo: use doctrine lifecycle event
     * @param  object $user
     * @return void
     */
    public function preUpdate($user)
    {
        $this->getUserManager()->updateCanonicalFields($user);
        $this->getUserManager()->updatePassword($user);
    }

    /**
     * @param UserManagerInterface $userManager
     */
    public function setUserManager(UserManagerInterface $userManager)
    {
        $this->userManager = $userManager;
    }

    /**
     * @return UserManagerInterface
     */
    public function getUserManager()
    {
        return $this->userManager;
    }
}
