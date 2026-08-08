<?php

declare(strict_types=1);

namespace App\Controller\Api;

use App\Api\ApiJson;
use App\Api\AssignHostInput;
use App\Api\CreateHostInput;
use App\Api\CreateSiteInput;
use App\Api\HostAlreadyAssignedException;
use App\Api\HostAdminSurfaceNotAllowedException;
use App\Api\HostApiMapper;
use App\Api\HostAssigner;
use App\Api\HostCreator;
use App\Api\HostDeleter;
use App\Api\HostHostTakenException;
use App\Api\HostNotPendingException;
use App\Api\HostNotVerifiedForAssignException;
use App\Api\HostSiteNotFoundException;
use App\Api\HostUnassigner;
use App\Api\HostUpdater;
use App\Api\HostVerificationFailedException;
use App\Api\HostVerifier;
use App\Api\SiteApiMapper;
use App\Api\SiteCreator;
use App\Api\SiteDeleter;
use App\Api\SiteHasHostsException;
use App\Api\SiteSlugTakenException;
use App\Api\SiteUpdater;
use App\Api\UpdateHostInput;
use App\Api\UpdateSiteInput;
use App\Entity\Site;
use App\Entity\SiteHost;
use App\Repository\SiteHostRepository;
use App\Repository\SiteRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
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

    #[Route('/sites/{id}', name: 'api_admin_sites_show', requirements: ['id' => '\d+'], methods: ['GET'])]
    #[IsGranted('site.list')]
    public function showSite(#[MapEntity(id: 'id')] Site $site): JsonResponse
    {
        return ApiJson::data(SiteApiMapper::toArray($site));
    }

    #[Route('/sites/{id}', name: 'api_admin_sites_update', requirements: ['id' => '\d+'], methods: ['PATCH'])]
    #[IsGranted('site.edit')]
    #[IsCsrfTokenValid('admin_api', tokenKey: 'X-CSRF-TOKEN', tokenSource: IsCsrfTokenValid::SOURCE_HEADER)]
    public function updateSite(
        #[MapEntity(id: 'id')] Site $site,
        Request $request,
        SiteUpdater $updater,
    ): JsonResponse {
        try {
            $payload = json_decode($request->getContent(), true, 512, JSON_THROW_ON_ERROR);
        } catch (\JsonException) {
            return ApiJson::error('invalid_json', 'Request body must be valid JSON.', 400);
        }

        $input = UpdateSiteInput::fromPayload($payload);
        if (!$input->isValid()) {
            return ApiJson::error(
                'validation_failed',
                'Site could not be updated.',
                422,
                $input->fieldErrors,
            );
        }

        try {
            $updated = $updater->update($site, $input);
        } catch (SiteSlugTakenException) {
            return ApiJson::error(
                'slug_taken',
                'A site with this slug already exists.',
                409,
                ['slug' => 'Slug is already taken.'],
            );
        }

        return ApiJson::data(SiteApiMapper::toArray($updated));
    }

    #[Route('/sites/{id}', name: 'api_admin_sites_delete', requirements: ['id' => '\d+'], methods: ['DELETE'])]
    #[IsGranted('site.edit')]
    #[IsCsrfTokenValid('admin_api', tokenKey: 'X-CSRF-TOKEN', tokenSource: IsCsrfTokenValid::SOURCE_HEADER)]
    public function deleteSite(
        #[MapEntity(id: 'id')] Site $site,
        SiteDeleter $deleter,
    ): Response {
        try {
            $deleter->delete($site);
        } catch (SiteHasHostsException) {
            return ApiJson::error(
                'hosts_assigned',
                'Unassign or delete hosts before deleting this site.',
                409,
            );
        }

        return new Response(null, Response::HTTP_NO_CONTENT);
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

    #[Route('/hosts/{id}', name: 'api_admin_hosts_show', requirements: ['id' => '\d+'], methods: ['GET'])]
    #[IsGranted('host.list')]
    public function showHost(#[MapEntity(id: 'id')] SiteHost $host): JsonResponse
    {
        return ApiJson::data(HostApiMapper::toArray($host));
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
        } catch (HostHostTakenException) {
            return ApiJson::error(
                'host_taken',
                'A host with this hostname already exists.',
                409,
                ['host' => 'Hostname is already taken.'],
            );
        } catch (HostNotVerifiedForAssignException) {
            return ApiJson::error(
                'not_assignable',
                'Only verified, unassigned hosts can be assigned to a site.',
                422,
                ['siteId' => 'Host must be verified and unassigned.'],
            );
        } catch (HostAlreadyAssignedException) {
            return ApiJson::error(
                'already_assigned',
                'Host is already assigned to a site.',
                422,
                ['siteId' => 'Host is already assigned.'],
            );
        } catch (HostAdminSurfaceNotAllowedException) {
            return ApiJson::error(
                'admin_surface_main_only',
                'Admin surface is only allowed on the Main site.',
                422,
                ['surface' => 'Admin surface requires the Main site (slug "main").'],
            );
        }

        return ApiJson::data(HostApiMapper::toArray($updated));
    }

    #[Route('/hosts/{id}', name: 'api_admin_hosts_delete', requirements: ['id' => '\d+'], methods: ['DELETE'])]
    #[IsGranted('host.edit')]
    #[IsCsrfTokenValid('admin_api', tokenKey: 'X-CSRF-TOKEN', tokenSource: IsCsrfTokenValid::SOURCE_HEADER)]
    public function deleteHost(
        #[MapEntity(id: 'id')] SiteHost $host,
        HostDeleter $deleter,
    ): Response {
        $deleter->delete($host);

        return new Response(null, Response::HTTP_NO_CONTENT);
    }

    #[Route('/hosts/{id}/assign', name: 'api_admin_hosts_assign', requirements: ['id' => '\d+'], methods: ['POST'])]
    #[IsGranted('host.edit')]
    #[IsCsrfTokenValid('admin_api', tokenKey: 'X-CSRF-TOKEN', tokenSource: IsCsrfTokenValid::SOURCE_HEADER)]
    public function assignHost(
        #[MapEntity(id: 'id')] SiteHost $host,
        Request $request,
        HostAssigner $assigner,
    ): JsonResponse {
        try {
            $payload = json_decode($request->getContent(), true, 512, JSON_THROW_ON_ERROR);
        } catch (\JsonException) {
            return ApiJson::error('invalid_json', 'Request body must be valid JSON.', 400);
        }

        $input = AssignHostInput::fromPayload($payload);
        if (!$input->isValid()) {
            return ApiJson::error(
                'validation_failed',
                'Host could not be assigned.',
                422,
                $input->fieldErrors,
            );
        }

        try {
            $updated = $assigner->assign($host, (int) $input->siteId);
        } catch (HostSiteNotFoundException) {
            return ApiJson::error(
                'site_not_found',
                'The selected site does not exist.',
                404,
                ['siteId' => 'Site not found.'],
            );
        } catch (HostNotVerifiedForAssignException) {
            return ApiJson::error(
                'not_assignable',
                'Only verified, unassigned hosts can be assigned to a site.',
                422,
            );
        } catch (HostAlreadyAssignedException) {
            return ApiJson::error(
                'already_assigned',
                'Host is already assigned to a site.',
                422,
            );
        } catch (HostAdminSurfaceNotAllowedException) {
            return ApiJson::error(
                'admin_surface_main_only',
                'Admin surface is only allowed on the Main site.',
                422,
                [
                    'siteId' => 'Admin-surface hosts can only be assigned to the Main site.',
                    'surface' => 'Admin surface requires the Main site (slug "main").',
                ],
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

    #[Route('/hosts/{id}/verify', name: 'api_admin_hosts_verify', requirements: ['id' => '\d+'], methods: ['POST'])]
    #[IsGranted('host.verify')]
    #[IsCsrfTokenValid('admin_api', tokenKey: 'X-CSRF-TOKEN', tokenSource: IsCsrfTokenValid::SOURCE_HEADER)]
    public function verifyHost(
        #[MapEntity(id: 'id')] SiteHost $host,
        HostVerifier $verifier,
    ): JsonResponse {
        try {
            $updated = $verifier->verify($host);
        } catch (HostNotPendingException) {
            return ApiJson::error(
                'not_pending',
                'Only pending hosts can be verified.',
                422,
            );
        } catch (HostVerificationFailedException) {
            return ApiJson::error(
                'verification_failed',
                'Ownership could not be verified. Ensure the hostname points at this install and try again.',
                422,
            );
        }

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
