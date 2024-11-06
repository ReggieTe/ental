<?php

namespace App\Form\Api;

use App\Entity\UserPayFast;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PayFastType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder        
        ->add('merchantId',TextType::class,['required'=>false,"label"=>"Merchant Id"])
        ->add('merchantKey',TextType::class,['required'=>false,"label"=>"Merchant Key"])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => UserPayFast::class,
            'allow_extra_fields'=>true
        ]);
    }
}
