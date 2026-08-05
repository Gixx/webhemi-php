<?php

declare(strict_types=1);

namespace App\Tests\Unit\SiteHost;

use App\SiteHost\Verification\HostOwnershipVerifier;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

final class HostOwnershipVerifierTest extends TestCase
{
    public function testAlternateSchemeKeepsNonDefaultPort(): void
    {
        $stack = new RequestStack();
        $stack->push(Request::create('https://admin.webhemi.local:8000/admin/api/hosts/1/verify'));

        $verifier = new HostOwnershipVerifier(sys_get_temp_dir(), $stack);
        $urls = $verifier->buildVerificationUrls('haho.mysite.local', 'abcd1234.txt');

        self::assertSame([
            'https://haho.mysite.local:8000/abcd1234.txt',
            'http://haho.mysite.local:8000/abcd1234.txt',
        ], $urls);
    }

    public function testDefaultPortsOmitExplicitPort(): void
    {
        $stack = new RequestStack();
        $stack->push(Request::create('https://admin.webhemi.local/admin'));

        $verifier = new HostOwnershipVerifier(sys_get_temp_dir(), $stack);
        $urls = $verifier->buildVerificationUrls('www.example.test', 'tok.txt');

        self::assertSame([
            'https://www.example.test/tok.txt',
            'http://www.example.test/tok.txt',
        ], $urls);
    }
}
