<?php

declare(strict_types=1);

namespace App\Controller\Api;

use App\Entity\Site;
use App\Entity\SiteHost;
use App\Repository\SiteHostRepository;
use App\Repository\SiteRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/admin/api')]
final class AdminApiController extends AbstractController
{
    #[Route('/sites', name: 'api_admin_sites', methods: ['GET'])]
    #[IsGranted('site.list')]
    public function sites(SiteRepository $sites): JsonResponse
    {
        $data = array_map(static function (Site $site): array {
            return [
                'id' => $site->getId(),
                'slug' => $site->getSlug(),
                'name' => $site->getName(),
                'enabled' => $site->isEnabled(),
                'hostCount' => $site->getHosts()->count(),
            ];
        }, $sites->findAll());

        return $this->json(['data' => $data]);
    }

    #[Route('/hosts', name: 'api_admin_hosts', methods: ['GET'])]
    #[IsGranted('host.list')]
    public function hosts(SiteHostRepository $hosts): JsonResponse
    {
        $data = array_map(static function (SiteHost $host): array {
            return [
                'id' => $host->getId(),
                'host' => $host->getHost(),
                'site' => $host->getSite()->getSlug(),
                'surface' => $host->getSurface()->value,
                'status' => $host->getStatus(),
                'active' => $host->isActive(),
            ];
        }, $hosts->findAll());

        return $this->json(['data' => $data]);
    }

    #[Route('/me', name: 'api_admin_me', methods: ['GET'])]
    #[IsGranted('ROLE_USER')]
    public function me(): JsonResponse
    {
        $user = $this->getUser();

        return $this->json([
            'user' => $user?->getUserIdentifier(),
            'roles' => $user?->getRoles() ?? [],
        ]);
    }
}
