<?php
namespace Tdom\GameBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Doctrine\ORM\EntityRepository;

class FilterFormType extends AbstractType {

    public function buildForm(FormBuilderInterface $builder, array $options) {

        $types = array("Group", "Event", "Tournament");

        $builder->add('category', 'entity',
                    array(
                        'class' => 'TdomAdminBundle:Category',
                        'property' => 'name',
                        'query_builder' => function(EntityRepository $er) {
                                return $er->createQueryBuilder('c')
                                    ->orderBy('c.name', 'ASC');
                            },
                        'empty_value' => 'All Categories',
                    ))
                ->add('games', 'entity',
                    array(
                        'class' => 'TdomAdminBundle:Game',
                        'query_builder' => function(EntityRepository $er) use ($options) {
                                $qb = $er->createQueryBuilder('g')
                                    ->leftJoin('g.category', 'c')
                                    ->where('g.isActive = :isActive')->setParameter('isActive', true)
                                    ->andWhere('g.isDeleted = :isDeleted')->setParameter('isDeleted', false);
                                if ($options['category'])
                                    $qb->andWhere('c.id =:catId')->setParameter('catId', $options['category']);
                                    $qb->orderBy('g.name', 'ASC');
                                return $qb;
                            },
                        'multiple' => true,
                    ))
                ->add('type', 'choice',
                array('mapped' => false,
                    'empty_value' => 'Type',
                    'choices' => $types
                ))
        ;
    }

    public function getName() {
        return 'tdom_filter_game';
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver) {
        $resolver->setDefaults(array(
            'data_class' => 'Tdom\GameBundle\Model\FindGamer',
            'category' => null
        ));
    }
} 