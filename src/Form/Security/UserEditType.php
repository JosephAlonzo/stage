<?php

namespace App\Form\Security;

use App\Entity\Security\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Doctrine\ORM\EntityRepository;

use App\Entity\Tenant\Tenant;

use App\Form\Sport\SocialWorkerType;

class UserEditType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('tenant', EntityType::class, [
                'label' => 'entity.tenant.singular',
                'class' => Tenant::class,
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('t')
                        ->orderBy('t.cdosNumber', 'ASC');
                },
                'attr' => array('class'=>'form-control select2'),
                'required' => false,
                'multiple' => false,
                'expanded' => false,
                'choice_label' => function ($tenant) {
                    return $tenant ? $tenant->getCdosName() . ' ' . $tenant->getCdosNumber(): NULL;
                },
            ])
            ->add('firstName', TextType::class, array('required' => true, 'label' => 'entity.social_worker.table.firstName'))
            ->add('lastName', TextType::class, array('required' => true, 'label' => 'entity.social_worker.table.lastName'))
            ->add('phoneNumber', TextType::class, array(
                'required' => false, 
                'label' => 'entity.social_worker.table.phoneNumber'
            ))
            ->add('email', EmailType::class, array(
                'required' => true, 
                'label' => 'entity.social_worker.table.email'
            ))
            ->add('username', TextType::class, array('required' => true, 'label' => 'entity.user.table.username'))
            ->add('roles', ChoiceType::class, array(
                'label' => "entity.user.table.roles",
                'choices' => array(
                    'ADVENSYS' => 'ROLE_ADVENSYS',
                    'MANAGER' => 'ROLE_MANAGER',
                    'UTILISATEUR' => 'ROLE_USER',
                    'TRAVAILLEUR SOCIAL' => 'ROLE_SOCIAL_WORKER',
                    'ÉDUCATEUR' => 'ROLE_EDUCATOR',
                ),
                'attr' => array('class'=>'form-control select2'),
                'required' => true,
                'multiple' => true,
                'expanded' => false
            ))
            ->remove('password')
            ->remove('deletedAt')
            ->remove('createdAt')
            ->remove('updatedAt')
            ->add('valider', SubmitType::class, array('attr'=>array('class'=>'btn btn-primary')))
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
