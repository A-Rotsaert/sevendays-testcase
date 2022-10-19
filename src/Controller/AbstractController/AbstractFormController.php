<?php

declare(strict_types=1);

namespace App\Controller\AbstractController;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * @author Andy Rotsaert <andy.rotsaert@live.be>
 */
abstract class AbstractFormController extends AbstractController
{
    /**
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(protected EntityManagerInterface $entityManager)
    {
    }

    /**
     * @param mixed $data
     * @param string $twig
     * @param string|array $redirectToRoute
     * @param FormInterface $form
     * @param Request $request
     * @param array|null $flashMessage
     *
     * @return Response
     */
    protected function handleForm(
        object|array $data,
        string $twig,
        string|array $redirectToRoute,
        FormInterface $form,
        Request $request,
        ?array $flashMessage = null
    ): Response {
        $form->handleRequest($request);
        if ($request->isMethod('POST')) {
            if ($form->isSubmitted() && $form->isValid()) {
                $this->entityManager->persist((is_array($data)) ? $data['data'] : $data);
                $this->entityManager->flush();
                if ($flashMessage) {
                    $this->addFlash($flashMessage['type'], $flashMessage['message']);
                }
                if (is_array($redirectToRoute)) {
                    return $this->redirectToRoute($redirectToRoute['route'], $redirectToRoute['params']);
                } else {
                    return $this->redirectToRoute($redirectToRoute);
                }
            }
        }

        return $this->render($twig, [
            'form' => $form->createView(),
            'data' => $data ?: null,
        ]);
    }
}
