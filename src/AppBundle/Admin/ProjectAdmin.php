<?php

namespace AppBundle\Admin;

use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Show\ShowMapper;

class ProjectAdmin extends Admin
{
    protected $baseRouteName = 'sonata_project';
    protected $baseRoutePattern = 'project';

    // Fields to be shown on create/edit forms
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->add('name', 'text', array('label' => 'Project Name'))
            ->add('githubName', 'text', array('label' => 'Name on github as <user>/<repo> string'))
            ->add('owner')
            ->add('needsUpdate', null, array('required' => false))
            ->add('tagPrefix', null, array('required' => false))
        ;
    }

    // Fields to be shown on filter forms
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('githubName')
            ->add('owner')
            ->add('name')
        ;
    }

    // Fields to be shown on lists
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->add('owner')
            ->add('name')
            ->add('githubName')
            ->add('_action', 'actions', array(
                'actions' => array(
                    'show' => array(),
                    'edit' => array(),
                    'delete' => array(),
                )
            ))
        ;
    }

    /**
     * @param ShowMapper $showMapper
     */
    protected function configureShowFields(ShowMapper $showMapper)
    {
        $showMapper
            ->add('id')
            ->add('createdAt')
            ->add('updatedAt')
            ->add('owner')
            ->add('name')
            ->add('githubName')
            ->add('needsUpdate')
            ->add('tagPrefix')
            ->add('masterVersion')
            ->add('versions', 'array')
            ->add('lastLogs', 'array')
        ;
    }
}
