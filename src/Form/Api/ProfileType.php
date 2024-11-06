<?php

namespace App\Form\Api;

use App\Entity\UserAdmin;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;


class ProfileType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder            
            ->add('fullname',TextType::class,['required'=>false])
            ->add('email',EmailType::class)
            ->add('phone',TextType::class,['required'=>false])
            ->add('website',TextType::class,['required'=>false])
            ->add('address',TextType::class,['required'=>false,"attr"=>["id"=>"address"]] )
            ->add('location',TextType::class, ['required'=>false,'label'=>"Location"] )
            ->add('isBankEnabled',CheckboxType::class,['required'=>false,'label'=>"Bank payments","help"=>"To allow clients to pay via your bank accounts"])
            ->add('isPayfastEnabled',CheckboxType::class,['required'=>false,'label'=>"Payfast payments","help"=>"To allow clients to pay via your payfast account"])
            ->add('isPaypalEnabled',CheckboxType::class,['required'=>false,'label'=>"Paypal payments","help"=>"To allow clients to pay via your paypal accounts"])
           ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => UserAdmin::class,
            'allow_extra_fields'=>true
        ]);
    }
}
