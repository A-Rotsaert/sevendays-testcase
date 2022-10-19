<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Controller\AbstractController\AbstractDatatableController;
use App\Controller\AbstractController\AbstractFormController;
use App\Controller\Enum\FormAction;
use App\Entity\User;
use App\Form\UserFormType;
use App\Service\UserManager;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * @author Andy Rotsaert <andy.rotsaert@live.be>
 */
final class UserManagementController extends AbstractDatatableController
{
    #[Route('/admin/user_management', name: 'app_admin_user_management')]
    public function userList(Request $request): Response
    {
        if ($request->getMethod() == 'POST') {
            $repository = $this->entityManager->getRepository(User::class);

            return AbstractDatatableController::dataTable($request, $repository);
        }

        return $this->render('admin/user/index.html.twig');
    }

    #[Route('/admin/user/add', name: 'app_admin_user_add')]
    public function addUser(Request $request, TranslatorInterface $translator): Response
    {
        $user = new User();
        return $this->modifyUser(FormAction::ADD, $user, $request, $translator);
    }

    #[Route('/admin/user/{id}/edit', name: 'app_admin_user_edit')]
    public function editUser(
        int $id,
        Request $request,
        TranslatorInterface $translator
    ): Response {
        $user = $this->entityManager->getRepository(User::class)->find($id);
        return $this->modifyUser(FormAction::EDIT, $user, $request, $translator);
    }

    /**
     * @param int $id
     * @param UserManager $userManager
     * @return Response
     */
    #[Route('/admin/user/{id}/delete', name: 'app_admin_user_delete')]
    public function userDelete(
        int $id,
        UserManager $userManager
    ): Response {
        $user = $this->entityManager->getRepository(User::class)->find($id);
        $userManager->deleteUser($user);

        return new RedirectResponse($this->generateUrl('app_admin_user_management'));
    }

    /**
     * Handles user form actions ( Add & Edit )
     * @param FormAction $action
     * @param User $user
     * @param Request $request
     * @param TranslatorInterface $translator
     * @return Response
     */
    private function modifyUser(
        FormAction $action,
        User $user,
        Request $request,
        TranslatorInterface $translator
    ): Response {
        $locales = explode('|', $this->getParameter('app.supported_locales'));
        $countries = explode('|', $this->getParameter('app.supported_countries'));

        $form = $this->createForm(UserFormType::class, $user, [
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
        ]);

        return AbstractFormController::handleForm(
            $user,
            'admin/user/modify.html.twig',
            'app_admin_user_management',
            $form,
            $request,
            [
                'type' => 'success',
                'message' => $translator->trans(
                    sprintf('app.%s.success', $action->getAction()),
                    ['%type%' => $translator->trans('app.user_form.user')]
                )
            ]
        );
    }
}
