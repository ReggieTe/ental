<?php

namespace App\Form\Admin;

use App\Entity\Car;
use App\Entity\Enum\FuelEnum;
use App\Entity\Enum\TransmissionEnum;
use App\Entity\Enum\VaultEnum;
use App\Entity\UserClient;
use App\Entity\Vault;
use Elao\Enum\Bridge\Symfony\Form\Type\EnumType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class CarType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder        
        ->add('name',TextType::class)
        ->add('description',TextareaType::class,['required'=>false])
        ->add('ratePerDay',IntegerType::class)
        ->add('refundableDeposit',IntegerType::class)
        ->add('seat_number',IntegerType::class)
        ->add('bag_number',IntegerType::class)
        ->add('door_number',IntegerType::class)
        ->add('transmission',ChoiceType::class,['choices'=>TransmissionEnum::choices()])
        ->add('fuel',ChoiceType::class,['choices'=>FuelEnum::choices()])
        ->add('aircon',CheckboxType::class,['required'=>false])
        ->add('leatherUpholstery',CheckboxType::class,['required'=>false])
        ->add('gps',CheckboxType::class,['required'=>false])
        ->add('active',CheckboxType::class,['required'=>false])         
            ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Car::class,
            'class'=>Car::class
        ]);
    }
}
