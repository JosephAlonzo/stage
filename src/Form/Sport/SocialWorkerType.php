<?php
namespace App\Form\Sport;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Doctrine\ORM\EntityRepository;

use App\Entity\Sport\SocialWorker;
use App\Entity\Sport\Structure;
use App\Entity\Core\City;
use App\Entity\Security\User;
use App\Repository\CityRepository;
use App\Form\Security\UserSocialWorkerType;
use App\Form\Security\UserType;

use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

class SocialWorkerType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('user', UserType::class, [
                'label' => false,
            ])
            ->add('origin', TextType::class, array('required' => true, 'label' => 'entity.social_worker.table.origin'))
            ->add('structure', EntityType::class, [
                'label' => 'entity.social_worker.table.structure',
                'required' => true,
                'class' => Structure::class,
                'choice_label' => function ($city) {
                    return $city->getName();
                }
            ])
            ->add('address', TextType::class, array('required' => false, 'label' => 'entity.social_worker.table.address'))
            ->add('city', EntityType::class, [
                'label' => 'entity.social_worker.table.city',
                'required' => true,
                'class' => City::class,
                'choice_label' => function ($city) {
                    return $city->getName();
                }
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
            'data_class' => SocialWorker::class
        ]);
    }
}