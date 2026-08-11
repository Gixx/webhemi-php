<?php

declare(strict_types=1);

namespace App\Tests\Unit\Security;

use App\Entity\Permission;
use App\Entity\Role;
use App\Entity\Site;
use App\Entity\SiteAssignment;
use App\Entity\User;
use App\Repository\SiteAssignmentRepository;
use App\Security\RbacAttributes;
use App\Security\Voter\PermissionVoter;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;

final class PermissionVoterTest extends TestCase
{
    public function testAdminRoleGrantsAnyPermission(): void
    {
        $voter = new PermissionVoter($this->createStub(SiteAssignmentRepository::class));

        $adminRole = (new Role())->setName(Role::ADMIN)->setLabel('Admin')->setIsReadOnly(true);
        $user = (new User())->setEmail('admin@example.com')->addRole($adminRole);
        $token = new UsernamePasswordToken($user, 'main', $user->getRoles());

        $method = (new \ReflectionClass($voter))->getMethod('voteOnAttribute');

        self::assertTrue($method->invoke($voter, 'site.list', null, $token));
        self::assertTrue($method->invoke($voter, 'host.delete', null, $token));
        self::assertTrue($method->invoke($voter, 'settings.edit', null, $token));
    }

    public function testUserWithoutAssignmentIsDenied(): void
    {
        $assignments = $this->createStub(SiteAssignmentRepository::class);
        $assignments->method('findBy')->willReturn([]);
        $voter = new PermissionVoter($assignments);

        $user = (new User())->setEmail('editor@example.com');
        $token = new UsernamePasswordToken($user, 'main', $user->getRoles());

        $method = (new \ReflectionClass($voter))->getMethod('voteOnAttribute');

        self::assertFalse($method->invoke($voter, 'site.list', null, $token));
    }

    public function testSiteAdminAllowsInteriorAndDeniesHosts(): void
    {
        $site = (new Site())->setSlug('blog')->setName('Blog');
        $this->setEntityId($site, 7);

        $siteAdmin = (new Role())
            ->setName(Role::SITE_ADMIN)
            ->setLabel('Site Administrator')
            ->setIsReadOnly(true);

        $user = (new User())->setEmail('site@example.com');
        $assignment = (new SiteAssignment())
            ->setUser($user)
            ->setSite($site)
            ->setRole($siteAdmin);

        $assignments = $this->createStub(SiteAssignmentRepository::class);
        $assignments->method('findForUserAndSite')->willReturn($assignment);
        $assignments->method('findBy')->willReturn([$assignment]);

        $voter = new PermissionVoter($assignments);
        $token = new UsernamePasswordToken($user, 'main', $user->getRoles());
        $method = (new \ReflectionClass($voter))->getMethod('voteOnAttribute');

        self::assertTrue($method->invoke($voter, 'site.list', 7, $token));
        self::assertTrue($method->invoke($voter, 'content.edit', 7, $token));
        self::assertFalse($method->invoke($voter, 'site.edit', 7, $token));
        self::assertFalse($method->invoke($voter, 'host.edit', 7, $token));
        self::assertFalse($method->invoke($voter, 'settings.list', 7, $token));
    }

    public function testCustomRoleRequiresExplicitPermission(): void
    {
        $site = (new Site())->setSlug('blog')->setName('Blog');
        $this->setEntityId($site, 3);

        $perm = (new Permission())->setName('content.edit')->setLabel('Edit content');
        $custom = (new Role())->setName('ROLE_AUTHOR')->setLabel('Author')->addPermission($perm);

        $user = (new User())->setEmail('author@example.com');
        $assignment = (new SiteAssignment())
            ->setUser($user)
            ->setSite($site)
            ->setRole($custom);

        $assignments = $this->createStub(SiteAssignmentRepository::class);
        $assignments->method('findForUserAndSite')->willReturn($assignment);

        $voter = new PermissionVoter($assignments);
        $token = new UsernamePasswordToken($user, 'main', $user->getRoles());
        $method = (new \ReflectionClass($voter))->getMethod('voteOnAttribute');

        self::assertTrue($method->invoke($voter, 'content.edit', 3, $token));
        self::assertFalse($method->invoke($voter, 'content.delete', 3, $token));
        self::assertFalse($method->invoke($voter, 'host.list', 3, $token));
    }

    public function testCustomGlobalRoleGrantsCatalogPermission(): void
    {
        $perm = (new Permission())->setName('user.edit')->setLabel('Edit users');
        $custom = (new Role())->setName('ROLE_USER_MANAGER')->setLabel('User manager')->addPermission($perm);
        $user = (new User())->setEmail('mgr@example.com')->addRole($custom);

        $assignments = $this->createStub(SiteAssignmentRepository::class);
        $assignments->method('findBy')->willReturn([]);

        $voter = new PermissionVoter($assignments);
        $token = new UsernamePasswordToken($user, 'main', $user->getRoles());
        $method = (new \ReflectionClass($voter))->getMethod('voteOnAttribute');

        self::assertTrue($method->invoke($voter, 'user.edit', null, $token));
        self::assertFalse($method->invoke($voter, 'user.delete', null, $token));
    }

    public function testSupportsDotPermissionsOnly(): void
    {
        $voter = new PermissionVoter($this->createStub(SiteAssignmentRepository::class));
        $method = (new \ReflectionClass($voter))->getMethod('supports');

        self::assertTrue($method->invoke($voter, 'site.list', null));
        self::assertFalse($method->invoke($voter, 'ROLE_ADMIN', null));
    }

    public function testRbacAttributesAdminOnlyMatrix(): void
    {
        self::assertTrue(RbacAttributes::isAdminOnly('host.edit'));
        self::assertTrue(RbacAttributes::isAdminOnly('settings.list'));
        self::assertTrue(RbacAttributes::isAdminOnly('site.edit'));
        self::assertTrue(RbacAttributes::isAdminOnly('user.edit'));
        self::assertFalse(RbacAttributes::isAdminOnly('site.list'));
        self::assertFalse(RbacAttributes::isAdminOnly('content.edit'));
        self::assertTrue(RbacAttributes::isSiteInterior('site.list'));
        self::assertFalse(RbacAttributes::isSiteInterior('host.verify'));
    }

    public function testRoleHasPermissionHelper(): void
    {
        $permission = (new Permission())->setName('host.verify')->setLabel('Verify');
        $role = (new Role())->setName('ROLE_CUSTOM')->setLabel('Custom')->addPermission($permission);

        self::assertTrue($role->hasPermission('host.verify'));
        self::assertFalse($role->hasPermission('user.edit'));
    }

    private function setEntityId(object $entity, int $id): void
    {
        $prop = (new \ReflectionClass($entity))->getProperty('id');
        $prop->setValue($entity, $id);
    }
}
