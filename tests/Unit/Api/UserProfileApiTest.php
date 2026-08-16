<?php

declare(strict_types=1);

namespace App\Tests\Unit\Api;

use App\Api\UpdateUserProfileInput;
use App\Api\UserProfileApiMapper;
use App\Entity\User;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

final class UserProfileApiTest extends TestCase
{
    public function testUpdateInputValidatesEmailAndAvatarType(): void
    {
        $input = UpdateUserProfileInput::fromPayload([
            'email' => 'not-an-email',
            'avatarType' => 'nope',
        ]);
        self::assertFalse($input->isValid());
        self::assertArrayHasKey('email', $input->fieldErrors);
        self::assertArrayHasKey('avatarType', $input->fieldErrors);
    }

    public function testUpdateInputParsesLinks(): void
    {
        $input = UpdateUserProfileInput::fromPayload([
            'links' => [
                ['name' => 'GitHub', 'url' => 'https://github.com/example'],
            ],
            'displayName' => 'Ada',
        ]);
        self::assertTrue($input->isValid());
        self::assertTrue($input->linksProvided);
        self::assertSame('Ada', $input->displayName);
        self::assertSame(
            [['name' => 'GitHub', 'url' => 'https://github.com/example']],
            $input->links,
        );
    }

    public function testGravatarUrl(): void
    {
        $url = UserProfileApiMapper::gravatarUrl('Ada@Example.COM');
        self::assertSame(
            'https://gravatar.com/avatar/'.md5('ada@example.com').'?s=150&d=identicon',
            $url,
        );
    }

    public function testMapperDefaultAvatarUrlIsNull(): void
    {
        $urls = $this->createMock(UrlGeneratorInterface::class);
        $mapper = new UserProfileApiMapper($urls);
        $user = (new User())->setEmail('a@b.co');
        $data = $mapper->toArray($user);
        self::assertSame('default', $data['avatarType']);
        self::assertNull($data['avatarUrl']);
        self::assertSame([], $data['links']);
    }
}
