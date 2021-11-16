<?php
namespace App\Form\Sport;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Doctrine\ORM\EntityRepository;

use App\Entity\Sport\SocialWorker;
use App\Entity\Core\City;
use App\Entity\Sport\Structure;
use App\Repository\CityRepository;
use App\Form\Security\UserEditType;

use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Validator\Constraints\File;

class SocialWorkerEditType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('user', UserEditType::class, [
                'label' => false,
            ])
            ->add('origin', TextType::class, array('required' => true, 'label' => 'entity.social_worker.table.origin'))
            ->add('address', TextType::class, array('required' => false, 'label' => 'entity.social_worker.table.address'))
            ->add('structure', EntityType::class, [
                'label' => 'entity.social_worker.table.structure',
                'required' => true,
                'class' => Structure::class,
                'choice_label' => function ($city) {
                    return $city->getName();
                }
            ])
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
            'data_class' => SocialWorker::class,
            'cascade_validation' => true
        ]);
    }
}