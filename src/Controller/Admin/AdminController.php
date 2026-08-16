<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Entity\MediaAsset;
use App\Entity\Site;
use App\Repository\SiteRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/admin')]
final class AdminController extends AbstractController
{
    #[Route('', name: 'admin_dashboard', methods: ['GET'])]
    #[IsGranted('ROLE_USER')]
    public function dashboard(SiteRepository $sites): Response
    {
        $rows = array_map(static function (Site $site): array {
            $favicon = $site->getFaviconMedia();

            return [
                'id' => (int) $site->getId(),
                'slug' => $site->getSlug(),
                'name' => $site->getName(),
                'enabled' => $site->isEnabled(),
                'faviconMediaId' => $favicon instanceof MediaAsset && !$favicon->isDeleted()
                    ? (int) $favicon->getId()
                    : null,
            ];
        }, $sites->findBy([], ['name' => 'ASC']));

        return $this->render('admin/dashboard.html.twig', [
            'sites' => $rows,
        ]);
    }

    /** Legacy HTML Sites UI removed; redirect kept for bookmarks. */
    #[Route('/sites', name: 'admin_sites', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_USER')]
    public function sites(): Response
    {
        return $this->redirectToRoute('admin_dashboard', [
            'window' => 'sites',
        ]);
    }

    /** Legacy HTML Hosts UI removed; redirect kept for bookmarks. */
    #[Route('/hosts', name: 'admin_hosts', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_USER')]
    public function hosts(): Response
    {
        return $this->redirectToRoute('admin_dashboard', [
            'window' => 'hosts',
        ]);
    }

    /** Legacy verify POST removed; use `/admin/api` Hosts verify. Redirect kept for bookmarks. */
    #[Route('/hosts/{id}/verify', name: 'admin_hosts_verify', methods: ['POST'])]
    #[IsGranted('ROLE_USER')]
    public function verifyHost(int $id): Response
    {
        return $this->redirectToRoute('admin_dashboard', [
            'window' => 'hosts',
            'id' => $id,
        ]);
    }
}
