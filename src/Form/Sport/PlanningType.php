<?php
namespace App\Form\Sport;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

use Doctrine\ORM\EntityRepository;

use App\Entity\Sport\Planning;
use App\Entity\Sport\Activity;
use App\Entity\Sport\Educator;
use App\Entity\Sport\Place;

use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TimeType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;

class PlanningType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('startDate', DateType::class, array(
                'widget' => 'single_text',
                'input' => 'datetime',
                'html5' => false,
                'format' => 'dd/MM/yyyy',
                'required' => true,
                'label' => 'entity.planning.table.startDate',
                'attr' => ['class' => 'js-datepicker']
            ))
            ->add('beginningTime', TimeType::class, array(
                'label' => 'entity.planning.table.beginningTime',
                'input'  => 'datetime',
                'widget' => 'choice'
            ))

            ->add('endingTime', TimeType::class, array(
                'label' => 'entity.planning.table.endingTime',
                'input'  => 'datetime',
                'widget' => 'choice',
            ))
            ->add('numberSessions', NumberType::class, array(
                'required' => true, 
                'label' => 'entity.planning.table.numberSessions',
                'html5' => true,
                'attr' => ['min' => '1'],
                ))

            ->add('maxPlaces', NumberType::class,  array(
                'required' => true, 
                'label' => 'entity.planning.table.maxPlaces',
                'html5' => true,
                'attr' => ['min' => '1' ,'class'=>'maxPlaces' ],
            ))

            ->add('day', ChoiceType::class, [
                'label' => 'entity.planning.table.day',
                'choices'  => [
                    'days.monday' => 1,
                    'days.tuesday' => 2,
                    'days.wednesday' => 3,
                    'days.thursday' => 4,
                    'days.friday' => 5,
                    'days.saturday' => 6,
                    'days.sunday' => 7,
                ],
                'attr' => array('class'=>'form-control select2'),
                'required' => true,
                'multiple' => false,
                'expanded' => false
            ])
                
            ->add('activity', EntityType::class, [
                'label' => 'entity.planning.table.activity',
                'required' => true,
                'class' => Activity::class,
                'choice_label' => function ($activity) {
                    return $activity->getName();
                },
                'attr' => array('class'=>'form-control activity'),
                'required' => true,
                'multiple' => false,
                'expanded' => false
            ])

            ->add('place', EntityType::class, [
                'label' => 'entity.planning.table.place',
                'required' => true,
                'class' => Place::class,
                'choice_label' => function ($place) {
                    return $place->getName() . ", " . $place->getAddress();
                },
                'attr' => array('class'=>'form-control select2'),
                'required' => true,
                'multiple' => false,
                'expanded' => false
            ])

            ->add('status', CheckboxType::class, [
                'required' => false,
                'label' => 'entity.planning.table.status',
                'label_attr' => ['class' => 'switch-custom'],
            ])
            ->remove('deletedAt')
            ->remove('createdAt')
            ->remove('updatedAt')
            ->add('valider', SubmitType::class, array('attr'=>array('class'=>'btn btn-primary')))
        ;
        
        $builder->addEventListener(
            FormEvents::PRE_SET_DATA,
            function (FormEvent $event) {
                $form = $event->getForm();
                $data = $event->getData();
                $activity = $data->getActivity();
                $ajaxCall = $data->getAjaxCall();

                if($activity != null && $ajaxCall != null ){
                    $form->add('educator', EntityType::class, [
                        'label' => 'entity.activity.table.educator',
                        'class' => Educator::class,
                        'query_builder' => function (EntityRepository $er) use($activity) {
                            return $er->createQueryBuilder('e')
                                ->join('e.user', 'u')
                                ->join('e.activities', 'a')
                                ->where('a.id = :activity')
                                ->setParameter('activity',  $activity->getId() )
                                ->orderBy('u.firstName', 'ASC');
                        },
                        'attr' => array('class'=>'form-control select2 educator'),
                        'required' => true,
                        'multiple' => false,
                        'expanded' => false,
                        'choice_label' => function ($educator) {
                            return $educator->getUser()->getFirstName() . ' ' . $educator->getUser()->getLastName();
                        }
                    ]);
                }   
            }
        );

    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Planning::class,
            'allow_extra_fields' => true
        ]);
    }
}