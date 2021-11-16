<?php
namespace App\Form\Sport;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Doctrine\ORM\EntityRepository;

use App\Entity\Sport\OrientationSheet;
use App\Entity\Sport\OrientationSheetPlannings;
use App\Entity\Sport\Planning;
use App\Entity\Sport\SocialWorker;
use App\Form\Sport\BeneficiaryType;

use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;


class OrientationSheetType extends AbstractType
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
                'label' => 'entity.orientation_sheet.table.startDate',
                'attr' => ['class' => 'js-datepicker']
            ))
            ->add('sendingDate', DateType::class, array(
                'widget' => 'single_text',
                'input' => 'datetime',
                'html5' => false,
                'format' => 'dd/MM/yyyy',
                'required' => true,
                'label' => 'entity.orientation_sheet.table.sendingDate',
                'attr' => ['class' => 'js-datepicker']
            ))
            ->add('situation', TextareaType::class, array(
                'required' => true, 
                'label' => 'entity.orientation_sheet.table.situation',
                'attr' => array('cols' => '5', 'rows' => '5'),
            )) 
            ->add('axes', ChoiceType::class, array(
                'label' => "entity.orientation_sheet.table.axe",
                'choices' => array(
                    'Autonomie' => 'AUTONOMIE',
                    'Sante' => 'SANTE',
                    'Socialisation' => 'SOCIALISATION'
                ),
                'attr' => array('class'=>'form-control select2'),
                'required' => true,
                'multiple' => true,
                'expanded' => false
            ))
            ->add('planning', EntityType::class, array(
                'label' => "entity.orientation_sheet.table.activities",
                'class' => Planning::class,
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('p')
                        ->orderBy('p.startDate', 'ASC');
                },
                'choice_label' => function ($planning) {
                    return $planning->getActivity()->getName() . ' ' . $planning->getStartDate()->format('d/M/Y');
                },
                'attr' => array('class'=>'form-control select2'),
                'required' => true,
                'multiple' => true,
                'mapped' => false,
                'expanded' => false
            ))
            ->add('socialWorker', EntityType::class, array(
                'label' => "entity.orientation_sheet.table.social_worker",
                'class' => SocialWorker::class,
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('s');
                },
                'choice_label' => function ($socialWorker) {
                    return $socialWorker->getUser()->getFirstName() . ' ' . $socialWorker->getUser()->getLastName();
                },
                'attr' => array('class'=>'form-control select2'),
            ))
            ->add('photoAuthorization', CheckboxType::class, array(
                'label' => 'entity.orientation_sheet.table.photoAuthorization',
                'data' => true,
            ))
            ->add('beneficiary', BeneficiaryType::class, [
                'label' => false,
            ])

            ->remove('deletedAt')
            ->remove('createdAt')
            ->remove('updatedAt')
            ->add('valider', SubmitType::class, array('attr'=>array('class'=>'btn btn-primary')))
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => orientationSheet::class,
        ]);
    }
}