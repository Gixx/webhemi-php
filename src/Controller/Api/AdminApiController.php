<?php

declare(strict_types=1);

namespace App\Controller\Api;

use App\Api\ApiJson;
use App\Api\CreateHostInput;
use App\Api\CreateSiteInput;
use App\Api\HostApiMapper;
use App\Api\HostCreator;
use App\Api\HostHostTakenException;
use App\Api\HostSiteNotFoundException;
use App\Api\HostUnassigner;
use App\Api\HostUpdater;
use App\Api\SiteApiMapper;
use App\Api\SiteCreator;
use App\Api\SiteSlugTakenException;
use App\Api\UpdateHostInput;
use App\Entity\Site;
use App\Entity\SiteHost;
use App\Repository\SiteHostRepository;
use App\Repository\SiteRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsCsrfTokenValid;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/admin/api')]
final class AdminApiController extends AbstractController
{
    #[Route('/sites', name: 'api_admin_sites', methods: ['GET'])]
    #[IsGranted('site.list')]
    public function sites(SiteRepository $sites): JsonResponse
    {
        $data = array_map(
            static fn (Site $site): array => SiteApiMapper::toArray($site),
            $sites->findBy([], ['name' => 'ASC']),
        );

        return ApiJson::data($data);
    }

    #[Route('/sites', name: 'api_admin_sites_create', methods: ['POST'])]
    #[IsGranted('site.edit')]
    #[IsCsrfTokenValid('admin_api', tokenKey: 'X-CSRF-TOKEN', tokenSource: IsCsrfTokenValid::SOURCE_HEADER)]
    public function createSite(Request $request, SiteCreator $creator): JsonResponse
    {
        try {
            $payload = json_decode($request->getContent(), true, 512, JSON_THROW_ON_ERROR);
        } catch (\JsonException) {
            return ApiJson::error('invalid_json', 'Request body must be valid JSON.', 400);
        }

        $input = CreateSiteInput::fromPayload($payload);
        if (!$input->isValid()) {
            return ApiJson::error(
                'validation_failed',
                'Site could not be created.',
                422,
                $input->fieldErrors,
            );
        }

        try {
            $site = $creator->create($input);
        } catch (SiteSlugTakenException) {
            return ApiJson::error(
                'slug_taken',
                'A site with this slug already exists.',
                409,
                ['slug' => 'Slug is already taken.'],
            );
        }

        return ApiJson::data(SiteApiMapper::toArray($site), 201);
    }

    #[Route('/hosts', name: 'api_admin_hosts', methods: ['GET'])]
    #[IsGranted('host.list')]
    public function hosts(SiteHostRepository $hosts): JsonResponse
    {
        $data = array_map(
            static fn (SiteHost $host): array => HostApiMapper::toArray($host),
            $hosts->findBy([], ['host' => 'ASC']),
        );

        return ApiJson::data($data);
    }

    #[Route('/hosts', name: 'api_admin_hosts_create', methods: ['POST'])]
    #[IsGranted('host.edit')]
    #[IsCsrfTokenValid('admin_api', tokenKey: 'X-CSRF-TOKEN', tokenSource: IsCsrfTokenValid::SOURCE_HEADER)]
    public function createHost(Request $request, HostCreator $creator): JsonResponse
    {
        try {
            $payload = json_decode($request->getContent(), true, 512, JSON_THROW_ON_ERROR);
        } catch (\JsonException) {
            return ApiJson::error('invalid_json', 'Request body must be valid JSON.', 400);
        }

        $input = CreateHostInput::fromPayload($payload);
        if (!$input->isValid()) {
            return ApiJson::error(
                'validation_failed',
                'Host could not be created.',
                422,
                $input->fieldErrors,
            );
        }

        try {
            $host = $creator->create($input);
        } catch (HostSiteNotFoundException) {
            return ApiJson::error(
                'site_not_found',
                'The selected site does not exist.',
                404,
                ['siteId' => 'Site not found.'],
            );
        } catch (HostHostTakenException) {
            return ApiJson::error(
                'host_taken',
                'A host with this hostname already exists.',
                409,
                ['host' => 'Hostname is already taken.'],
            );
        }

        return ApiJson::data(HostApiMapper::toArray($host), 201);
    }

    #[Route('/hosts/{id}', name: 'api_admin_hosts_update', requirements: ['id' => '\d+'], methods: ['PATCH'])]
    #[IsGranted('host.edit')]
    #[IsCsrfTokenValid('admin_api', tokenKey: 'X-CSRF-TOKEN', tokenSource: IsCsrfTokenValid::SOURCE_HEADER)]
    public function updateHost(
        #[MapEntity(id: 'id')] SiteHost $host,
        Request $request,
        HostUpdater $updater,
    ): JsonResponse {
        try {
            $payload = json_decode($request->getContent(), true, 512, JSON_THROW_ON_ERROR);
        } catch (\JsonException) {
            return ApiJson::error('invalid_json', 'Request body must be valid JSON.', 400);
        }

        $input = UpdateHostInput::fromPayload($payload);
        if (!$input->isValid()) {
            return ApiJson::error(
                'validation_failed',
                'Host could not be updated.',
                422,
                $input->fieldErrors,
            );
        }

        try {
            $updated = $updater->update($host, $input);
        } catch (HostSiteNotFoundException) {
            return ApiJson::error(
                'site_not_found',
                'The selected site does not exist.',
                404,
                ['siteId' => 'Site not found.'],
            );
        }

        return ApiJson::data(HostApiMapper::toArray($updated));
    }

    #[Route('/hosts/{id}/unassign', name: 'api_admin_hosts_unassign', requirements: ['id' => '\d+'], methods: ['POST'])]
    #[IsGranted('host.edit')]
    #[IsCsrfTokenValid('admin_api', tokenKey: 'X-CSRF-TOKEN', tokenSource: IsCsrfTokenValid::SOURCE_HEADER)]
    public function unassignHost(
        #[MapEntity(id: 'id')] SiteHost $host,
        HostUnassigner $unassigner,
    ): JsonResponse {
        $updated = $unassigner->unassign($host);

        return ApiJson::data(HostApiMapper::toArray($updated));
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
