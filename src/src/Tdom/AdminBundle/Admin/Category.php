<?php

namespace Tdom\AdminBundle\Admin;

use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Show\ShowMapper;

class Category extends Admin
{
    /**
     * @param DatagridMapper $datagridMapper
     */
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('name')
        ;
    }

    /**,
     * @param ListMapper $listMapper
     */
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->add('id')
            ->add('orderBy', null, array('label' => 'Order'))
            ->add('name')
            ->add("color", "string", array("template" => "TdomAdminBundle:Category:color.html.twig"))
            ->add("icon", "string", array("template" => "TdomAdminBundle:Category:icon.html.twig"))
            ->add('isActive', null, array('label' => 'Active'))
            ->add('createdAt', null, array('label' => 'Created'))
            ->add('updatedAt',  null, array('label' => 'Updated'))
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
            ->add('name')
            ->add('color')
            ->add('file', 'file',array('required' => false))
            ->add('orderBy', null, array("label" => "Order"))
            ->add('isActive', null, array("label" => 'Active'))
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

        ;
    }
}
