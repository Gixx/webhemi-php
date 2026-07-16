<?php

declare(strict_types=1);

namespace App\Tests\Unit\Security;

use App\Entity\Permission;
use App\Entity\Role;
use App\Entity\User;
use App\Repository\SiteAssignmentRepository;
use App\Security\Voter\PermissionVoter;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;

final class PermissionVoterTest extends TestCase
{
    public function testAdminRoleGrantsAnyPermission(): void
    {
        $voter = new PermissionVoter($this->createStub(SiteAssignmentRepository::class));

        $adminRole = (new Role())->setName('ROLE_ADMIN')->setLabel('Admin');
        $user = (new User())->setEmail('admin@example.com')->addRole($adminRole);
        $token = new UsernamePasswordToken($user, 'main', $user->getRoles());

        $method = (new \ReflectionClass($voter))->getMethod('voteOnAttribute');

        self::assertTrue($method->invoke($voter, 'site.list', null, $token));
    }

    public function testPermissionOnGlobalRolesViaAssignmentRequiresAssignment(): void
    {
        $assignments = $this->createStub(SiteAssignmentRepository::class);
        $assignments->method('findBy')->willReturn([]);
        $voter = new PermissionVoter($assignments);

        $user = (new User())->setEmail('editor@example.com');
        $token = new UsernamePasswordToken($user, 'main', $user->getRoles());

        $method = (new \ReflectionClass($voter))->getMethod('voteOnAttribute');

        self::assertFalse($method->invoke($voter, 'site.list', null, $token));
    }

    public function testSupportsDotPermissionsOnly(): void
    {
        $voter = new PermissionVoter($this->createStub(SiteAssignmentRepository::class));
        $method = (new \ReflectionClass($voter))->getMethod('supports');

        self::assertTrue($method->invoke($voter, 'site.list', null));
        self::assertFalse($method->invoke($voter, 'ROLE_ADMIN', null));
    }

    public function testRoleHasPermissionHelper(): void
    {
        $permission = (new Permission())->setName('host.verify')->setLabel('Verify');
        $role = (new Role())->setName('ROLE_EDITOR')->setLabel('Editor')->addPermission($permission);

        self::assertTrue($role->hasPermission('host.verify'));
        self::assertFalse($role->hasPermission('user.edit'));
    }
}
