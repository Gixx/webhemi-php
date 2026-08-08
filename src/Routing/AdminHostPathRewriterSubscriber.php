<?php

declare(strict_types=1);

namespace App\Routing;

use App\Config\AdminAccessMode;
use App\Entity\SiteHost;
use App\Entity\SurfaceType;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\KernelEvents;

/**
 * On domain-mode admin host: map / → /admin, /login → /admin/login, /api → /admin/api
 * so existing routes apply. Runs after HostContext, before the router.
 */
final class AdminHostPathRewriterSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private readonly HostContextHolder $holder,
        private readonly AdminEntryResolverInterface $entryResolver,
    ) {
    }

    public static function getSubscribedEvents(): array
    {
        return [KernelEvents::REQUEST => ['onKernelRequest', 40]];
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
        if (!$siteHost instanceof SiteHost || SurfaceType::Admin !== $siteHost->getSurface()) {
            return;
        }

        if (!$siteHost->getSite()?->isMain()) {
            throw new NotFoundHttpException('Admin surface hosts must belong to the Main site.');
        }

        $entry = $this->entryResolver->resolve();
        if (AdminAccessMode::Domain !== $entry->effectiveMode) {
            return;
        }

        $path = $request->getPathInfo();
        $adminPath = $entry->adminPath;
        $adminApiPath = $entry->adminApiPath;
        $publicApiPath = $entry->publicApiPath;

        $newPath = null;
        if ($path === '/' || $path === '') {
            $newPath = $adminPath;
        } elseif ($path === '/login') {
            $newPath = rtrim($adminPath, '/') . '/login';
        } elseif ($path === '/logout') {
            $newPath = rtrim($adminPath, '/') . '/logout';
        } elseif ($this->matchesPrefix($path, $publicApiPath)) {
            $suffix = substr($path, strlen($publicApiPath));
            $newPath = $adminApiPath . ($suffix ?: '');
        }

        if (null === $newPath || $newPath === $path) {
            return;
        }

        $this->rewritePath($request, $newPath);
    }

    private function shouldSkip(Request $request): bool
    {
        $path = $request->getPathInfo();

        return str_starts_with($path, '/_profiler')
            || str_starts_with($path, '/_wdt')
            || str_starts_with($path, '/assets');
    }

    private function matchesPrefix(string $path, string $prefix): bool
    {
        if ($path === $prefix) {
            return true;
        }

        return str_starts_with($path, rtrim($prefix, '/') . '/');
    }

    private function rewritePath(Request $request, string $newPath): void
    {
        $qs = $request->server->get('QUERY_STRING');
        $uri = $newPath . ($qs ? '?' . $qs : '');
        $request->server->set('REQUEST_URI', $uri);
        $request->server->set('PATH_INFO', $newPath);

        // Clear HttpFoundation pathInfo / requestUri caches.
        \Closure::bind(static function (Request $request) use ($newPath, $uri): void {
            $request->pathInfo = $newPath;
            $request->requestUri = $uri;
        }, null, Request::class)($request);
    }
}
