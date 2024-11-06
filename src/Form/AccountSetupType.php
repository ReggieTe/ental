<?php

namespace App\Form;

use App\Entity\Enum\DashboardEnum;
use App\Entity\UserSetting;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AccountSetupType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder            
        ->add('notifications',CheckboxType::class,['label'=> 'Phone notifications','required'=>false,'help'=>"Mobile app must be installed to get phone notifications"])
        ->add('sms',CheckboxType::class,['label'=> 'Receive sms notifications','required'=>false,'help'=>"Phone number must be verified to get phone notifications"])
        ->add('email',CheckboxType::class,['label'=> 'Receive email notifications','required'=>false,'help'=>"Email must be verified to get email notifications"])
        ->add('account',CheckboxType::class,['label'=> 'De-active your account','required'=>false,'help'=>"Account won't be able to receive all notifications"])
        ->add('Update',SubmitType::class,['label'=>"Complete setup","attr"=>["class"=>"btn btn-lg btn-danger col-sm-12 col-md-4 pull-right"]])
      ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => UserSetting::class,
        ]);
    }
}
