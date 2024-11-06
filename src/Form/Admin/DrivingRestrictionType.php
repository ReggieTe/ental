<?php

namespace App\Form\Admin;

use App\Entity\Enum\VaultEnum;
use App\Entity\UserClient;
use App\Entity\UserDrivingRestriction;
use App\Entity\Vault;
use Elao\Enum\Bridge\Symfony\Form\Type\EnumType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;

class DrivingRestrictionType extends AbstractType
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
            'class'=>UserDrivingRestriction::class
        ]);
    }
}
