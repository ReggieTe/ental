<?php

namespace App\Form\Api;

use App\Entity\UserDrivingRestriction;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CarDrivingRestrictionsType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder             
        ->add('description',TextareaType::class,['required'=>false])
        ->add('fine',IntegerType::class)
        ->add('state',CheckboxType::class,['required'=>false])
        ; 
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => UserDrivingRestriction::class,
            'allow_extra_fields'=>true
        ]);
    }
}
