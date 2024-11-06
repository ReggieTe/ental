<?php

namespace App\Form;

use App\Entity\UserPayFast;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PayFastType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('merchantId', TextType::class, ['required' => false, "label" => "Merchant Id"])
            ->add('merchantKey', TextType::class, ['required' => false, "label" => "Merchant Key"])
            ->add('Update', SubmitType::class, ['label' => "Update profile", "attr" => ["class" => "btn btn-lg btn-danger  col-sm-12 col-md-4 pull-right"]])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => UserPayFast::class,
        ]);
    }
}
