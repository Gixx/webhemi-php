<?php

declare(strict_types=1);

namespace App\Controller\Site;

use App\Api\MediaBlobStore;
use App\Entity\MediaAsset;
use App\Routing\HostContextHolder;
use App\Site\CurrentPublicSite;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Asset\Packages;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Attribute\Route;

/**
 * Public site favicon: custom media when set, else default system SVG.
 */
final class SiteFaviconController extends AbstractController
{
    public function __construct(
        private readonly HostContextHolder $hostContext,
        private readonly CurrentPublicSite $currentPublicSite,
        private readonly MediaBlobStore $blobs,
        private readonly Packages $assets,
    ) {
    }

    #[Route('/.webhemi/favicon', name: 'site_favicon', methods: ['GET'])]
    public function __invoke(): Response
    {
        try {
            $resolved = $this->currentPublicSite->require($this->hostContext->get());
        } catch (NotFoundHttpException) {
            return $this->redirectToDefault(false);
        }

        $site = $resolved['site'];
        $favicon = $site->getFaviconMedia();
        if ($favicon instanceof MediaAsset && !$favicon->isDeleted()) {
            $absolute = $this->blobs->absolutePath($favicon->getStorageKey());
            if (is_file($absolute) && is_readable($absolute)) {
                $response = new BinaryFileResponse($absolute);
                $response->headers->set(
                    'Content-Type',
                    $favicon->getMimeType() ?: 'application/octet-stream',
                );
                $response->setContentDisposition(
                    ResponseHeaderBag::DISPOSITION_INLINE,
                    $favicon->getOriginalFilename(),
                );
                $response->setPublic();
                $response->setMaxAge(3600);

                return $response;
            }
        }

        return $this->redirectToDefault($site->isMain());
    }

    private function redirectToDefault(bool $main): RedirectResponse
    {
        $file = $main ? 'site_main.svg' : 'site.svg';

        return $this->redirect($this->assets->getUrl('admin/icons/system/' . $file));
    }
}
