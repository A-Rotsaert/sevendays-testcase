<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Family;
use App\Event\User\UserFinalizedEvent;
use App\Form\UserProfileFormType;
use App\Service\Hubspot\HubspotInterface;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * @author Andy Rotsaert <andy.rotsaert@live.be>
 */
final class UserController extends AbstractController
{
    #[Route('/user/profile', name: 'app_user_profile')]
    public function userProfile(
        Request $request,
        TranslatorInterface $translator,
        EntityManagerInterface $entityManager,
        EventDispatcherInterface $eventDispatcher,
        LoggerInterface $logger
    ): Response {
        $locales = explode('|', $this->getParameter('app.supported_locales'));
        $countries = explode('|', $this->getParameter('app.supported_countries'));

        $user = $this->getUser();
        if (empty($user->getFamilies())) {
            $family = new Family();
            $user->addFamily($family);
        }

        $form = $this->createForm(
            UserProfileFormType::class,
            $user,
            [
                'locales' => array_combine(
                    array_map(function ($value) {
                        return 'app.locales.' . $value;
                    }, $locales),
                    $locales
                ),
                'countries' => array_combine(
                    array_map(function ($value) {
                        return 'app.countries.' . $value;
                    }, $countries),
                    $countries
                ),
            ]
        );

        $form->handleRequest($request);
        if ($request->isMethod('POST')) {
            if ($form->isSubmitted() && $form->isValid()) {
                $formData = $form->getData();
                if (empty($user->getFamilies())) {
                    $family = new Family();
                    $family->setName($formData['family_name']);
                    unset($formData['family_name']);
                }

                $entityManager->persist($formData);
                $entityManager->flush();

                if (!$user->getName() || !$user->getLocale() || !$user->getLocation()) {
                    $event = new UserFinalizedEvent($form->getData());
                    $eventDispatcher->dispatch($event, UserFinalizedEvent::NAME);
                    $logger->info(sprintf('User with id %d has finalized his profile', $user->getId()));
                }
                $this->addFlash('success', $translator->trans('app.user_profile_form.success'));
                $this->redirectToRoute('app_family');
            }
        }

        return $this->render('user/profile.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/user/profile/delete', name: 'app_user_delete_profile')]
    public function userDeleteProfile(
        EntityManagerInterface $entityManager,
        TranslatorInterface $translator,
        LoggerInterface $logger,
        HubspotInterface $hubspot
    ): Response {
        $user = $this->getUser();
        $entityManager->remove($user);
        $entityManager->flush();
        $this->addFlash(
            'success',
            $translator->trans('app.delete.success', ['%type%' => $translator->trans('app.menu.profile')])
        );
        $hubspot = $hubspot->createFactory();
        $hubspot->deleteContact($user);

        $logger->info(sprintf('User with id %d has deleted his profile', $user->getId()));

        return new RedirectResponse($this->generateUrl('app_family'));
    }
}
