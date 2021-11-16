<?php
namespace App\Form\Core;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Doctrine\ORM\EntityRepository;

use App\Entity\Core\Holiday;

use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\DateType;

class HolidayType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, array('required' => true, 'label' => 'entity.holiday.table.name')) 
            ->add('startDate', DateType::class, array(
                'widget' => 'single_text',
                'input' => 'datetime',
                'html5' => false,
                'format' => 'dd/MM/yyyy',
                'required' => true,
                'label' => 'entity.holiday.table.startDate',
                'attr' => ['class' => 'js-datepicker']
            ))
            ->add('endDate', DateType::class, array(
                'widget' => 'single_text',
                'input' => 'datetime',
                'html5' => false,
                'format' => 'dd/MM/yyyy',
                'required' => true,
                'label' => 'entity.holiday.table.endDate',
                'attr' => ['class' => 'js-datepicker'],
                'data' => new \Datetime()
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
            'data_class' => Holiday::class,
        ]);
    }
}