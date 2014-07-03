<?php

namespace Tdom\AdminBundle\Admin;

use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Show\ShowMapper;
use Symfony\Component\Security\Core\Encoder\MessageDigestPasswordEncoder;
use Symfony\Component\Intl\Intl;
use Sonata\AdminBundle\Route\RouteCollection;

class UserAdmin extends Admin
{

    /**
     * @param DatagridMapper $datagridMapper
     */
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $countries = Intl::getRegionBundle()->getCountryNames();

        $countries['KO'] = "Kosovo";

        $datagridMapper
            ->add('nickName')
            ->add('email')
            ->add('country',  null, array('label' => 'Country'), 'choice', array(
                'choices' => $countries
            ))
        ;
    }

    /**
     * @param ListMapper $listMapper
     */
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->add('nickName', "string", array("template" => "TdomAdminBundle:User:view-link.html.twig"))
            ->add('fullName')
            ->add('email')
            ->add("avatar", "string", array("template" => "TdomAdminBundle:User:avatar.html.twig"))
            ->add('country', null, array("template" => "TdomAdminBundle:User:country.html.twig"))
            ->add('city')
            ->add('_action', 'actions', array(
                'actions' => array(
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
        $countries = Intl::getRegionBundle()->getCountryNames();

        $countries['KO'] = "Kosovo";

        $formMapper
            ->add('nickName')
            ->add('fullName')
            ->add('email')
            ->add('password','password', array('required' => false ))
            ->add('birthDay','date', array(
                'years' => range(date('Y') -60, date('Y')),
                'required'    => true,
                'label' => 'Birthday'
            ))
            ->add('country', 'choice', array(
                'choices' => $countries,
                'preferred_choices' => array(
                    'FI', // Finland
                    'FR', //France
                    'UK', // United Kingdom
                    'US', // United States
                ),
            ))
            ->add('city')
            ->add('file', 'file', array('required' => false ))
            ->add('enabled')
            ->add('roles', 'collection', array(
                'type' => "choice",
                'label' => 'Role User',
                'options'  => array(
                    'choices'  => array(
                        'ROLE_USER' => 'Role User',
                        'ROLE_SUPER_ADMIN' => 'Role Super Admin'
                    ),
                    'required'    => false,
                    'label' => 'User role'
                )))
        ;
    }

    /**
     * @param ShowMapper $showMapper
     */
    protected function configureShowFields(ShowMapper $showMapper)
    {
        $showMapper
            ->add('nickName')
            ->add('fullName')
            ->add('email')
            ->add('country')
            ->add('city')
            ->add('enabled')
            ->add('lastLogin')
            ->add('locked')
            ->add('expired')
            ->add('expiresAt')
            ->add('roles')
        ;
    }

    protected function configureRoutes(RouteCollection $collection) {
        $collection->remove('show');
    }

    public function prePersist($user) {
        $user->setPlainPassword($user->getPassword());
    }

    public function preUpdate($user) {
        if($user->getPassword())
            $user->setPlainPassword($user->getPassword());
        else {
            $user->unsetPassword();
        }
    }
}
