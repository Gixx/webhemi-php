<?php

declare(strict_types=1);

namespace App\Api;

use App\Entity\User;

/**
 * Parsed body for PATCH /admin/api/me/profile.
 */
final class UpdateUserProfileInput
{
    /**
     * @param list<array{name: string, url: string}>|null $links
     * @param array<string, string>                       $fieldErrors
     */
    private function __construct(
        public readonly bool $emailProvided,
        public readonly ?string $email,
        public readonly bool $displayNameProvided,
        public readonly ?string $displayName,
        public readonly bool $telephoneProvided,
        public readonly ?string $telephone,
        public readonly bool $addressProvided,
        public readonly ?string $address,
        public readonly bool $zipProvided,
        public readonly ?string $zip,
        public readonly bool $cityProvided,
        public readonly ?string $city,
        public readonly bool $countryProvided,
        public readonly ?string $country,
        public readonly bool $bioProvided,
        public readonly ?string $bio,
        public readonly bool $avatarTypeProvided,
        public readonly ?string $avatarType,
        public readonly bool $linksProvided,
        public readonly ?array $links,
        public readonly array $fieldErrors,
    ) {
    }

    public static function fromPayload(mixed $payload): self
    {
        if (!\is_array($payload)) {
            return new self(
                false,
                null,
                false,
                null,
                false,
                null,
                false,
                null,
                false,
                null,
                false,
                null,
                false,
                null,
                false,
                null,
                false,
                null,
                false,
                null,
                ['_body' => 'JSON object required.'],
            );
        }

        $fields = [];

        $emailProvided = \array_key_exists('email', $payload);
        $email = null;
        if ($emailProvided) {
            $email = strtolower(trim((string) $payload['email']));
            if ('' === $email || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $fields['email'] = 'A valid email is required.';
            }
        }

        $stringFields = [
            'displayName' => 128,
            'telephone' => 64,
            'address' => 255,
            'zip' => 32,
            'city' => 128,
            'country' => 128,
        ];
        $parsedStrings = [];
        foreach ($stringFields as $key => $max) {
            $provided = \array_key_exists($key, $payload);
            $value = null;
            if ($provided) {
                if (null === $payload[$key] || '' === $payload[$key]) {
                    $value = null;
                } else {
                    $value = trim((string) $payload[$key]);
                    if (strlen($value) > $max) {
                        $fields[$key] = sprintf('%s is too long.', ucfirst($key));
                    }
                }
            }
            $parsedStrings[$key] = [$provided, $value];
        }

        $bioProvided = \array_key_exists('bio', $payload);
        $bio = null;
        if ($bioProvided) {
            if (null === $payload['bio'] || '' === $payload['bio']) {
                $bio = null;
            } else {
                $bio = (string) $payload['bio'];
                if (strlen($bio) > 10000) {
                    $fields['bio'] = 'Bio is too long.';
                }
            }
        }

        $avatarTypeProvided = \array_key_exists('avatarType', $payload);
        $avatarType = null;
        if ($avatarTypeProvided) {
            $avatarType = strtolower(trim((string) $payload['avatarType']));
            if (!\in_array($avatarType, [User::AVATAR_DEFAULT, User::AVATAR_GRAVATAR, User::AVATAR_UPLOAD], true)) {
                $fields['avatarType'] = 'Avatar type must be default, gravatar, or upload.';
            }
        }

        $linksProvided = \array_key_exists('links', $payload);
        $links = null;
        if ($linksProvided) {
            if (!\is_array($payload['links'])) {
                $fields['links'] = 'Links must be an array.';
            } else {
                $links = [];
                foreach ($payload['links'] as $index => $row) {
                    if (!\is_array($row)) {
                        $fields['links'] = 'Each link must be an object.';
                        break;
                    }
                    $name = trim((string) ($row['name'] ?? ''));
                    $url = trim((string) ($row['url'] ?? ''));
                    if ('' === $name) {
                        $fields['links'] = 'Link name is required.';
                        break;
                    }
                    if ('' === $url || !filter_var($url, FILTER_VALIDATE_URL)) {
                        $fields['links'] = 'Link URL must be a valid URL.';
                        break;
                    }
                    if (strlen($name) > 128) {
                        $fields['links'] = 'Link name is too long.';
                        break;
                    }
                    if (strlen($url) > 2048) {
                        $fields['links'] = 'Link URL is too long.';
                        break;
                    }
                    $links[] = ['name' => $name, 'url' => $url];
                    unset($index);
                }
            }
        }

        return new self(
            $emailProvided,
            $email,
            $parsedStrings['displayName'][0],
            $parsedStrings['displayName'][1],
            $parsedStrings['telephone'][0],
            $parsedStrings['telephone'][1],
            $parsedStrings['address'][0],
            $parsedStrings['address'][1],
            $parsedStrings['zip'][0],
            $parsedStrings['zip'][1],
            $parsedStrings['city'][0],
            $parsedStrings['city'][1],
            $parsedStrings['country'][0],
            $parsedStrings['country'][1],
            $bioProvided,
            $bio,
            $avatarTypeProvided,
            $avatarType,
            $linksProvided,
            $links,
            $fields,
        );
    }

    public function isValid(): bool
    {
        return [] === $this->fieldErrors;
    }

    public function hasChanges(): bool
    {
        return $this->emailProvided
            || $this->displayNameProvided
            || $this->telephoneProvided
            || $this->addressProvided
            || $this->zipProvided
            || $this->cityProvided
            || $this->countryProvided
            || $this->bioProvided
            || $this->avatarTypeProvided
            || $this->linksProvided;
    }
}
