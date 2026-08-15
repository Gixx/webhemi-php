<?php

declare(strict_types=1);

namespace App\Controller\Api;

use App\Api\ApiJson;
use App\Api\SiteSettingsApiMapper;
use App\Api\SiteSettingsInvalidFaviconException;
use App\Api\SiteSettingsUpdater;
use App\Api\UpdateSiteSettingsInput;
use App\Entity\Site;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsCsrfTokenValid;

#[Route('/admin/api/sites/{siteId}', requirements: ['siteId' => '\d+'])]
final class SiteSettingsAdminApiController extends AbstractController
{
    #[Route('/settings', name: 'api_admin_site_settings_show', methods: ['GET'])]
    public function show(
        #[MapEntity(id: 'siteId')] Site $site,
        SiteSettingsApiMapper $mapper,
    ): JsonResponse {
        $this->denyAccessUnlessGranted('site_settings.list', (int) $site->getId());

        return ApiJson::data($mapper->toArray($site, $this->capabilities()));
    }

    #[Route('/settings', name: 'api_admin_site_settings_update', methods: ['PATCH'])]
    #[IsCsrfTokenValid('admin_api', tokenKey: 'X-CSRF-TOKEN', tokenSource: IsCsrfTokenValid::SOURCE_HEADER)]
    public function update(
        #[MapEntity(id: 'siteId')] Site $site,
        Request $request,
        SiteSettingsUpdater $updater,
        SiteSettingsApiMapper $mapper,
    ): JsonResponse {
        $this->denyAccessUnlessGranted('site_settings.edit', (int) $site->getId());

        try {
            $payload = json_decode($request->getContent(), true, 512, JSON_THROW_ON_ERROR);
        } catch (\JsonException) {
            return ApiJson::error('invalid_json', 'Request body must be valid JSON.', 400);
        }

        $input = UpdateSiteSettingsInput::fromPayload($payload);
        if (!$input->isValid()) {
            return ApiJson::error('validation_failed', 'Site settings could not be updated.', 422, $input->fieldErrors);
        }

        try {
            $updated = $updater->update($site, $input);
        } catch (SiteSettingsInvalidFaviconException $e) {
            return ApiJson::error('invalid_favicon', $e->getMessage(), 422, [
                'faviconMediaId' => $e->getMessage(),
            ]);
        }

        return ApiJson::data($mapper->toArray($updated, $this->capabilities()));
    }

    /**
     * @return array{manageHosts: bool, manageUsers: bool}
     */
    private function capabilities(): array
    {
        return [
            'manageHosts' => $this->isGranted('host.list'),
            'manageUsers' => $this->isGranted('user.list'),
        ];
    }
}
