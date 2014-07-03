<?php

namespace Tdom\GameBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class GameFormType  extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options) {

        $builder->add('name')
                ->add('category');

    }

    public function getName() {
        return 'tdom_user_game';
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver) {
        $resolver->setDefaults(array(
            'data_class' => 'Tdom\AdminBundle\Entity\Game'
        ));
    }
}