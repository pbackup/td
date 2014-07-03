<?php

namespace Tdom\AdminBundle\Admin;

use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Show\ShowMapper;

class GameAdmin extends Admin
{
    /**
     * @param DatagridMapper $datagridMapper
     */
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            //->add('id')
            ->add('name')
            ->add('category')
        ;
    }

    /**
     * @param ListMapper $listMapper
     */
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->add('name')
            ->add('category')
            ->add("users", "string", array("template" => "TdomAdminBundle:Game:users.html.twig"))
            ->add('userBy', null, array("label" => "Created by"))
            ->add('isActive' , null, array('label' => 'Active'))
            ->add('isSystem', null, array('label' => 'System'))
            ->add('createdAt', null, array('label' => 'Created'))
            ->add('updatedAt', null, array('label' => 'Updated'))
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
     * @param FormMapper $formMapper
     */
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
          //  ->add('id')
            ->add('name')
            ->add("category")
            ->add('isActive', null, array('label' => 'Active'))
        ;
    }

    /**
     * @param ShowMapper $showMapper
     */
    protected function configureShowFields(ShowMapper $showMapper)
    {
        $showMapper
            ->add('id')
            ->add('name')
            ->add('isActive')
            ->add('isSystem')
            ->add('isDeleted')
            ->add('createdAt')
            ->add('updatedAt')
        ;
    }
}
