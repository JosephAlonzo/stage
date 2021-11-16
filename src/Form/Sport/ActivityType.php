<?php
namespace App\Form\Sport;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Doctrine\ORM\EntityRepository;

use App\Entity\Sport\Activity;
use App\Entity\Sport\Educator;

use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\ColorType; 
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

class ActivityType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, array('required' => true, 'label' => 'entity.activity.table.name'))
            ->add('educator', EntityType::class, [
                'label' => 'entity.activity.table.educator',
                'class' => Educator::class,
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('e')
                        ->join('e.user', 'u')
                        ->orderBy('u.firstName', 'ASC');
                },
                'attr' => array('class'=>'form-control select2'),
                'required' => true,
                'multiple' => true,
                'expanded' => false,
                'choice_label' => function ($educator) {
                    return $educator->getUser()->getFirstName() . ' ' . $educator->getUser()->getLastName();
                },
            ])
            ->add('color', ColorType::class, array('required' => true, 'label' => 'entity.activity.table.color'))
            ->add('maxPlaces', NumberType::class, array('required' => true, 'label' => 'entity.activity.table.maxPlaces'))
            ->remove('deletedAt')
            ->remove('createdAt')
            ->remove('updatedAt')
            ->add('valider', SubmitType::class, array('attr'=>array('class'=>'btn btn-primary')))
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Activity::class,
        ]);
    }
}