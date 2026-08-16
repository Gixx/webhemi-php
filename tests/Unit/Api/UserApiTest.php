<?php

declare(strict_types=1);

namespace App\Tests\Unit\Api;

use App\Api\CreateUserInput;
use App\Api\SetUserPasswordInput;
use App\Api\UserApiMapper;
use App\Api\UserCreator;
use App\Api\UserDeleter;
use App\Api\UserEmailTakenException;
use App\Api\UserInvalidRoleException;
use App\Api\UserLastAdminException;
use App\Api\UserPasswordMismatchException;
use App\Api\UserPasswordSetter;
use App\Api\UserRoleSync;
use App\Api\UserSelfDeleteException;
use App\Api\UserUpdater;
use App\Api\UpdateUserInput;
use App\Entity\Role;
use App\Entity\Site;
use App\Entity\User;
use App\Repository\RoleRepository;
use App\Repository\SiteRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

final class UserApiTest extends TestCase
{
    public function testCreateInputRequiresPasswordAndValidEmail(): void
    {
        $bad = CreateUserInput::fromPayload(['email' => 'nope', 'password' => 'short']);
        self::assertFalse($bad->isValid());
        self::assertArrayHasKey('email', $bad->fieldErrors);
        self::assertArrayHasKey('password', $bad->fieldErrors);
        self::assertArrayHasKey('displayName', $bad->fieldErrors);

        $ok = CreateUserInput::fromPayload([
            'email' => ' Editor@Example.com ',
            'password' => 'password1',
            'displayName' => 'Editor',
            'roleIds' => [1],
        ]);
        self::assertTrue($ok->isValid());
        self::assertSame('editor@example.com', $ok->email);
        self::assertSame('Editor', $ok->displayName);
    }

    public function testUpdateOptionalPassword(): void
    {
        $rejected = UpdateUserInput::fromPayload(['email' => 'a@b.co', 'password' => 'short']);
        self::assertFalse($rejected->isValid());
        self::assertArrayHasKey('password', $rejected->fieldErrors);

        $omitEmpty = UpdateUserInput::fromPayload(['email' => 'a@b.co', 'password' => '']);
        self::assertTrue($omitEmpty->isValid());
        self::assertFalse($omitEmpty->passwordProvided);

        $ok = UpdateUserInput::fromPayload(['password' => 'password1']);
        self::assertTrue($ok->isValid());
        self::assertTrue($ok->passwordProvided);
        self::assertSame('password1', $ok->password);
    }

    public function testMapperShape(): void
    {
        $admin = (new Role())->setName(Role::ADMIN)->setLabel('Administrator');
        $adminRef = new \ReflectionProperty(Role::class, 'id');
        $adminRef->setValue($admin, 1);

        $user = (new User())->setEmail('admin@webhemi.local')->addRole($admin);
        $userRef = new \ReflectionProperty(User::class, 'id');
        $userRef->setValue($user, 7);

        $data = UserApiMapper::toArray($user);
        self::assertSame(7, $data['id']);
        self::assertSame('admin@webhemi.local', $data['email']);
        self::assertSame([1], $data['roleIds']);
        self::assertSame(1, $data['roleCount']);
        self::assertSame(0, $data['siteAssignmentCount']);
    }

    public function testCreateHashesPasswordAndAssignsRoles(): void
    {
        $admin = (new Role())->setName(Role::ADMIN)->setLabel('Administrator');
        $adminRef = new \ReflectionProperty(Role::class, 'id');
        $adminRef->setValue($admin, 1);

        $input = CreateUserInput::fromPayload([
            'email' => 'new@webhemi.local',
            'password' => 'password1',
            'displayName' => 'New User',
            'roleIds' => [1],
        ]);
        self::assertTrue($input->isValid());

        $users = $this->createMock(UserRepository::class);
        $users->expects(self::once())->method('findOneBy')->with(['email' => 'new@webhemi.local'])->willReturn(null);

        $roles = $this->createMock(RoleRepository::class);
        $roles->method('find')->with(1)->willReturn($admin);

        $sites = $this->createStub(SiteRepository::class);
        $sync = new UserRoleSync($roles, $sites, $users);

        $hasher = $this->createMock(UserPasswordHasherInterface::class);
        $hasher->expects(self::once())
            ->method('hashPassword')
            ->willReturn('hashed');

        $em = $this->createMock(EntityManagerInterface::class);
        $em->expects(self::once())->method('persist')->with(self::isInstanceOf(User::class));
        $em->expects(self::once())->method('flush');

        $user = (new UserCreator($users, $roles, $sync, $hasher, $em))->create($input);
        self::assertSame('new@webhemi.local', $user->getEmail());
        self::assertSame('hashed', $user->getPassword());
        self::assertTrue($user->hasRoleName(Role::ADMIN));
    }

    public function testSiteAdminNotGlobal(): void
    {
        $siteAdmin = (new Role())->setName(Role::SITE_ADMIN)->setLabel('Site Admin');
        $ref = new \ReflectionProperty(Role::class, 'id');
        $ref->setValue($siteAdmin, 2);

        $roles = $this->createMock(RoleRepository::class);
        $roles->method('find')->with(2)->willReturn($siteAdmin);
        $sites = $this->createStub(SiteRepository::class);
        $users = $this->createStub(UserRepository::class);
        $sync = new UserRoleSync($roles, $sites, $users);

        $this->expectException(UserInvalidRoleException::class);
        $sync->syncGlobalRoles(new User(), [2], enforceLastAdmin: false);
    }

    public function testAdminNotSiteRole(): void
    {
        $admin = (new Role())->setName(Role::ADMIN)->setLabel('Administrator');
        $adminRef = new \ReflectionProperty(Role::class, 'id');
        $adminRef->setValue($admin, 1);

        $site = (new Site())->setName('Main')->setSlug('main');
        $siteRef = new \ReflectionProperty(Site::class, 'id');
        $siteRef->setValue($site, 10);

        $roles = $this->createMock(RoleRepository::class);
        $roles->method('find')->with(1)->willReturn($admin);
        $sites = $this->createMock(SiteRepository::class);
        $sites->method('find')->with(10)->willReturn($site);
        $users = $this->createStub(UserRepository::class);
        $sync = new UserRoleSync($roles, $sites, $users);

        $this->expectException(UserInvalidRoleException::class);
        $sync->syncSiteAssignments(new User(), [['siteId' => 10, 'roleId' => 1]]);
    }

    public function testDeleteSelfThrows(): void
    {
        $user = (new User())->setEmail('a@b.co');
        $ref = new \ReflectionProperty(User::class, 'id');
        $ref->setValue($user, 3);

        $em = $this->createMock(EntityManagerInterface::class);
        $em->expects(self::never())->method('remove');

        $this->expectException(UserSelfDeleteException::class);
        (new UserDeleter($this->createStub(UserRepository::class), $em))->delete($user, $user);
    }

    public function testDeleteLastAdminThrows(): void
    {
        $admin = (new Role())->setName(Role::ADMIN)->setLabel('Administrator');
        $user = (new User())->setEmail('a@b.co')->addRole($admin);
        $ref = new \ReflectionProperty(User::class, 'id');
        $ref->setValue($user, 3);

        $users = $this->createMock(UserRepository::class);
        $users->expects(self::once())->method('countAdmins')->with(3)->willReturn(0);

        $em = $this->createMock(EntityManagerInterface::class);
        $em->expects(self::never())->method('remove');

        $this->expectException(UserLastAdminException::class);
        (new UserDeleter($users, $em))->delete($user, null);
    }

    public function testUpdateDuplicateEmailThrows(): void
    {
        $user = (new User())->setEmail('a@b.co');
        $ref = new \ReflectionProperty(User::class, 'id');
        $ref->setValue($user, 1);

        $other = (new User())->setEmail('taken@b.co');
        $otherRef = new \ReflectionProperty(User::class, 'id');
        $otherRef->setValue($other, 2);

        $input = UpdateUserInput::fromPayload(['email' => 'taken@b.co']);
        self::assertTrue($input->isValid());

        $users = $this->createMock(UserRepository::class);
        $users->method('findOneBy')->with(['email' => 'taken@b.co'])->willReturn($other);

        $roles = $this->createStub(RoleRepository::class);
        $sites = $this->createStub(SiteRepository::class);
        $sync = new UserRoleSync($roles, $sites, $users);
        $em = $this->createMock(EntityManagerInterface::class);
        $em->expects(self::never())->method('flush');

        $this->expectException(UserEmailTakenException::class);
        (new UserUpdater(
            $users,
            $sync,
            $this->createStub(UserPasswordHasherInterface::class),
            $em,
        ))->update($user, $input);
    }

    public function testPasswordInputRequiresCurrentWhenSelf(): void
    {
        $self = SetUserPasswordInput::fromPayload(
            ['password' => 'password1', 'confirmPassword' => 'password1'],
            requireCurrentPassword: true,
        );
        self::assertFalse($self->isValid());
        self::assertArrayHasKey('currentPassword', $self->fieldErrors);

        $other = SetUserPasswordInput::fromPayload(
            ['password' => 'password1', 'confirmPassword' => 'password1'],
            requireCurrentPassword: false,
        );
        self::assertTrue($other->isValid());
        self::assertFalse($other->requireCurrentPassword);
    }

    public function testPasswordSetterSkipsCurrentCheckForAdminReset(): void
    {
        $user = (new User())->setEmail('a@b.co')->setPassword('old-hash');
        $input = SetUserPasswordInput::fromPayload(
            ['password' => 'password1'],
            requireCurrentPassword: false,
        );
        self::assertTrue($input->isValid());

        $hasher = $this->createMock(UserPasswordHasherInterface::class);
        $hasher->expects(self::never())->method('isPasswordValid');
        $hasher->expects(self::once())
            ->method('hashPassword')
            ->willReturn('new-hash');

        $em = $this->createMock(EntityManagerInterface::class);
        $em->expects(self::once())->method('flush');

        (new UserPasswordSetter($hasher, $em))->setPassword($user, $input);
        self::assertSame('new-hash', $user->getPassword());
    }

    public function testPasswordSetterVerifiesCurrentForSelf(): void
    {
        $user = (new User())->setEmail('a@b.co')->setPassword('old-hash');
        $input = SetUserPasswordInput::fromPayload([
            'currentPassword' => 'wrong',
            'password' => 'password1',
        ]);
        self::assertTrue($input->isValid());

        $hasher = $this->createMock(UserPasswordHasherInterface::class);
        $hasher->expects(self::once())
            ->method('isPasswordValid')
            ->with($user, 'wrong')
            ->willReturn(false);
        $hasher->expects(self::never())->method('hashPassword');

        $em = $this->createMock(EntityManagerInterface::class);
        $em->expects(self::never())->method('flush');

        $this->expectException(UserPasswordMismatchException::class);
        (new UserPasswordSetter($hasher, $em))->setPassword($user, $input);
    }
}
