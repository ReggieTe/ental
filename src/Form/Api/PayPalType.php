<?php

namespace App\Form\Api;

use App\Entity\UserPayPal;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PayPalType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder        
        ->add('merchantEmail',TextType::class,['required'=>false,"label"=>"Merchant Email"])
       ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => UserPayPal::class,
            'allow_extra_fields'=>true
        ]);
    }
}
