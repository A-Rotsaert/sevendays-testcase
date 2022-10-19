<?php

declare(strict_types=1);

namespace App\Controller;

use App\Controller\AbstractController\AbstractFormController;
use App\Entity\Child;
use App\Entity\Family;
use App\Form\ChildFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * @author Andy Rotsaert <andy.rotsaert@live.be>
 */
final class FamilyController extends AbstractFormController
{
    #[Route('/family', name: 'app_family')]
    public function index(): Response
    {
        $families = $this->getUser()->getFamilies();

        return $this->render('family/index.html.twig', ['families' => $families]);
    }

    #[Route('/family/add', name: 'app_family_add')]
    public function addFamily()
    {
    }

    #[Route('/family/{id}/edit', name: 'app_family_edit')]
    public function editFamily(int $id, TranslatorInterface $translator): Response
    {
        $family = $this->getFamilyFromUserById($id, $translator);

        return $this->render('family/edit.html.twig', ['family' => $family]);
    }

    /**
     * Get the family by id from the user
     * if we can't find it the user has no access to this family, or it doesn't exist.
     * @param int $id
     * @param TranslatorInterface $translator
     * @return array|null
     */
    private function getFamilyFromUserById(int $id, TranslatorInterface $translator): ?Family
    {
        $family = $this->getUser()->getFamilies()->filter(
            function ($family) use ($id) {
                if ($family->getId() === $id) {
                    return $family;
                }

                return null;
            }
        );

        if (empty($family)) {
            throw $this->createNotFoundException($translator->trans('app.family_form.not_found_error'));
        }

        return $family[0];
    }

    #[Route('/family/{id}/child/add', name: 'app_family_child_add')]
    public function addChild(
        int $id,
        Request $request,
        TranslatorInterface $translator
    ): Response {
        $child = new Child();
        $family = $this->getFamilyFromUserById($id, $translator);

        $child->setFamily($family);
        $form = $this->createForm(ChildFormType::class, $child);

        return parent::handleForm(
            ['data' => $child, 'family' => $family],
            'family/children/modify.html.twig',
            ['route' => 'app_family_edit', 'params' => ['id' => $family->getId()]],
            $form,
            $request,
            [
                'type' => 'success',
                'message' => $translator->trans(
                    'app.add.success',
                    ['%type%' => $translator->trans('app.child')]
                )
            ]
        );
    }

    /**
     * @param int $familyId
     * @param int $childId
     * @param Request $request
     * @param TranslatorInterface $translator
     * @return Response
     */
    #[Route('/family/{familyId}/child/{childId}/edit', name: 'app_family_child_edit')]
    public function editChild(int $familyId, int $childId, Request $request, TranslatorInterface $translator): Response
    {
        $family = $this->getFamilyFromUserById($familyId, $translator);
        $child = $this->getChildBeloningToFamily($familyId, $childId, $translator);

        $form = $this->createForm(ChildFormType::class, $child);
        return parent::handleForm(
            ['data' => $child, 'family' => $family],
            'family/children/modify.html.twig',
            ['route' => 'app_family_edit', 'params' => ['id' => $family->getId()]],
            $form,
            $request,
            [
                'type' => 'success',
                'message' => $translator->trans(
                    'app.edit.success',
                    ['%type%' => $translator->trans('app.child')]
                )
            ]
        );
    }

    /**
     * Get the child by id if it belongs to the family
     * @param int $familyId
     * @param int $childId
     * @param TranslatorInterface $translator
     * @return Child
     */
    private function getChildBeloningToFamily(int $familyId, int $childId, TranslatorInterface $translator): Child
    {
        $family = $this->getFamilyFromUserById($familyId, $translator);
        $child = $this->entityManager->getRepository(Child::class)->findOneBy([
            'id' => $childId,
            'family' => $family
        ]);

        if (!$child) {
            throw $this->createNotFoundException($translator->trans('app.child_form.error_id'));
        }

        return $child;
    }

    /**
     * @param int $familyId
     * @param int $childId
     * @param EntityManagerInterface $entityManager
     * @param TranslatorInterface $translator
     * @return RedirectResponse
     */
    #[Route('/family/{familyId}/child/{childId}/delete', name: 'app_family_child_delete')]
    public function deleteChild(
        int $familyId,
        int $childId,
        EntityManagerInterface $entityManager,
        TranslatorInterface $translator
    ): RedirectResponse {
        $child = $this->getChildBeloningToFamily($familyId, $childId, $translator);
        $this->addFlash(
            'success',
            $translator->trans('app.delete.success', ['%type%' => $translator->trans('app.your_child')])
        );
        $entityManager->remove($child);
        $entityManager->flush();

        return new RedirectResponse($this->generateUrl('app_family_edit', ['id' => $familyId]));
    }
}
