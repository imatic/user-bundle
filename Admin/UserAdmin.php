<?php
namespace Imatic\Bundle\UserBundle\Admin;

use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Form\FormMapper;
use FOS\UserBundle\Model\UserManagerInterface;

class UserAdmin extends Admin
{
    /**
     * @var UserManagerInterface
     */
    protected $userManager;

    protected $formOptions = array(
        'validation_groups' => 'Profile'
    );

    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->add('fullname')
            ->add('username')
            ->add('email')
            ->add('enabled', null, array('required' => false))
            ->add('roles', 'sonata_security_roles', array(
            'expanded' => true,
            'multiple' => true,
            'required' => false,
            'translation_domain' => 'roles'
        ));
    }

    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('fullname')
            ->add('username')
            ->add('email')
            ->add('enabled');
    }

    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->add('fullname')
            ->addIdentifier('username')
            ->add('email')
            ->add('enabled');
    }

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
