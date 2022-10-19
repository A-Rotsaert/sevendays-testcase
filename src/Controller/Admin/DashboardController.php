<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Entity\Child;
use App\Entity\Family;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @author Andy Rotsaert <andy.rotsaert@live.be>
 */
final class DashboardController extends AbstractController
{
    #[Route('/admin/dashboard', name: 'app_admin_dashboard')]
    public function index(EntityManagerInterface $entityManager): Response
    {
        $families = $entityManager->getRepository(Family::class)->findAll();
        $children = $entityManager->getRepository(Child::class)->findAll();

        $totals = [
            'families' => count($families),
            'children' => count($children),
        ];
        return $this->render('admin/dashboard/index.html.twig', [
            'totals' => $totals,
        ]);
    }
}
