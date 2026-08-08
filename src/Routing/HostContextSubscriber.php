<?php

declare(strict_types=1);

namespace App\Routing;

use App\Config\WebhemiConfigLoader;
use App\Entity\SiteHost;
use App\Entity\SurfaceType;
use App\Repository\SiteHostRepository;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;

final class HostContextSubscriber implements EventSubscriberInterface
{
    public const REQUEST_ATTRIBUTE = '_webhemi_host_context';

    public function __construct(
        private readonly SiteHostRepository $siteHostRepository,
        private readonly HostContextHolder $holder,
        private readonly WebhemiConfigLoader $configLoader,
    ) {
    }

    public static function getSubscribedEvents(): array
    {
        // Before path rewriter (40) and router (32).
        return [KernelEvents::REQUEST => ['onKernelRequest', 48]];
    }

    public function onKernelRequest(RequestEvent $event): void
    {
        if (!$event->isMainRequest()) {
            return;
        }

        $request = $event->getRequest();
        $host = strtolower($request->getHost());
        $siteHost = $this->siteHostRepository->findOneByHost($host);
        $context = new HostContext($siteHost);

        $adminPath = $this->configLoader->get()->adminPath;
        $path = $request->getPathInfo();
        if (
            $siteHost instanceof SiteHost
            && SurfaceType::Site === $siteHost->getSurface()
            && $this->isUnder($path, $adminPath)
        ) {
            $context = $context->withSurfaceOverride(SurfaceType::Admin);
        }

        $this->holder->set($context);
        $request->attributes->set(self::REQUEST_ATTRIBUTE, $context);
    }

    private function isUnder(string $path, string $prefix): bool
    {
        if ($path === $prefix) {
            return true;
        }

        return str_starts_with($path, rtrim($prefix, '/') . '/');
    }
}
