<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Controller\Enum\FormAction;
use App\Entity\Characteristic;
use App\Form\CharacteristicFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * @author Andy Rotsaert <andy.rotsaert@live.be>
 */
final class CharacteristicController extends AbstractController
{
    #[Route('/admin/characteristics', name: 'app_admin_characteristics')]
    public function listCharacteristics(EntityManagerInterface $entityManager): Response
    {
        return $this->render('admin/characteristic/index.html.twig', [
            'locale' => $this->getUser()->getLocale(),
            'characteristics' => $entityManager->getRepository(Characteristic::class)->findAll()
        ]);
    }

    #[Route('/admin/characteristic/add', name: 'app_admin_characteristic_add')]
    public function addCharacteristic(
        Request $request,
        TranslatorInterface $translator,
        EntityManagerInterface $entityManager
    ): Response {
        $characteristic = new Characteristic();
        return $this->modifyCharacteristic(FormAction::ADD, $characteristic, $request, $translator, $entityManager);
    }

    #[Route('/admin/characteristic/{id}/edit', name: 'app_admin_characteristic_edit')]
    public function editCharacteristic(
        int $id,
        Request $request,
        TranslatorInterface $translator,
        EntityManagerInterface $entityManager
    ): Response {
        $characteristic = $entityManager->getRepository(Characteristic::class)->find($id);
        return $this->modifyCharacteristic(FormAction::EDIT, $characteristic, $request, $translator, $entityManager);
    }

    private function modifyCharacteristic(
        FormAction $action,
        Characteristic $characteristic,
        Request $request,
        TranslatorInterface $translator,
        EntityManagerInterface $entityManager
    ): Response {
        $locales = explode('|', $this->getParameter('app.supported_locales'));
        $form = $this->createForm(CharacteristicFormType::class, $characteristic, [
            'locales' => array_combine(
                array_map(function ($value) {
                    return 'app.locales.' . $value;
                }, $locales),
                $locales
            )
        ]);

        $form->handleRequest($request);
        if ($request->isMethod('POST')) {
            if ($form->isSubmitted() && $form->isValid()) {
                $entityManager->persist($characteristic);
                $entityManager->flush();

                $this->addFlash(
                    'success',
                    $translator->trans(
                        sprintf('app.%s.success', $action->getAction()),
                        ['%type%' => $translator->trans('app.characteristic_form.title')]
                    )
                );

                return $this->redirectToRoute('app_admin_characteristics');
            }
        }

        return $this->render('admin/characteristic/modify.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
