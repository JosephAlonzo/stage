<?php
namespace App\Form\Sport;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Doctrine\ORM\EntityRepository;

use App\Entity\Sport\Beneficiary;
use App\Entity\Core\City;

use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

class BeneficiaryType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, array('required' => true, 'label' => 'entity.beneficiary.table.name'))
            ->add('lastName', TextType::class, array('required' => true, 'label' => 'entity.beneficiary.table.lastName'))
            ->add('gender', ChoiceType::class, array(
                'label' => "entity.beneficiary.table.gender",
                'choices' => array(
                    'F' => 'F',
                    'M' => 'M'
                ),
                'attr' => array('class'=>'form-control select2'),
                'required' => true,
                'multiple' => false,
                'expanded' => false
            ))
            ->add('familySituation', ChoiceType::class, array(
                'label' => "entity.beneficiary.table.familySituation",
                'choices' => array(
                    'Célibataire' => 'Celibataire',
                    'Marié(e)/PACSE' => 'Marie',
                    'Séparé(e)' => 'Separe',
                ),
                'attr' => array('class'=>'form-control select2'),
                'required' => true,
                'multiple' => false,
                'expanded' => false
            ))
            ->add('numberChildren', NumberType::class, array(
                'required' => true, 
                'label' => 'entity.beneficiary.table.numberChildren',
                'html5' => true,
                'attr' => ['min' => '0','max' => '90']
                ))
            ->add('dateBirth', DateType::class, array(
                'widget' => 'single_text',
                'input' => 'datetime',
                'html5' => false,
                'format' => 'dd/MM/yyyy',
                'required' => true,
                'label' => 'entity.beneficiary.table.dateBirth',
                'attr' => ['class' => 'js-datepicker year']
            ))
            ->add('address', TextType::class, array('required' => true, 'label' => 'entity.beneficiary.table.address'))
            ->add('city', EntityType::class, array(
                'label' => "entity.beneficiary.table.city",
                'class' => City::class,
                'choice_label' => 'name',
                'attr' => array('class'=>'form-control select2'),
                'required' => true,
            ))
            ->add('email', EmailType::class, array('required' => false, 'label' => 'entity.beneficiary.table.email'))
            ->add('phoneNumber', TextType::class, array(
                'required' => false, 
                'label' => 'entity.beneficiary.table.phoneNumber'
            ))
            ->add('lodging', ChoiceType::class, array(
                'label' => "entity.beneficiary.table.lodging",
                'choices' => array(
                    'Domicile personnel' => 'Domicile personnel',
                    'CADA' => 'CADA',
                    'CHRS' => 'CHRS',
                    'Autre' => 'Autre'
                ),
                'attr' => array('class'=>'form-control select2 lodging'),
                'required' => true,
            ))
            ->add('autreLodging', TextType::class, array(
                'label' => false,
                'required' => false,
                'attr' => array('class'=>'d-none autreLodging'),
            ))
            ->add('medicalCover', ChoiceType::class, array(
                'label' => "entity.beneficiary.table.medicalCover",
                'choices' => array(
                    'Régime général' => 'Regime general',
                    'CMU de Base' => 'CMU de Base',
                    'Aide Médicale Etat' => 'Aide Médicale Etat',
                    'CMU/ CMU-C' => 'CMU/ CMU-C',
                    'Sans couverture' => 'Sans couverture'
                ),
                'attr' => array('class'=>'form-control select2'),
                'required' => true,
            ))
            ->add('resourcesReceived', ChoiceType::class, array(
                'label' => "entity.beneficiary.table.resourcesReceived",
                'choices' => array(
                    'RSA' => 'RSA',
                    'Pôle Emploi' => 'Pole Emploi',
                    'AAH' => 'AAH',
                    'Retaite' => 'Retaite',
                    'Prestations sociales' => 'Prestations sociales',
                    'Salaire' => 'Salaire',
                    'Autres' => 'Autres'
                ),
                'attr' => array('class'=>'form-control select2 resourcesReceived'),
                'required' => true,
            ))
            ->add('autreResourcesReceived', TextType::class, array(
                'label' => false,
                'required' => false,
                'attr' => array('class'=>'d-none autreResourcesReceived'),
            ))
            ->remove('deletedAt')
            ->remove('createdAt')
            ->remove('updatedAt')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => beneficiary::class,
        ]);
    }
}