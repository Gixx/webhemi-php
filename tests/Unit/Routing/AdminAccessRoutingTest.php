<?php

declare(strict_types=1);

namespace App\Tests\Unit\Routing;

use App\Config\AdminAccessMode;
use App\Config\WebhemiConfig;
use App\Config\WebhemiConfigLoader;
use App\Entity\Site;
use App\Entity\SiteHost;
use App\Entity\SurfaceType;
use App\Repository\SiteHostRepository;
use App\Routing\AdminAccessRedirectSubscriber;
use App\Routing\AdminEntryResolver;
use App\Routing\AdminEntryResolverInterface;
use App\Routing\AdminHostPathRewriterSubscriber;
use App\Routing\CanonicalAdminEntry;
use App\Routing\HostContext;
use App\Routing\HostContextHolder;
use App\Routing\ReservedPaths;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\HttpKernelInterface;

final class AdminAccessRoutingTest extends TestCase
{
    public function testReservedPathsDetectsApiLoginRegister(): void
    {
        $dir = sys_get_temp_dir() . '/webhemi-rsv-' . bin2hex(random_bytes(4));
        mkdir($dir . '/var/config', 0775, true);
        $loader = new WebhemiConfigLoader($dir);
        $paths = new ReservedPaths($loader);

        self::assertTrue($paths->isReservedOnSiteHost('/api'));
        self::assertTrue($paths->isReservedOnSiteHost('/api/posts'));
        self::assertTrue($paths->isReservedOnSiteHost('/login'));
        self::assertTrue($paths->isReservedOnSiteHost('/register'));
        self::assertFalse($paths->isReservedOnSiteHost('/about'));
        self::assertTrue($paths->isAdminPath('/admin'));
        self::assertTrue($paths->isAdminPath('/admin/api/sites'));

        $this->removeTree($dir);
    }

    public function testResolverFallsBackToPathWithoutAdminHost(): void
    {
        $dir = sys_get_temp_dir() . '/webhemi-entry-' . bin2hex(random_bytes(4));
        mkdir($dir . '/var/config', 0775, true);
        $loader = new WebhemiConfigLoader($dir);
        $loader->save(new WebhemiConfig(
            adminAccess: AdminAccessMode::Domain,
            adminPath: '/admin',
            adminApiPath: '/admin/api',
            publicApiPath: '/api',
            loginPath: '/login',
            registerPath: '/register',
        ));

        $main = $this->siteHost('www.example.test', SurfaceType::Site, true);
        $hosts = $this->createStub(SiteHostRepository::class);
        $hosts->method('findMainSiteHost')->willReturn($main);
        $hosts->method('findMainAdminHost')->willReturn(null);

        $entry = (new AdminEntryResolver($loader, $hosts))->resolve();
        self::assertSame(AdminAccessMode::Path, $entry->effectiveMode);
        self::assertSame('www.example.test', $entry->canonicalHostname());

        $this->removeTree($dir);
    }

    public function testDomainModeRedirectsMainAdminPathToAdminHost(): void
    {
        $main = $this->siteHost('www.example.test', SurfaceType::Site, true);
        $admin = $this->siteHost('admin.example.test', SurfaceType::Admin, true);

        $holder = new HostContextHolder();
        $holder->set(new HostContext($main));

        $entry = $this->entry(AdminAccessMode::Domain, $main, $admin);
        $resolver = $this->resolverReturning($entry);

        $kernel = $this->createStub(HttpKernelInterface::class);
        $request = Request::create('https://www.example.test/admin/sites');
        $event = new RequestEvent($kernel, $request, HttpKernelInterface::MAIN_REQUEST);

        (new AdminAccessRedirectSubscriber($holder, $resolver))->onKernelRequest($event);

        $response = $event->getResponse();
        self::assertInstanceOf(RedirectResponse::class, $response);
        self::assertSame('https://admin.example.test/sites', $response->getTargetUrl());
    }

    public function testDomainModeRedirectsAdminHostAdminPathToCanonical(): void
    {
        $main = $this->siteHost('www.example.test', SurfaceType::Site, true);
        $admin = $this->siteHost('admin.example.test', SurfaceType::Admin, true);

        $holder = new HostContextHolder();
        $holder->set(new HostContext($admin));

        $entry = $this->entry(AdminAccessMode::Domain, $main, $admin);
        $resolver = $this->resolverReturning($entry);

        $kernel = $this->createStub(HttpKernelInterface::class);
        $request = Request::create('https://admin.example.test:8000/admin/login');
        $event = new RequestEvent($kernel, $request, HttpKernelInterface::MAIN_REQUEST);

        (new AdminAccessRedirectSubscriber($holder, $resolver))->onKernelRequest($event);

        $response = $event->getResponse();
        self::assertInstanceOf(RedirectResponse::class, $response);
        self::assertSame('https://admin.example.test:8000/login', $response->getTargetUrl());
    }

    public function testPathModeRedirectsAdminHostToMainAdmin(): void
    {
        $main = $this->siteHost('www.example.test', SurfaceType::Site, true);
        $admin = $this->siteHost('admin.example.test', SurfaceType::Admin, true);

        $holder = new HostContextHolder();
        $holder->set(new HostContext($admin));

        $entry = $this->entry(AdminAccessMode::Path, $main, $admin);
        $resolver = $this->resolverReturning($entry);

        $kernel = $this->createStub(HttpKernelInterface::class);
        $request = Request::create('https://admin.example.test/');
        $event = new RequestEvent($kernel, $request, HttpKernelInterface::MAIN_REQUEST);

        (new AdminAccessRedirectSubscriber($holder, $resolver))->onKernelRequest($event);

        $response = $event->getResponse();
        self::assertInstanceOf(RedirectResponse::class, $response);
        self::assertSame('https://www.example.test/admin', $response->getTargetUrl());
    }

    public function testPathModeRedirectPreservesNonDefaultPort(): void
    {
        $main = $this->siteHost('www.example.test', SurfaceType::Site, true);
        $admin = $this->siteHost('admin.example.test', SurfaceType::Admin, true);

        $holder = new HostContextHolder();
        $holder->set(new HostContext($admin));

        $entry = $this->entry(AdminAccessMode::Path, $main, $admin);
        $resolver = $this->resolverReturning($entry);

        $kernel = $this->createStub(HttpKernelInterface::class);
        $request = Request::create('https://admin.example.test:8000/');
        $event = new RequestEvent($kernel, $request, HttpKernelInterface::MAIN_REQUEST);

        (new AdminAccessRedirectSubscriber($holder, $resolver))->onKernelRequest($event);

        $response = $event->getResponse();
        self::assertInstanceOf(RedirectResponse::class, $response);
        self::assertSame('https://www.example.test:8000/admin', $response->getTargetUrl());
    }

    public function testOrphanSiteHostDoesNotRedirectAdminPath(): void
    {
        $orphan = (new SiteHost())
            ->setHost('127.0.0.1')
            ->setSurface(SurfaceType::Site)
            ->setVerification('verified')
            ->setIsEnabled(true);

        $holder = new HostContextHolder();
        $holder->set(new HostContext($orphan));

        $main = $this->siteHost('www.example.test', SurfaceType::Site, true);
        $entry = $this->entry(AdminAccessMode::Path, $main, null);
        $resolver = $this->resolverReturning($entry);

        $kernel = $this->createStub(HttpKernelInterface::class);
        $request = Request::create('https://127.0.0.1:8000/admin/login');
        $event = new RequestEvent($kernel, $request, HttpKernelInterface::MAIN_REQUEST);

        (new AdminAccessRedirectSubscriber($holder, $resolver))->onKernelRequest($event);

        self::assertNull($event->getResponse());
    }

    public function testRewriterMapsAdminHostRootToAdminPath(): void
    {
        $admin = $this->siteHost('admin.example.test', SurfaceType::Admin, true);
        $main = $this->siteHost('www.example.test', SurfaceType::Site, true);

        $holder = new HostContextHolder();
        $holder->set(new HostContext($admin));

        $entry = $this->entry(AdminAccessMode::Domain, $main, $admin);
        $resolver = $this->resolverReturning($entry);

        $kernel = $this->createStub(HttpKernelInterface::class);
        $request = Request::create('https://admin.example.test/');
        $event = new RequestEvent($kernel, $request, HttpKernelInterface::MAIN_REQUEST);

        (new AdminHostPathRewriterSubscriber($holder, $resolver))->onKernelRequest($event);

        self::assertSame('/admin', $event->getRequest()->getPathInfo());
    }

    public function testRewriterMapsAdminHostLoginToAdminLogin(): void
    {
        $admin = $this->siteHost('admin.example.test', SurfaceType::Admin, true);
        $main = $this->siteHost('www.example.test', SurfaceType::Site, true);

        $holder = new HostContextHolder();
        $holder->set(new HostContext($admin));

        $entry = $this->entry(AdminAccessMode::Domain, $main, $admin);
        $resolver = $this->resolverReturning($entry);

        $kernel = $this->createStub(HttpKernelInterface::class);
        $request = Request::create('https://admin.example.test/login');
        $event = new RequestEvent($kernel, $request, HttpKernelInterface::MAIN_REQUEST);

        (new AdminHostPathRewriterSubscriber($holder, $resolver))->onKernelRequest($event);

        self::assertSame('/admin/login', $event->getRequest()->getPathInfo());
    }

    public function testPathModeRedirectsAdminHostLoginToMainAdminLogin(): void
    {
        $main = $this->siteHost('www.example.test', SurfaceType::Site, true);
        $admin = $this->siteHost('admin.example.test', SurfaceType::Admin, true);

        $holder = new HostContextHolder();
        $holder->set(new HostContext($admin));

        $entry = $this->entry(AdminAccessMode::Path, $main, $admin);
        $resolver = $this->resolverReturning($entry);

        $kernel = $this->createStub(HttpKernelInterface::class);
        $request = Request::create('https://admin.example.test/login');
        $event = new RequestEvent($kernel, $request, HttpKernelInterface::MAIN_REQUEST);

        (new AdminAccessRedirectSubscriber($holder, $resolver))->onKernelRequest($event);

        $response = $event->getResponse();
        self::assertInstanceOf(RedirectResponse::class, $response);
        self::assertSame('https://www.example.test/admin/login', $response->getTargetUrl());
    }

    public function testOrphanAdminSurfaceHostIsNotFound(): void
    {
        $orphan = $this->siteHost('admin.blog.test', SurfaceType::Admin, false);
        $holder = new HostContextHolder();
        $holder->set(new HostContext($orphan));

        $main = $this->siteHost('www.example.test', SurfaceType::Site, true);
        $entry = $this->entry(AdminAccessMode::Path, $main, null);
        $resolver = $this->resolverReturning($entry);

        $kernel = $this->createStub(HttpKernelInterface::class);
        $request = Request::create('https://admin.blog.test/');
        $event = new RequestEvent($kernel, $request, HttpKernelInterface::MAIN_REQUEST);

        $this->expectException(NotFoundHttpException::class);
        (new AdminAccessRedirectSubscriber($holder, $resolver))->onKernelRequest($event);
    }

    private function entry(
        AdminAccessMode $mode,
        ?SiteHost $main,
        ?SiteHost $admin,
    ): \App\Routing\CanonicalAdminEntry {
        return new \App\Routing\CanonicalAdminEntry(
            effectiveMode: $mode,
            adminPath: '/admin',
            adminApiPath: '/admin/api',
            publicApiPath: '/api',
            mainSiteHost: $main,
            adminHost: $admin,
        );
    }

    private function resolverReturning(\App\Routing\CanonicalAdminEntry $entry): AdminEntryResolverInterface
    {
        return new class ($entry) implements AdminEntryResolverInterface {
            public function __construct(
                private readonly CanonicalAdminEntry $entry,
            ) {
            }

            public function resolve(): CanonicalAdminEntry
            {
                return $this->entry;
            }
        };
    }

    private function siteHost(string $hostname, SurfaceType $surface, bool $mainSite): SiteHost
    {
        $site = (new Site())
            ->setSlug($mainSite ? Site::MAIN_SLUG : 'blog')
            ->setName($mainSite ? 'Main site' : 'Blog');
        $host = (new SiteHost())
            ->setHost($hostname)
            ->setSurface($surface)
            ->setVerification('verified')
            ->setIsEnabled(true)
            ->setSite($site);

        return $host;
    }

    private function removeTree(string $dir): void
    {
        if (!is_dir($dir)) {
            return;
        }
        foreach (scandir($dir) ?: [] as $item) {
            if ($item === '.' || $item === '..') {
                continue;
            }
            $path = $dir . '/' . $item;
            is_dir($path) ? $this->removeTree($path) : unlink($path);
        }
        rmdir($dir);
    }
}
