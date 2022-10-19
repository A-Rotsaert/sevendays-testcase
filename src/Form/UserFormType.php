<?php

declare(strict_types=1);

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @author Andy Rotsaert <andy.rotsaert@live.be>
 */
final class UserFormType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     * @return void
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class)
            ->add('email', TextType::class)
            ->add('locale', ChoiceType::class, [
                'choices' => $options['locales'],
            ])
            ->add(
                $builder->create(
                    'roles',
                    ChoiceType::class,
                    [
                        'multiple' => false,
                        'choices' => [
                            'User' => 'ROLE_USER',
                            'Admin' => 'ROLE_ADMIN',
                            'Super admin' => 'ROLE_SUPER_ADMIN',
                        ],
                        'choice_attr' => [
                            'Super admin' => [
                                'disabled' => (!in_array(
                                    'ROLE_SUPER_ADMIN',
                                    $options['data']->getRoles()
                                ))
                            ],
                        ]
                    ]
                )->addModelTransformer(
                    new CallbackTransformer(
                        function ($rolesAsArray) {
                            return count($rolesAsArray) ? $rolesAsArray[0] : null;
                        },
                        function ($rolesAsString) {
                            return [$rolesAsString];
                        }
                    )
                )
            )
            ->add('password', PasswordType::class)
            ->add('location', LocationFormType::class, [
                'countries' => $options['countries'],
                'block_prefix' => 'location_form_row'
            ]);
    }

    /**
     * @param OptionsResolver $resolver
     * @return void
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
            'locales' => null,
            'countries' => null,
        ]);
    }
}
