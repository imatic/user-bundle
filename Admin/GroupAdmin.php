<?php
namespace Imatic\Bundle\UserBundle\Admin;

use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Show\ShowMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Form\FormMapper;

class GroupAdmin extends Admin
{
    protected $translationDomain = 'ImaticUserBundleGroup';

    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->add('name')
            ->add('roles', 'sonata_security_roles', array(
            'expanded' => true,
            'multiple' => true,
            'required' => false
        ));
    }

    protected function configureShowFields(ShowMapper $showMapper)
    {
        $showMapper
            ->add('name')
            ->add('roles', null, array('template' => 'ImaticUserBundle:Admin:Field/roles.html.twig'));
    }

    protected function configureDatagridFilters(DatagridMapper $dataGridMapper)
    {
        $dataGridMapper
            ->add('name');
    }

    protected function configureListFields(ListMapper $listMapper)
    {
        $actions = array(
            'view' => array(),
            'edit' => array(),
        );

        $listMapper
            ->addIdentifier('name')
            ->add('_action', 'actions', array('actions' => $actions));
    }
}
