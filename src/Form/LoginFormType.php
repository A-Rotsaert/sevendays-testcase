<?php

declare(strict_types=1);

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\IsTrue;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

/**
 * @author Andy Rotsaert <andy.rotsaert@live.be>
 */
final class LoginFormType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     * @return void
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email', TextType::class, [
                'label' => 'app.login_form.email',
                'required' => true,
                'attr' => [
                    'name' => '_username',
                    'id' => 'username',
                    'value' => $options['last_username'],
                    'autocomplete' => 'email',
                    'autofocus' => true
                ]
            ])
            ->add('password', PasswordType::class, [
                'label' => 'app.login_form.password',
                'required' => true,
                'attr' => [
                    'name' => '_password',
                    'id' => 'password',
                    'autocomplete' => 'current-password',
                ],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Please enter a password',
                    ]),
                    new Length([
                        'min' => 6,
                        'minMessage' => 'Your password should be at least {{ limit }} characters',
                        'max' => 128,
                    ]),
                ],
            ])
            ->add('_remember_me', CheckboxType::class, [
                'label' => 'app.login_form.remember_me',
                'required' => false,
            ]);
    }

    /**
     * @param OptionsResolver $resolver
     * @return void
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'last_username' => null,
        ]);
    }
}
