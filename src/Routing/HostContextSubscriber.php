<?php

declare(strict_types=1);

namespace App\Routing;

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
    ) {
    }

    public static function getSubscribedEvents(): array
    {
        return [KernelEvents::REQUEST => ['onKernelRequest', 32]];
    }

    public function onKernelRequest(RequestEvent $event): void
    {
        if (!$event->isMainRequest()) {
            return;
        }

        $host = strtolower($event->getRequest()->getHost());
        $siteHost = $this->siteHostRepository->findOneByHost($host);
        $context = new HostContext($siteHost);
        $this->holder->set($context);
        $event->getRequest()->attributes->set(self::REQUEST_ATTRIBUTE, $context);
    }
}
