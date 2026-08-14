<?php

declare(strict_types=1);

namespace App\Controller\Api;

use App\Api\ApiJson;
use App\Routing\HostContextHolder;
use App\Site\CurrentPublicSite;
use App\Theme\ThemeNotFoundException;
use App\Theme\ThemeResolver;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Attribute\Route;

/**
 * Thin public (site-scoped) API — Phase 9 proof; not the protected admin API.
 */
final class PublicSiteApiController extends AbstractController
{
    #[Route('/api/site', name: 'public_api_site', methods: ['GET'])]
    public function site(
        HostContextHolder $holder,
        CurrentPublicSite $currentPublicSite,
        ThemeResolver $themes,
    ): JsonResponse {
        $resolved = $currentPublicSite->require($holder->get());
        $site = $resolved['site'];
        $host = $resolved['host'];

        try {
            $theme = $themes->resolve($site->getThemeId());
        } catch (ThemeNotFoundException $e) {
            throw new NotFoundHttpException($e->getMessage(), $e);
        }

        return ApiJson::data([
            'id' => (int) $site->getId(),
            'slug' => $site->getSlug(),
            'name' => $site->getName(),
            'themeId' => $theme->id,
            'theme' => [
                'id' => $theme->manifest->id,
                'name' => $theme->manifest->name,
                'version' => $theme->manifest->version,
                'source' => $theme->source->value,
            ],
            'host' => $host->getHost(),
        ]);
    }
}
