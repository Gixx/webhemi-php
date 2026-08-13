<?php

declare(strict_types=1);

namespace App\Tests\Unit\EventSubscriber;

use App\Config\WebhemiConfig;
use App\Config\WebhemiConfigLoader;
use App\EventSubscriber\SymfonyDebugToolbarSubscriber;
use PHPUnit\Framework\TestCase;
use Symfony\Bundle\WebProfilerBundle\EventListener\WebDebugToolbarListener;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpKernel\Profiler\Profiler;

final class SymfonyDebugToolbarSubscriberTest extends TestCase
{
    public function testDisablesWhenFlagFalseInDev(): void
    {
        $dir = $this->tempConfigDir(false);
        $profiler = $this->createMock(Profiler::class);
        $profiler->expects(self::once())->method('disable');
        $toolbar = $this->createMock(WebDebugToolbarListener::class);
        $toolbar->expects(self::once())->method('setMode')->with(WebDebugToolbarListener::DISABLED);

        $subscriber = new SymfonyDebugToolbarSubscriber(
            new WebhemiConfigLoader($dir),
            'dev',
            $profiler,
            $toolbar,
        );
        $subscriber->onKernelRequest($this->mainRequestEvent());

        $this->removeTree($dir);
    }

    public function testLeavesProfilerWhenFlagTrueInDev(): void
    {
        $dir = $this->tempConfigDir(true);
        $profiler = $this->createMock(Profiler::class);
        $profiler->expects(self::never())->method('disable');
        $toolbar = $this->createMock(WebDebugToolbarListener::class);
        $toolbar->expects(self::never())->method('setMode');

        $subscriber = new SymfonyDebugToolbarSubscriber(
            new WebhemiConfigLoader($dir),
            'dev',
            $profiler,
            $toolbar,
        );
        $subscriber->onKernelRequest($this->mainRequestEvent());

        $this->removeTree($dir);
    }

    public function testDisablesInProdEvenWhenFlagTrue(): void
    {
        $dir = $this->tempConfigDir(true);
        $profiler = $this->createMock(Profiler::class);
        $profiler->expects(self::once())->method('disable');
        $toolbar = $this->createMock(WebDebugToolbarListener::class);
        $toolbar->expects(self::once())->method('setMode')->with(WebDebugToolbarListener::DISABLED);

        $subscriber = new SymfonyDebugToolbarSubscriber(
            new WebhemiConfigLoader($dir),
            'prod',
            $profiler,
            $toolbar,
        );
        $subscriber->onKernelRequest($this->mainRequestEvent());

        $this->removeTree($dir);
    }

    private function tempConfigDir(bool $toolbar): string
    {
        $dir = sys_get_temp_dir() . '/webhemi-toolbar-' . bin2hex(random_bytes(4));
        mkdir($dir . '/var/config', 0775, true);
        $loader = new WebhemiConfigLoader($dir);
        $loader->save(WebhemiConfig::defaults()->withSymfonyDebugToolbar($toolbar));

        return $dir;
    }

    private function mainRequestEvent(): RequestEvent
    {
        $kernel = $this->createStub(HttpKernelInterface::class);

        return new RequestEvent($kernel, Request::create('/'), HttpKernelInterface::MAIN_REQUEST);
    }

    private function removeTree(string $dir): void
    {
        if (!is_dir($dir)) {
            return;
        }
        $items = scandir($dir);
        if (false === $items) {
            return;
        }
        foreach ($items as $item) {
            if ($item === '.' || $item === '..') {
                continue;
            }
            $path = $dir . '/' . $item;
            if (is_dir($path)) {
                $this->removeTree($path);
            } else {
                unlink($path);
            }
        }
        rmdir($dir);
    }
}
