<?php

declare(strict_types=1);

namespace App\Form;

use App\Entity\Family;
use App\Entity\User;
use App\EventSubscriber\RequestSubscriber;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @author Andy Rotsaert <andy.rotsaert@live.be>
 */
final class UserProfileFormType extends AbstractType
{
    private RequestSubscriber $requestSubscriber;

    /**
     * @param RequestSubscriber $requestSubscriber
     */
    public function __construct(RequestSubscriber $requestSubscriber)
    {
        $this->requestSubscriber = $requestSubscriber;
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     * @return void
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class)
            ->add('locale', ChoiceType::class, [
                'choices' => $options['locales'],
            ])
            ->add('location', LocationFormType::class, [
                'countries' => $options['countries'],
                'block_prefix' => 'location_form_row'
            ])
            ->addEventListener(FormEvents::POST_SET_DATA, function (FormEvent $event) {
                $families = $event->getData()->getFamilies();
                $form = $event->getForm();
                if (!count($families) && !$event->getData()->getName()) {
                    $form->add('families', CollectionType::class, [
                        'entry_type' => FamilyFormType::class,
                        'data' => [new Family()],
                        'entry_options' => [
                            'label' => false
                        ]
                    ]);
                }
            })
            ->addEventListener(FormEvents::SUBMIT, function (FormEvent $event) {
                $form = $event->getForm();
                $form->remove('families');
            })
            ->addEventSubscriber($this->requestSubscriber);
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
