<?php

declare(strict_types=1);

namespace App\Tests\Unit\Api;

use App\Api\UserAccess;
use App\Entity\Role;
use App\Entity\User;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

final class UserAccessTest extends TestCase
{
    public function testAdminGetsAllCapabilities(): void
    {
        $adminRole = (new Role())->setName(Role::ADMIN)->setLabel('Admin');
        $actor = (new User())->setEmail('admin@webhemi.local')->addRole($adminRole);
        $ref = new \ReflectionProperty(User::class, 'id');
        $ref->setValue($actor, 1);

        $auth = $this->createMock(AuthorizationCheckerInterface::class);
        $auth->expects(self::never())->method('isGranted');

        $access = new UserAccess($auth);
        self::assertTrue($access->canListUsers($actor));
        self::assertTrue($access->canCreateUser($actor));
        self::assertTrue($access->canEditUser($actor));
        self::assertTrue($access->canDeleteUser($actor));
        self::assertSame(
            [
                'listUsers' => true,
                'viewUser' => true,
                'createUser' => true,
                'editUser' => true,
                'deleteUser' => true,
            ],
            $access->capabilities($actor),
        );
    }

    public function testSelfCanViewAndSetPasswordWithoutList(): void
    {
        $actor = (new User())->setEmail('me@webhemi.local');
        $ref = new \ReflectionProperty(User::class, 'id');
        $ref->setValue($actor, 5);

        $other = (new User())->setEmail('other@webhemi.local');
        $otherRef = new \ReflectionProperty(User::class, 'id');
        $otherRef->setValue($other, 9);

        $auth = $this->createMock(AuthorizationCheckerInterface::class);
        $auth->method('isGranted')->willReturn(false);

        $access = new UserAccess($auth);
        self::assertFalse($access->canListUsers($actor));
        self::assertTrue($access->canViewUser($actor, $actor));
        self::assertFalse($access->canViewUser($actor, $other));
        self::assertTrue($access->canSetPassword($actor, $actor));
        self::assertFalse($access->canSetPassword($actor, $other));
    }

    public function testEditGrantAllowsResetOtherPassword(): void
    {
        $actor = (new User())->setEmail('ops@webhemi.local');
        $ref = new \ReflectionProperty(User::class, 'id');
        $ref->setValue($actor, 2);

        $other = (new User())->setEmail('other@webhemi.local');
        $otherRef = new \ReflectionProperty(User::class, 'id');
        $otherRef->setValue($other, 3);

        $auth = $this->createMock(AuthorizationCheckerInterface::class);
        $auth->method('isGranted')->willReturnCallback(
            static fn (string $attr): bool => 'user.edit' === $attr,
        );

        $access = new UserAccess($auth);
        self::assertTrue($access->canEditUser($actor));
        self::assertTrue($access->canSetPassword($actor, $other));
        self::assertFalse($access->canListUsers($actor));
    }
}
