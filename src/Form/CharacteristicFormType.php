<?php

declare(strict_types=1);

namespace App\Form;

use A2lix\TranslationFormBundle\Form\Type\TranslationsType;
use App\Entity\Characteristic;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

/**
 * @author <andy.rotsaert@live.be>
 */
final class CharacteristicFormType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     * @return void
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('translations', TranslationsType::class, [
                'fields' => [
                    'name' => [
                        'field_type' => TextType::class,
                        'constraints' => new NotBlank()
                    ]
                ]
            ])
            ->add('amountType', ChoiceType::class, [
                'multiple' => false,
                'mapped' => false,
                'choices' => [
                    'app.characteristic_form.months' => 'months',
                    'app.characteristic_form.years' => 'years',
                ],
            ])
            ->add('minAge', NumberType::class)
            ->addEventListener(FormEvents::PRE_SUBMIT, function (FormEvent $event) {
                if ($event->getData()['amountType'] === 'years') {
                    $data = $event->getData();
                    $data['minAge'] = $event->getData()['minAge'] * 12;
                    $event->setData($data);
                }
            });
    }

    /**
     * @param OptionsResolver $resolver
     * @return void
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Characteristic::class,
            'locales' => null,
        ]);
    }
}
