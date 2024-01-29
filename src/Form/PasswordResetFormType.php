<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Validator\Constraints\NotBlank;


class PasswordResetFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options) :void
    {
        $builder->add('password', RepeatedType::class, [
            'constraints' => [
                new NotBlank(),
            ],
            'data' => '',
            'required' => true,
            'type' => PasswordType::class,
            'invalid_message' => 'The password fields must match.',
            'options' => [
                'attr' => ['class' => 'password-field'],
                'required' => true,
            ],
            'first_options' => ['label' => 'user.new_password'],
            'second_options' => ['label' => 'user.repeat_new_password'],
            'translation_domain' => 'user_bundle',
        ]);

    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            // Configure your form options here
        ]);
    }
}
