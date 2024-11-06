<?php

namespace App\Form\Api;

use App\Entity\UserAdmin;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\IsTrue;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Regex;

class RegistrationProfileFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('email',EmailType::class,['label'=>"Email"])
            ->add('agreeTerms', CheckboxType::class, ['label'=>"I agree I have read the above T's & C's of service",'mapped' => false,'constraints' => [ new IsTrue([
                        'message' => 'Tick the "I agree I have read the above" checkbox',]),
                ],
            ])
            ->add('plainPassword', PasswordType::class, [
                'label'=>"Password",
                'mapped' => false,
                'attr' => ['autocomplete' => 'new-password'],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Please enter a password',
                    ]),
                    new Length([
                        'min' => 8,
                        'minMessage' => 'Your password should be at least {{ limit }} characters',
                        // max length allowed by Symfony for security reasons
                        'max' => 4096,
                    ]),
                    new Regex([
                        'pattern'=>"/^(?=.*[A-Z])(?=.*[0-9]).{8,}$/",
                        'message'=>'Password must contains atleast 1 Uppercase letter,1 digit (0-9) and min length of 8 letters'
                    ])
                ],
            ])
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
