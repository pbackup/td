<?php

namespace Tdom\UserBundle\Form;

use Symfony\Component\Form\FormBuilderInterface;
use FOS\UserBundle\Form\Type\ProfileFormType as BaseType;
use Symfony\Component\Intl\Intl;

class ProfileFormType extends BaseType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);

        $countries = Intl::getRegionBundle()->getCountryNames();

        $countries['KO'] = "Kosovo";

        $builder->add('fullName')
                ->add('birthDay','date', array(
                    'years' => range(date('Y') -60, date('Y')),
                    'required'    => true
                ))
                ->add('country', 'country', array(
                    'choices' => $countries,
                    'preferred_choices' => array(
                        'FI', // Finland
                        'FR', //France
                        'UK', // United Kingdom
                        'US', // United States
                    ),
                ))
                ->add('city', 'text', array('required'=> false))
                ->add('description', 'textarea', array('required'=> false))
                ->add('location', 'hidden',  array('required'=> true))
                ->add('username', "hidden")
                ->add('email','hidden', array("mapped" => false))
                ->add('file', "file",  array('required'=> false));
    }

    public function getName()
    {
        return 'tdom_user_profile';
    }
}