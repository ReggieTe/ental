<?php

namespace App\Form;

use App\Entity\Car;
use App\Entity\Enum\AgreedEnum;
use App\Entity\Enum\ChecklistEnum;
use App\Entity\Enum\FuelEnum;
use App\Entity\Enum\PaymentStatusEnum;
use App\Entity\Enum\TransmissionEnum;
use App\Entity\UserTripChecklist;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ChecklistType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('type', ChoiceType::class, ['choices'=>ChecklistEnum::choices()])
            ->add('damageDescription', TextareaType::class, ['required'=>false])
            ->add('milleage', IntegerType::class)
            ->add('ownerAgreed', ChoiceType::class, ['choices'=>AgreedEnum::choices(),'required'=>false,"label"=>"Rentee"])
            ->add('fuel_available', IntegerType::class)
            ->add('requestRenterToSign', CheckboxType::class,['label'=>"Request vehicle renter to sign","help"=>"Check this box if you want user to review and sign the checklist",'required'=>false])
            
            ->add('update', SubmitType::class, ['label'=>"Update","attr"=>["class"=>"btn btn-lg btn-danger pull-right"]])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => UserTripChecklist::class,
        ]);
    }
}
