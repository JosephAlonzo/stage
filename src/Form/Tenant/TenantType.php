<?php
namespace App\Form\Tenant;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Doctrine\ORM\EntityRepository;

use App\Entity\Tenant\Tenant;
use App\Entity\Core\City;
use App\Repository\CityRepository;

use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

class TenantType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('cdosName', TextType::class, array('required' => true, 'label' => 'entity.tenant.table.cdosName'))
            ->add('cdosNumber', TextType::class, array('required' => true, 'label' => 'entity.tenant.table.cdosNumber'))
            ->add('siret', TextType::class, array('required' => true, 'label' => 'entity.tenant.table.siret'))
            ->add('codeApe', TextType::class, array('required' => true, 'label' => 'entity.tenant.table.codeApe'))
            ->add('address', TextType::class, array('required' => true, 'label' => 'entity.tenant.table.address'))
            ->add('city', EntityType::class, [
                'label' => 'entity.tenant.table.city',
                'required' => true,
                'class' => City::class,
                'choice_label' => function ($city) {
                    return $city->getName();
                }
            ])
            ->add('phoneNumber', TelType::class, array(
                'required' => false, 
                'label' => 'entity.tenant.table.phoneNumber'
            ))
            ->add('email', EmailType::class, array('required' => false, 'label' => 'entity.tenant.table.email'))
            ->add('siteInternet', TextType::class, array('required' => true, 'label' => 'entity.tenant.table.siteInternet'))
            ->remove('deletedAt')
            ->remove('createdAt')
            ->remove('updatedAt')
            ->add('valider', SubmitType::class, array('attr'=>array('class'=>'btn btn-primary')))
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => tenant::class,
        ]);
    }
}