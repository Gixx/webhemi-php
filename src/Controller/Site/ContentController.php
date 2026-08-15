<?php

declare(strict_types=1);

namespace App\Controller\Site;

use App\Api\MediaBlobStore;
use App\Content\LexicalHtmlRenderer;
use App\Content\PublicContentHit;
use App\Content\PublicPathResolver;
use App\Entity\ContentNode;
use App\Entity\ContentNodeKind;
use App\Entity\MediaAsset;
use App\Routing\HostContextHolder;
use App\Site\CurrentPublicSite;
use App\Theme\ThemeNotFoundException;
use App\Theme\ThemePackage;
use App\Theme\ThemeRenderer;
use App\Theme\ThemeResolver;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Attribute\Route;

/**
 * Public CMS routing: site root, folder indexes, documents, redirects, media refs.
 */
final class ContentController extends AbstractController
{
    public function __construct(
        private readonly HostContextHolder $hostContext,
        private readonly CurrentPublicSite $currentPublicSite,
        private readonly ThemeResolver $themes,
        private readonly ThemeRenderer $themeRenderer,
        private readonly PublicPathResolver $paths,
        private readonly LexicalHtmlRenderer $lexicalHtml,
        private readonly MediaBlobStore $blobs,
    ) {
    }

    #[Route('/', name: 'site_home', methods: ['GET'])]
    public function home(): Response
    {
        return $this->dispatch('/');
    }

    #[Route('/{path}', name: 'site_content', requirements: ['path' => '.+'], methods: ['GET'], priority: -50)]
    public function content(string $path): Response
    {
        return $this->dispatch('/' . ltrim($path, '/'));
    }

    private function dispatch(string $path): Response
    {
        $resolved = $this->currentPublicSite->require($this->hostContext->get());
        $site = $resolved['site'];
        $host = $resolved['host'];

        try {
            $theme = $this->themes->resolve($site->getThemeId());
        } catch (ThemeNotFoundException $e) {
            throw new NotFoundHttpException($e->getMessage(), $e);
        }

        $hit = $this->paths->resolve($site, $path);
        if (!$hit instanceof PublicContentHit) {
            throw new NotFoundHttpException('Content not found.');
        }

        $baseContext = [
            'siteName' => $site->getName(),
            'siteSlug' => $site->getSlug(),
            'themeId' => $theme->id,
            'hostName' => $host->getHost(),
            'assetPrefix' => $theme->assetPrefix(),
        ];

        if ($hit->isRootIndex) {
            $children = $this->paths->listableChildren($site, null);
            if ([] === $children) {
                return $this->themeResponse($theme, 'home.html.twig', $baseContext);
            }

            return $this->folderResponse($theme, $baseContext, $site->getName(), '/', $children);
        }

        $node = $hit->node;
        if (!$node instanceof ContentNode) {
            throw new NotFoundHttpException('Content not found.');
        }

        return match ($node->getKind()) {
            ContentNodeKind::Folder => $this->folderResponse(
                $theme,
                $baseContext,
                $node->getTitle(),
                $hit->path,
                $this->paths->listableChildren($site, $node),
            ),
            ContentNodeKind::Document => $this->documentResponse($theme, $baseContext, $node),
            ContentNodeKind::Redirect => $this->redirectResponse($node),
            ContentNodeKind::MediaRef => $this->mediaResponse($node),
        };
    }

    /**
     * @param array<string, mixed> $baseContext
     * @param list<ContentNode> $children
     */
    private function folderResponse(
        ThemePackage $theme,
        array $baseContext,
        string $title,
        string $folderPath,
        array $children,
    ): Response {
        $entries = [];
        foreach ($children as $child) {
            $entries[] = [
                'title' => $child->getTitle(),
                'href' => $this->paths->childHref($folderPath, $child),
                'kind' => $child->getKind()->value,
            ];
        }

        return $this->themeResponse($theme, 'folder.html.twig', $baseContext + [
            'title' => $title,
            'folderPath' => $folderPath,
            'entries' => $entries,
        ]);
    }

    /**
     * @param array<string, mixed> $baseContext
     */
    private function documentResponse(ThemePackage $theme, array $baseContext, ContentNode $node): Response
    {
        return $this->themeResponse($theme, 'document.html.twig', $baseContext + [
            'title' => $node->getTitle(),
            'bodyHtml' => $this->lexicalHtml->render($node->getBody()),
        ]);
    }

    private function redirectResponse(ContentNode $node): Response
    {
        $target = $node->getRedirectTarget();
        if (null === $target || '' === trim($target)) {
            throw new NotFoundHttpException('Redirect has no target.');
        }
        $target = trim($target);
        if (!$this->isSafeRedirectTarget($target)) {
            throw new NotFoundHttpException('Redirect target is not allowed.');
        }

        return new RedirectResponse($target, Response::HTTP_FOUND);
    }

    private function mediaResponse(ContentNode $node): Response
    {
        $asset = $node->getMediaAsset();
        if (!$asset instanceof MediaAsset || $asset->isDeleted()) {
            throw new NotFoundHttpException('Media asset not found.');
        }
        $absolute = $this->blobs->absolutePath($asset->getStorageKey());
        if (!is_file($absolute) || !is_readable($absolute)) {
            throw new NotFoundHttpException('Media file missing.');
        }

        $response = new BinaryFileResponse($absolute);
        $response->headers->set('Content-Type', $asset->getMimeType() ?: 'application/octet-stream');
        $response->setContentDisposition(
            ResponseHeaderBag::DISPOSITION_INLINE,
            $asset->getOriginalFilename(),
        );

        return $response;
    }

    /**
     * @param array<string, mixed> $context
     */
    private function themeResponse(ThemePackage $theme, string $template, array $context): Response
    {
        return new Response($this->themeRenderer->render($theme, $template, $context));
    }

    private function isSafeRedirectTarget(string $target): bool
    {
        if (str_starts_with($target, '/')) {
            return !str_starts_with($target, '//');
        }
        $lower = strtolower($target);

        return str_starts_with($lower, 'https://') || str_starts_with($lower, 'http://');
    }
}
