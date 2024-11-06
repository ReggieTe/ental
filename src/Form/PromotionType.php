<?php

namespace App\Form;

use App\Entity\Enum\PromotionTypeEnum;
use App\Entity\Promotion;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PromotionType extends AbstractType
{
   
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        
        $builder
        ->add('name',TextType::class,["label"=>"Name"])
        ->add('description',TextareaType::class,['required'=>false,"label"=>"Description"])
        ->add('type', ChoiceType::class, ['choices'=>PromotionTypeEnum::choices(),"label"=>"Type"])
        ->add('amount', IntegerType::class,['label'=>"Amount/Count"])  
        ->add('startDate', DateType::class,['placeholder' => ['year' => 'Year', 'month' => 'Month', 'day' => 'Day',],'label'=>"Start date"])  
        ->add('endDate', DateType::class,['placeholder' => ['year' => 'Year', 'month' => 'Month', 'day' => 'Day',],'label'=>"End date",])  
        ->add('active', CheckboxType::class,['label'=>"Active","help"=>"Apply with immediate effective"])    
        ->add('Update',SubmitType::class,['label'=>"Update profile","attr"=>["class"=>"btn btn-lg btn-danger  col-sm-12 col-md-4 pull-right"]])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Promotion::class,
        ]);
    }
}
