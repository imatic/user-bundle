<?php
namespace Imatic\Bundle\UserBundle\Admin;

use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Route\RouteCollection;
use Sonata\AdminBundle\Show\ShowMapper;

/**
 * Group admin.
 *
 * @author Stepan Koci <stepan.koci@imatic.cz>
 */
class GroupAdmin extends Admin
{
    /** {@inheritDoc} */
    protected $translationDomain = 'ImaticUserBundleGroup';

    /**
     * {@inheritDoc}
     */
    public function configure()
    {
        $this->setTemplate('roles', 'ImaticUserBundle:Admin:group_roles.html.twig');
        $this->securityInformation += ['ROLES' => ['ROLES']];
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
        $formMapper->add('name');
    }

    /**
     * {@inheritDoc}
     */
    protected function configureShowFields(ShowMapper $showMapper)
    {
        $showMapper->add('name')->add('roles');
    }

    /**
     * {@inheritDoc}
     */
    protected function configureDatagridFilters(DatagridMapper $dataGridMapper)
    {
        $dataGridMapper
            ->add('name');
    }

    /**
     * {@inheritDoc}
     */
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->addIdentifier('name')
            ->add('_action', 'actions', ['actions' => [
                'edit' => [],
                'roles' => ['template' => 'ImaticUserBundle:Admin:Field/roles.html.twig']
            ]]);
    }
}
