<?php

declare(strict_types=1);

namespace App\Site;

use App\Entity\Site;
use App\Entity\SiteHost;
use App\Entity\SurfaceType;
use App\Routing\HostContext;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Resolves the public Site for the current HostContext (site surface only).
 */
final class CurrentPublicSite
{
    /**
     * @return array{host: SiteHost, site: Site}
     */
    public function require(HostContext $context): array
    {
        $siteHost = $context->getSiteHost();
        if (!$siteHost instanceof SiteHost) {
            throw new NotFoundHttpException('Unknown host.');
        }

        if (SurfaceType::Site !== $siteHost->getSurface()) {
            throw new NotFoundHttpException('Not a public site host.');
        }

        if (!$siteHost->isEnabled() || 'verified' !== $siteHost->getVerification()) {
            throw new NotFoundHttpException('Host is not available.');
        }

        $site = $siteHost->getSite();
        if (!$site instanceof Site || !$site->isEnabled()) {
            throw new NotFoundHttpException('Site is not available.');
        }

        return ['host' => $siteHost, 'site' => $site];
    }
}
