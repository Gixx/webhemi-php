<?php

declare(strict_types=1);

namespace App\EventSubscriber;

use App\Config\SymfonyDebugToolbarSupport;
use App\Config\WebhemiConfigLoader;
use Symfony\Bundle\WebProfilerBundle\EventListener\WebDebugToolbarListener;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\HttpKernel\Profiler\Profiler;

/**
 * Honors webhemi.symfony.debug_toolbar for the Symfony Web Debug Toolbar.
 *
 * @see docs/plan/Settings_Symfony_Debug_Toolbar.md
 */
final class SymfonyDebugToolbarSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private readonly WebhemiConfigLoader $configLoader,
        private readonly string $environment,
        private readonly ?Profiler $profiler = null,
        private readonly ?WebDebugToolbarListener $toolbarListener = null,
    ) {
    }

    public static function getSubscribedEvents(): array
    {
        return [KernelEvents::REQUEST => ['onKernelRequest', 1024]];
    }

    public function onKernelRequest(RequestEvent $event): void
    {
        if (!$event->isMainRequest()) {
            return;
        }

        if (
            SymfonyDebugToolbarSupport::isEditable($this->environment)
            && $this->configLoader->get()->symfonyDebugToolbar
        ) {
            return;
        }

        $this->profiler?->disable();
        $this->toolbarListener?->setMode(WebDebugToolbarListener::DISABLED);
    }
}
