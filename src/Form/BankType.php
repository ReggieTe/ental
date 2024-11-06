<?php

namespace App\Form;

use App\Entity\Enum\BankAccountTypeEnum;
use App\Entity\Enum\BankEnum;
use App\Entity\UserBank;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class BankType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('accountHolder', TextType::class, ['required' => false, "label" => "Account holder"])
            ->add('accountType', ChoiceType::class, ['choices' => BankAccountTypeEnum::choices(), "label" => "Account type"])
            ->add('accountBank', ChoiceType::class, ['choices' => BankEnum::choices(), "label" => "Account Bank"])
            ->add('accountBranchCode', TextType::class, ['required' => false, "label" => "Account branch code"])
            ->add('accountNumber', TextType::class, ['required' => false, "label" => "Account number"])
            ->add('defaultAccount', CheckboxType::class, ['required' => false, 'label' => "Default account"])
            ->add('Update', SubmitType::class, ['label' => "Update profile", "attr" => ["class" => "btn btn-lg btn-danger  col-sm-12 col-md-4 pull-right"]])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => UserBank::class,
        ]);
    }
}
