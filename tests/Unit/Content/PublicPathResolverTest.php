<?php

declare(strict_types=1);

namespace App\Tests\Unit\Content;

use App\Config\WebhemiConfigLoader;
use App\Content\PublicationVisibility;
use App\Content\PublicPathResolver;
use App\Entity\ContentNode;
use App\Entity\ContentNodeKind;
use App\Entity\ContentTree;
use App\Entity\PublicationStatus;
use App\Entity\Site;
use App\Repository\ContentNodeRepository;
use App\Routing\ReservedPaths;
use PHPUnit\Framework\TestCase;

final class PublicPathResolverTest extends TestCase
{
    private string $projectDir;

    protected function setUp(): void
    {
        $this->projectDir = sys_get_temp_dir() . '/wh-path-' . uniqid('', true);
        mkdir($this->projectDir . '/var/config', 0777, true);
        file_put_contents(
            $this->projectDir . '/var/config/webhemi.yaml',
            <<<'YAML'
webhemi:
  access:
    admin: path
  paths:
    admin: /admin
    admin_api: /admin/api
    public_api: /api
    login: /login
    register: /register
YAML
        );
    }

    protected function tearDown(): void
    {
        @unlink($this->projectDir . '/var/config/webhemi.yaml');
        @rmdir($this->projectDir . '/var/config');
        @rmdir($this->projectDir . '/var');
        @rmdir($this->projectDir);
    }

    public function testRootIndex(): void
    {
        $resolver = $this->resolver([]);
        $hit = $resolver->resolve($this->site(), '/');
        self::assertNotNull($hit);
        self::assertTrue($hit->isRootIndex);
    }

    public function testReservedPathRejected(): void
    {
        $resolver = $this->resolver([]);
        self::assertNull($resolver->resolve($this->site(), '/admin'));
        self::assertNull($resolver->resolve($this->site(), '/api'));
        self::assertNull($resolver->resolve($this->site(), '/login'));
    }

    public function testFolderRequiresTrailingSlash(): void
    {
        $docs = $this->folder('docs');
        $resolver = $this->resolver([
            [null, 'docs', $docs],
        ]);
        self::assertNull($resolver->resolve($this->site(), '/docs'));
        $hit = $resolver->resolve($this->site(), '/docs/');
        self::assertNotNull($hit);
        self::assertSame($docs, $hit->node);
        self::assertSame('/docs/', $hit->path);
    }

    public function testDocumentHtmlLeaf(): void
    {
        $docs = $this->folder('docs');
        $guide = $this->document('guide');
        $resolver = $this->resolver([
            [null, 'docs', $docs],
            [$docs, 'guide', $guide],
        ]);
        $hit = $resolver->resolve($this->site(), '/docs/guide.html');
        self::assertNotNull($hit);
        self::assertSame($guide, $hit->node);
        self::assertSame('/docs/guide.html', $hit->path);
    }

    public function testDraftLeafNotFound(): void
    {
        $draft = $this->document('secret')->setPublication(PublicationStatus::Draft);
        $resolver = $this->resolver([
            [null, 'secret', $draft],
        ]);
        self::assertNull($resolver->resolve($this->site(), '/secret.html'));
    }

    public function testChildHref(): void
    {
        $resolver = $this->resolver([]);
        self::assertSame(
            '/docs/',
            $resolver->childHref('/', $this->folder('docs')),
        );
        self::assertSame(
            '/docs/a.html',
            $resolver->childHref('/docs/', $this->document('a')),
        );
    }

    /**
     * @param list<array{0: ?ContentNode, 1: string, 2: ContentNode}> $map
     */
    private function resolver(array $map): PublicPathResolver
    {
        $nodes = $this->createMock(ContentNodeRepository::class);
        $nodes->method('findLiveSiblingSlug')->willReturnCallback(
            function (Site $site, ContentTree $tree, ?ContentNode $parent, string $slug) use ($map): ?ContentNode {
                foreach ($map as [$p, $s, $node]) {
                    if ($p === $parent && $s === $slug) {
                        return $node;
                    }
                }

                return null;
            },
        );

        return new PublicPathResolver(
            $nodes,
            new PublicationVisibility(),
            new ReservedPaths(new WebhemiConfigLoader($this->projectDir)),
        );
    }

    private function site(): Site
    {
        return (new Site())->setSlug('main')->setName('Main');
    }

    private function folder(string $slug): ContentNode
    {
        return (new ContentNode())
            ->setKind(ContentNodeKind::Folder)
            ->setSlug($slug)
            ->setTitle($slug)
            ->setPublication(PublicationStatus::Published);
    }

    private function document(string $slug): ContentNode
    {
        return (new ContentNode())
            ->setKind(ContentNodeKind::Document)
            ->setSlug($slug)
            ->setTitle($slug)
            ->setPublication(PublicationStatus::Published);
    }
}
