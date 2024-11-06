<?php

namespace App\Form;

use App\Entity\Car;
use App\Entity\Enum\FuelEnum;
use App\Entity\Enum\TransmissionEnum;
use App\Entity\UserCarAdditional;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CarAdditionalType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder            
            ->add('description',TextareaType::class,['required'=>false])
            ->add('amount',IntegerType::class)
            ->add('state',CheckboxType::class,['required'=>false])
            ->add('addToBookingtotal',CheckboxType::class,['required'=>false,'label'=>"Add to booking total"]) 
            ->add('update',SubmitType::class,['label'=>"Update"])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => UserCarAdditional::class,
        ]);
    }
}
