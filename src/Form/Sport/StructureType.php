<?php
namespace App\Form\Sport;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Doctrine\ORM\EntityRepository;

use App\Entity\Sport\structure;
use App\Entity\Core\City;
use App\Repository\CityRepository;

use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

class StructureType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, array('required' => true, 'label' => 'entity.structure.table.name'))
            ->add('address', TextType::class, array('required' => true, 'label' => 'entity.structure.table.address'))
            ->add('city', EntityType::class, [
                'label' => 'entity.structure.table.city',
                'required' => true,
                'class' => City::class,
                'choice_label' => function ($city) {
                    return $city->getName();
                }
            ])
            ->add('email', EmailType::class, array('required' => false, 'label' => 'entity.structure.table.email'))
            ->add('phoneNumber', TelType::class, array(
                'required' => false, 
                'label' => 'entity.structure.table.phoneNumber'
            ))
            ->remove('deletedAt')
            ->remove('createdAt')
            ->remove('updatedAt')
            ->add('valider', SubmitType::class, array('attr'=>array('class'=>'btn btn-primary')))
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => structure::class,
        ]);
    }
}