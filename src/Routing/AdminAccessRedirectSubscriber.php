<?php

declare(strict_types=1);

namespace App\Routing;

use App\Config\AdminAccessMode;
use App\Entity\Site;
use App\Entity\SiteHost;
use App\Entity\SurfaceType;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\KernelEvents;

/**
 * Redirects to the canonical admin entry (path vs domain) per Admin_API_Access_Mode.
 * Priority 42: before AdminHostPathRewriter (40), so domain `/admin…` on the admin host
 * can redirect to `/…` without fighting the internal rewrite of `/login` → `/admin/login`.
 */
final class AdminAccessRedirectSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private readonly HostContextHolder $holder,
        private readonly AdminEntryResolverInterface $entryResolver,
    ) {
    }

    public static function getSubscribedEvents(): array
    {
        return [KernelEvents::REQUEST => ['onKernelRequest', 42]];
    }

    public function onKernelRequest(RequestEvent $event): void
    {
        if (!$event->isMainRequest()) {
            return;
        }

        $request = $event->getRequest();
        if ($this->shouldSkip($request)) {
            return;
        }

        $context = $this->holder->get();
        $siteHost = $context->getSiteHost();
        $entry = $this->entryResolver->resolve();
        $path = $request->getPathInfo();
        $adminPath = $entry->adminPath;

        if (
            $siteHost instanceof SiteHost
            && SurfaceType::Admin === $siteHost->getSurface()
            && !$siteHost->getSite()?->isMain()
        ) {
            throw new NotFoundHttpException('Admin surface hosts must belong to the Main site.');
        }

        $isAdminPath = $this->isUnder($path, $adminPath);
        $onAdminHost = $siteHost instanceof SiteHost && SurfaceType::Admin === $siteHost->getSurface();
        $onMainSiteHost = $siteHost instanceof SiteHost
            && SurfaceType::Site === $siteHost->getSurface()
            && true === $siteHost->getSite()?->isMain();
        $onOtherSiteHost = $siteHost instanceof SiteHost
            && SurfaceType::Site === $siteHost->getSurface()
            && $siteHost->getSite() instanceof Site
            && !$siteHost->getSite()->isMain();

        if (AdminAccessMode::Domain === $entry->effectiveMode) {
            $adminHostname = $entry->adminHost?->getHost();
            if (null === $adminHostname) {
                return;
            }

            if (($onMainSiteHost || $onOtherSiteHost) && $isAdminPath) {
                $suffix = $this->stripPrefix($path, $adminPath);
                $event->setResponse($this->redirectToHost($request, $adminHostname, $suffix));

                return;
            }

            // Canonical domain UI URLs are /, /login — not /admin or /admin/login.
            // Do not redirect /admin/api…: the SPA fetch uses redirect:'manual' and treats
            // opaque redirects as session loss.
            if (
                $onAdminHost
                && $isAdminPath
                && $request->isMethodSafe()
                && !$this->isUnder($path, $entry->adminApiPath)
            ) {
                $suffix = $this->stripPrefix($path, $adminPath);
                $event->setResponse($this->redirectToHost($request, $adminHostname, $suffix));
            }

            return;
        }

        // Path mode
        if ($onAdminHost) {
            $mainHostname = $entry->mainSiteHost?->getHost();
            if (null === $mainHostname) {
                throw new NotFoundHttpException('Main site host is not configured.');
            }
            $targetPath = $this->mapAdminHostPathToSiteAdmin($path, $entry);
            $event->setResponse($this->redirectToHost($request, $mainHostname, $targetPath));

            return;
        }

        if ($onOtherSiteHost && $isAdminPath) {
            $mainHostname = $entry->mainSiteHost?->getHost();
            if (null === $mainHostname) {
                throw new NotFoundHttpException('Main site host is not configured.');
            }
            $event->setResponse($this->redirectToHost($request, $mainHostname, $path));
        }
    }

    private function mapAdminHostPathToSiteAdmin(string $path, CanonicalAdminEntry $entry): string
    {
        if ($path === '/' || $path === '') {
            return $entry->adminPath;
        }
        if ($path === '/login') {
            return rtrim($entry->adminPath, '/') . '/login';
        }
        if ($path === '/logout') {
            return rtrim($entry->adminPath, '/') . '/logout';
        }
        if ($this->isUnder($path, $entry->publicApiPath)) {
            $suffix = $this->stripPrefix($path, $entry->publicApiPath);

            return $entry->adminApiPath . ($suffix === '/' ? '' : $suffix);
        }
        if ($this->isUnder($path, $entry->adminPath)) {
            return $path;
        }

        // Other paths on an orphan admin host → same path on the Main site host.
        return $path;
    }

    private function stripPrefix(string $path, string $prefix): string
    {
        if ($path === $prefix) {
            return '/';
        }
        $suffix = substr($path, strlen($prefix));

        return $suffix === '' ? '/' : $suffix;
    }

    private function isUnder(string $path, string $prefix): bool
    {
        if ($path === $prefix) {
            return true;
        }

        return str_starts_with($path, rtrim($prefix, '/') . '/');
    }

    private function redirectToHost(Request $request, string $hostname, string $path): RedirectResponse
    {
        if ($path === '' || $path[0] !== '/') {
            $path = '/' . $path;
        }
        $qs = $request->getQueryString();
        $scheme = $request->getScheme();
        $port = $request->getPort();
        $isDefaultPort = ('http' === $scheme && 80 === $port)
            || ('https' === $scheme && 443 === $port);
        $authority = $hostname . ($isDefaultPort ? '' : ':' . $port);
        $url = $scheme . '://' . $authority . $path . ($qs ? '?' . $qs : '');

        return new RedirectResponse($url, 302);
    }

    private function shouldSkip(Request $request): bool
    {
        $path = $request->getPathInfo();

        return str_starts_with($path, '/_profiler')
            || str_starts_with($path, '/_wdt')
            || str_starts_with($path, '/assets');
    }
}
