<?php

declare(strict_types=1);

namespace App\SiteHost\Verification;

use Symfony\Component\HttpFoundation\RequestStack;

final class HostOwnershipVerifier implements HostOwnershipProbe
{
    private const CONNECT_TIMEOUT_SECONDS = 2;

    public function __construct(
        private readonly string $projectDir,
        private readonly RequestStack $requestStack,
    ) {
    }

    public function verify(string $host): HostVerificationResult
    {
        $token = bin2hex(random_bytes(4));
        $fileName = $token . '.txt';
        $expectedContent = 'webhemi-host-verification:' . $token;
        $verificationFilePath = $this->projectDir . '/public/' . $fileName;

        if (false === @file_put_contents($verificationFilePath, $expectedContent, LOCK_EX)) {
            return new HostVerificationResult(false);
        }

        $verificationUrls = $this->buildVerificationUrls($host, $fileName);

        try {
            foreach ($verificationUrls as $verificationUrl) {
                $context = stream_context_create([
                    'http' => [
                        'method' => 'GET',
                        'timeout' => self::CONNECT_TIMEOUT_SECONDS,
                        'ignore_errors' => true,
                        'user_agent' => 'WebHemiHostVerifier/1.0',
                    ],
                    // Candidate hostnames often are not on the TLS cert yet; the
                    // probe proves the Host header reaches this install + token body.
                    'ssl' => [
                        'verify_peer' => false,
                        'verify_peer_name' => false,
                    ],
                ]);

                $responseBody = @file_get_contents($verificationUrl, false, $context);
                if (!is_string($responseBody)) {
                    continue;
                }

                if (trim($responseBody) === $expectedContent) {
                    return new HostVerificationResult(true, $verificationUrl);
                }
            }

            return new HostVerificationResult(false);
        } finally {
            if (is_file($verificationFilePath)) {
                @unlink($verificationFilePath);
            }
        }
    }

    /**
     * @return list<string>
     */
    public function buildVerificationUrls(string $host, string $fileName): array
    {
        $currentRequest = $this->requestStack->getCurrentRequest();
        $scheme = $currentRequest?->getScheme();
        $port = $currentRequest?->getPort();

        if ('http' === $scheme || 'https' === $scheme) {
            $alternate = 'http' === $scheme ? 'https' : 'http';
            // Propagate only custom ports (e.g. :8000). Default 80/443 stay scheme-native.
            $explicitPort = null !== $port && $this->defaultPortForScheme($scheme) !== $port
                ? $port
                : null;

            return array_values(array_unique([
                $this->buildVerificationUrl($scheme, $host, $fileName, $explicitPort),
                $this->buildVerificationUrl($alternate, $host, $fileName, $explicitPort),
            ]));
        }

        return [
            $this->buildVerificationUrl('http', $host, $fileName),
            $this->buildVerificationUrl('https', $host, $fileName),
        ];
    }

    private function buildVerificationUrl(string $scheme, string $host, string $fileName, ?int $port = null): string
    {
        $url = $scheme . '://' . $host;

        if (null !== $port && $this->defaultPortForScheme($scheme) !== $port) {
            $url .= ':' . $port;
        }

        return $url . '/' . $fileName;
    }

    private function defaultPortForScheme(string $scheme): int
    {
        return 'https' === $scheme ? 443 : 80;
    }
}
