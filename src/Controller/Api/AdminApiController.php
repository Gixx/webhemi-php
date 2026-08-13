<?php

declare(strict_types=1);

namespace App\Controller\Api;

use App\Api\ApiJson;
use App\Api\AssignHostInput;
use App\Api\CreateHostInput;
use App\Api\CreatePermissionInput;
use App\Api\CreateRoleInput;
use App\Api\CreateSiteInput;
use App\Api\CreateUserInput;
use App\Api\HostAlreadyAssignedException;
use App\Api\HostAdminSurfaceNotAllowedException;
use App\Api\HostApiMapper;
use App\Api\HostAssigner;
use App\Api\HostCreator;
use App\Api\HostDeleter;
use App\Api\HostHostTakenException;
use App\Api\HostNotPendingException;
use App\Api\HostNotVerifiedForAssignException;
use App\Api\HostProtectedException;
use App\Api\HostSiteNotFoundException;
use App\Api\HostUnassigner;
use App\Api\HostUpdater;
use App\Api\HostVerificationFailedException;
use App\Api\HostVerifier;
use App\Api\PermissionApiMapper;
use App\Api\PermissionCreator;
use App\Api\PermissionDeleter;
use App\Api\PermissionHasRolesException;
use App\Api\PermissionNameTakenException;
use App\Api\PermissionUpdater;
use App\Api\RoleApiMapper;
use App\Api\RoleCreator;
use App\Api\RoleDeleter;
use App\Api\RoleHasUsersException;
use App\Api\RoleNameTakenException;
use App\Api\RolePermissionNotFoundException;
use App\Api\RoleProtectedException;
use App\Api\RoleUpdater;
use App\Api\SettingsApiMapper;
use App\Api\SiteApiMapper;
use App\Api\SiteCreator;
use App\Api\SiteDeleter;
use App\Api\SiteHasHostsException;
use App\Api\SiteProtectedException;
use App\Api\SiteSlugTakenException;
use App\Api\SiteUpdater;
use App\Api\UpdateHostInput;
use App\Api\UpdatePermissionInput;
use App\Api\UpdateRoleInput;
use App\Api\UpdateSettingsInput;
use App\Api\UpdateSiteInput;
use App\Api\UpdateUserInput;
use App\Api\UserApiMapper;
use App\Api\UserCreator;
use App\Api\UserDeleter;
use App\Api\UserEmailTakenException;
use App\Api\UserInvalidRoleException;
use App\Api\UserLastAdminException;
use App\Api\UserPasswordMismatchException;
use App\Api\UserPasswordSetter;
use App\Api\UserRoleNotFoundException;
use App\Api\UserSelfDeleteException;
use App\Api\UserSiteNotFoundException;
use App\Api\UserUpdater;
use App\Api\SetUserPasswordInput;
use App\Api\UserAccess;
use App\Config\AdminAccessMode;
use App\Config\SymfonyDebugToolbarSupport;
use App\Config\WebhemiConfigLoader;
use App\Entity\Permission;
use App\Entity\Role;
use App\Entity\Site;
use App\Entity\SiteHost;
use App\Entity\User;
use App\Repository\PermissionRepository;
use App\Repository\RoleRepository;
use App\Repository\SiteHostRepository;
use App\Repository\SiteRepository;
use App\Repository\UserRepository;
use App\Routing\AdminEntryResolverInterface;
use App\Routing\CanonicalAdminEntry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Http\Attribute\IsCsrfTokenValid;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/admin/api')]
final class AdminApiController extends AbstractController
{
    #[Route('/sites', name: 'api_admin_sites', methods: ['GET'])]
    #[IsGranted('site.list')]
    public function sites(SiteRepository $sites): JsonResponse
    {
        $data = array_map(
            static fn (Site $site): array => SiteApiMapper::toArray($site),
            $sites->findBy([], ['name' => 'ASC']),
        );

        return ApiJson::data($data);
    }

    #[Route('/sites', name: 'api_admin_sites_create', methods: ['POST'])]
    #[IsGranted('site.edit')]
    #[IsCsrfTokenValid('admin_api', tokenKey: 'X-CSRF-TOKEN', tokenSource: IsCsrfTokenValid::SOURCE_HEADER)]
    public function createSite(Request $request, SiteCreator $creator): JsonResponse
    {
        try {
            $payload = json_decode($request->getContent(), true, 512, JSON_THROW_ON_ERROR);
        } catch (\JsonException) {
            return ApiJson::error('invalid_json', 'Request body must be valid JSON.', 400);
        }

        $input = CreateSiteInput::fromPayload($payload);
        if (!$input->isValid()) {
            return ApiJson::error(
                'validation_failed',
                'Site could not be created.',
                422,
                $input->fieldErrors,
            );
        }

        try {
            $site = $creator->create($input);
        } catch (SiteSlugTakenException) {
            return ApiJson::error(
                'slug_taken',
                'A site with this slug already exists.',
                409,
                ['slug' => 'Slug is already taken.'],
            );
        }

        return ApiJson::data(SiteApiMapper::toArray($site), 201);
    }

    #[Route('/sites/{id}', name: 'api_admin_sites_show', requirements: ['id' => '\d+'], methods: ['GET'])]
    #[IsGranted('site.list')]
    public function showSite(#[MapEntity(id: 'id')] Site $site): JsonResponse
    {
        return ApiJson::data(SiteApiMapper::toArray($site));
    }

    #[Route('/sites/{id}', name: 'api_admin_sites_update', requirements: ['id' => '\d+'], methods: ['PATCH'])]
    #[IsGranted('site.edit')]
    #[IsCsrfTokenValid('admin_api', tokenKey: 'X-CSRF-TOKEN', tokenSource: IsCsrfTokenValid::SOURCE_HEADER)]
    public function updateSite(
        #[MapEntity(id: 'id')] Site $site,
        Request $request,
        SiteUpdater $updater,
    ): JsonResponse {
        try {
            $payload = json_decode($request->getContent(), true, 512, JSON_THROW_ON_ERROR);
        } catch (\JsonException) {
            return ApiJson::error('invalid_json', 'Request body must be valid JSON.', 400);
        }

        $input = UpdateSiteInput::fromPayload($payload);
        if (!$input->isValid()) {
            return ApiJson::error(
                'validation_failed',
                'Site could not be updated.',
                422,
                $input->fieldErrors,
            );
        }

        try {
            $updated = $updater->update($site, $input);
        } catch (SiteSlugTakenException) {
            return ApiJson::error(
                'slug_taken',
                'A site with this slug already exists.',
                409,
                ['slug' => 'Slug is already taken.'],
            );
        } catch (SiteProtectedException $e) {
            return ApiJson::error(
                'site_protected',
                $e->getMessage(),
                409,
                ['slug' => 'Protected system site.'],
            );
        }

        return ApiJson::data(SiteApiMapper::toArray($updated));
    }

    #[Route('/sites/{id}', name: 'api_admin_sites_delete', requirements: ['id' => '\d+'], methods: ['DELETE'])]
    #[IsGranted('site.edit')]
    #[IsCsrfTokenValid('admin_api', tokenKey: 'X-CSRF-TOKEN', tokenSource: IsCsrfTokenValid::SOURCE_HEADER)]
    public function deleteSite(
        #[MapEntity(id: 'id')] Site $site,
        SiteDeleter $deleter,
    ): Response {
        try {
            $deleter->delete($site);
        } catch (SiteProtectedException $e) {
            return ApiJson::error(
                'site_protected',
                $e->getMessage(),
                409,
            );
        } catch (SiteHasHostsException) {
            return ApiJson::error(
                'hosts_assigned',
                'Unassign or delete hosts before deleting this site.',
                409,
            );
        }

        return new Response(null, Response::HTTP_NO_CONTENT);
    }

    #[Route('/permissions', name: 'api_admin_permissions', methods: ['GET'])]
    #[IsGranted('permission.list')]
    public function permissions(PermissionRepository $permissions): JsonResponse
    {
        $data = array_map(
            static fn (Permission $permission): array => PermissionApiMapper::toArray($permission),
            $permissions->findBy([], ['name' => 'ASC']),
        );

        return ApiJson::data($data);
    }

    #[Route('/permissions', name: 'api_admin_permissions_create', methods: ['POST'])]
    #[IsGranted('permission.edit')]
    #[IsCsrfTokenValid('admin_api', tokenKey: 'X-CSRF-TOKEN', tokenSource: IsCsrfTokenValid::SOURCE_HEADER)]
    public function createPermission(Request $request, PermissionCreator $creator): JsonResponse
    {
        try {
            $payload = json_decode($request->getContent(), true, 512, JSON_THROW_ON_ERROR);
        } catch (\JsonException) {
            return ApiJson::error('invalid_json', 'Request body must be valid JSON.', 400);
        }

        $input = CreatePermissionInput::fromPayload($payload);
        if (!$input->isValid()) {
            return ApiJson::error(
                'validation_failed',
                'Permission could not be created.',
                422,
                $input->fieldErrors,
            );
        }

        try {
            $permission = $creator->create($input);
        } catch (PermissionNameTakenException) {
            return ApiJson::error(
                'name_taken',
                'A permission with this name already exists.',
                409,
                ['name' => 'Name is already taken.'],
            );
        }

        return ApiJson::data(PermissionApiMapper::toArray($permission), 201);
    }

    #[Route('/permissions/{id}', name: 'api_admin_permissions_show', requirements: ['id' => '\d+'], methods: ['GET'])]
    #[IsGranted('permission.list')]
    public function showPermission(#[MapEntity(id: 'id')] Permission $permission): JsonResponse
    {
        return ApiJson::data(PermissionApiMapper::toArray($permission));
    }

    #[Route(
        '/permissions/{id}',
        name: 'api_admin_permissions_update',
        requirements: ['id' => '\d+'],
        methods: ['PATCH'],
    )]
    #[IsGranted('permission.edit')]
    #[IsCsrfTokenValid('admin_api', tokenKey: 'X-CSRF-TOKEN', tokenSource: IsCsrfTokenValid::SOURCE_HEADER)]
    public function updatePermission(
        #[MapEntity(id: 'id')] Permission $permission,
        Request $request,
        PermissionUpdater $updater,
    ): JsonResponse {
        try {
            $payload = json_decode($request->getContent(), true, 512, JSON_THROW_ON_ERROR);
        } catch (\JsonException) {
            return ApiJson::error('invalid_json', 'Request body must be valid JSON.', 400);
        }

        $input = UpdatePermissionInput::fromPayload($payload);
        if (!$input->isValid()) {
            return ApiJson::error(
                'validation_failed',
                'Permission could not be updated.',
                422,
                $input->fieldErrors,
            );
        }

        try {
            $updated = $updater->update($permission, $input);
        } catch (PermissionNameTakenException) {
            return ApiJson::error(
                'name_taken',
                'A permission with this name already exists.',
                409,
                ['name' => 'Name is already taken.'],
            );
        }

        return ApiJson::data(PermissionApiMapper::toArray($updated));
    }

    #[Route(
        '/permissions/{id}',
        name: 'api_admin_permissions_delete',
        requirements: ['id' => '\d+'],
        methods: ['DELETE'],
    )]
    #[IsGranted('permission.edit')]
    #[IsCsrfTokenValid('admin_api', tokenKey: 'X-CSRF-TOKEN', tokenSource: IsCsrfTokenValid::SOURCE_HEADER)]
    public function deletePermission(
        #[MapEntity(id: 'id')] Permission $permission,
        PermissionDeleter $deleter,
    ): Response {
        try {
            $deleter->delete($permission);
        } catch (PermissionHasRolesException) {
            return ApiJson::error(
                'roles_assigned',
                'Detach this permission from all roles before deleting it.',
                409,
            );
        }

        return new Response(null, Response::HTTP_NO_CONTENT);
    }

    #[Route('/roles', name: 'api_admin_roles', methods: ['GET'])]
    #[IsGranted('role.list')]
    public function roles(RoleRepository $roles): JsonResponse
    {
        $data = array_map(
            static fn (Role $role): array => RoleApiMapper::toArray($role),
            $roles->findBy([], ['name' => 'ASC']),
        );

        return ApiJson::data($data);
    }

    #[Route('/roles', name: 'api_admin_roles_create', methods: ['POST'])]
    #[IsGranted('role.edit')]
    #[IsCsrfTokenValid('admin_api', tokenKey: 'X-CSRF-TOKEN', tokenSource: IsCsrfTokenValid::SOURCE_HEADER)]
    public function createRole(Request $request, RoleCreator $creator): JsonResponse
    {
        try {
            $payload = json_decode($request->getContent(), true, 512, JSON_THROW_ON_ERROR);
        } catch (\JsonException) {
            return ApiJson::error('invalid_json', 'Request body must be valid JSON.', 400);
        }

        $input = CreateRoleInput::fromPayload($payload);
        if (!$input->isValid()) {
            return ApiJson::error(
                'validation_failed',
                'Role could not be created.',
                422,
                $input->fieldErrors,
            );
        }

        try {
            $role = $creator->create($input);
        } catch (RoleNameTakenException) {
            return ApiJson::error(
                'name_taken',
                'A role with this name already exists.',
                409,
                ['name' => 'Name is already taken.'],
            );
        } catch (RolePermissionNotFoundException) {
            return ApiJson::error(
                'permission_not_found',
                'One or more permissions were not found.',
                422,
                ['permissionIds' => 'Unknown permission id.'],
            );
        }

        return ApiJson::data(RoleApiMapper::toArray($role), 201);
    }

    #[Route('/roles/{id}', name: 'api_admin_roles_show', requirements: ['id' => '\d+'], methods: ['GET'])]
    #[IsGranted('role.list')]
    public function showRole(#[MapEntity(id: 'id')] Role $role): JsonResponse
    {
        return ApiJson::data(RoleApiMapper::toArray($role));
    }

    #[Route(
        '/roles/{id}',
        name: 'api_admin_roles_update',
        requirements: ['id' => '\d+'],
        methods: ['PATCH'],
    )]
    #[IsGranted('role.edit')]
    #[IsCsrfTokenValid('admin_api', tokenKey: 'X-CSRF-TOKEN', tokenSource: IsCsrfTokenValid::SOURCE_HEADER)]
    public function updateRole(
        #[MapEntity(id: 'id')] Role $role,
        Request $request,
        RoleUpdater $updater,
    ): JsonResponse {
        try {
            $payload = json_decode($request->getContent(), true, 512, JSON_THROW_ON_ERROR);
        } catch (\JsonException) {
            return ApiJson::error('invalid_json', 'Request body must be valid JSON.', 400);
        }

        $input = UpdateRoleInput::fromPayload($payload);
        if (!$input->isValid()) {
            return ApiJson::error(
                'validation_failed',
                'Role could not be updated.',
                422,
                $input->fieldErrors,
            );
        }

        try {
            $updated = $updater->update($role, $input);
        } catch (RoleProtectedException $e) {
            return ApiJson::error(
                'role_protected',
                $e->getMessage(),
                409,
            );
        } catch (RoleNameTakenException) {
            return ApiJson::error(
                'name_taken',
                'A role with this name already exists.',
                409,
                ['name' => 'Name is already taken.'],
            );
        } catch (RolePermissionNotFoundException) {
            return ApiJson::error(
                'permission_not_found',
                'One or more permissions were not found.',
                422,
                ['permissionIds' => 'Unknown permission id.'],
            );
        }

        return ApiJson::data(RoleApiMapper::toArray($updated));
    }

    #[Route(
        '/roles/{id}',
        name: 'api_admin_roles_delete',
        requirements: ['id' => '\d+'],
        methods: ['DELETE'],
    )]
    #[IsGranted('role.edit')]
    #[IsCsrfTokenValid('admin_api', tokenKey: 'X-CSRF-TOKEN', tokenSource: IsCsrfTokenValid::SOURCE_HEADER)]
    public function deleteRole(
        #[MapEntity(id: 'id')] Role $role,
        RoleDeleter $deleter,
    ): Response {
        try {
            $deleter->delete($role);
        } catch (RoleProtectedException $e) {
            return ApiJson::error(
                'role_protected',
                $e->getMessage(),
                409,
            );
        } catch (RoleHasUsersException) {
            return ApiJson::error(
                'users_assigned',
                'Detach this role from all users before deleting it.',
                409,
            );
        }

        return new Response(null, Response::HTTP_NO_CONTENT);
    }

    #[Route('/users', name: 'api_admin_users', methods: ['GET'])]
    #[IsGranted('ROLE_USER')]
    public function users(UserRepository $users, UserAccess $access): JsonResponse
    {
        $actor = $this->getUser();
        if (!$actor instanceof User) {
            throw $this->createAccessDeniedException();
        }

        $all = $users->findBy([], ['email' => 'ASC']);
        if ($access->canListUsers($actor)) {
            $filtered = $all;
        } else {
            $actorId = $actor->getId();
            $filtered = array_values(array_filter(
                $all,
                static fn (User $user): bool => $user->getId() === $actorId,
            ));
        }

        usort(
            $filtered,
            static function (User $a, User $b) use ($actor): int {
                $actorId = $actor->getId();
                if ($a->getId() === $actorId) {
                    return -1;
                }
                if ($b->getId() === $actorId) {
                    return 1;
                }

                return strcasecmp($a->getEmail(), $b->getEmail());
            },
        );

        $data = array_map(
            static fn (User $user): array => UserApiMapper::toArray($user),
            $filtered,
        );

        return ApiJson::data($data);
    }

    #[Route('/users', name: 'api_admin_users_create', methods: ['POST'])]
    #[IsGranted('user.create')]
    #[IsCsrfTokenValid('admin_api', tokenKey: 'X-CSRF-TOKEN', tokenSource: IsCsrfTokenValid::SOURCE_HEADER)]
    public function createUser(Request $request, UserCreator $creator): JsonResponse
    {
        try {
            $payload = json_decode($request->getContent(), true, 512, JSON_THROW_ON_ERROR);
        } catch (\JsonException) {
            return ApiJson::error('invalid_json', 'Request body must be valid JSON.', 400);
        }

        $input = CreateUserInput::fromPayload($payload);
        if (!$input->isValid()) {
            return ApiJson::error(
                'validation_failed',
                'User could not be created.',
                422,
                $input->fieldErrors,
            );
        }

        try {
            $user = $creator->create($input);
        } catch (UserEmailTakenException) {
            return ApiJson::error(
                'email_taken',
                'A user with this email already exists.',
                409,
                ['email' => 'Email is already taken.'],
            );
        } catch (UserInvalidRoleException $e) {
            return ApiJson::error('invalid_role', $e->getMessage(), 409);
        } catch (UserRoleNotFoundException) {
            return ApiJson::error('invalid_role', 'One or more roles were not found.', 409);
        } catch (UserSiteNotFoundException) {
            return ApiJson::error('site_not_found', 'One or more sites were not found.', 409);
        } catch (UserLastAdminException $e) {
            return ApiJson::error('last_admin', $e->getMessage(), 409);
        }

        return ApiJson::data(UserApiMapper::toArray($user), 201);
    }

    #[Route('/users/{id}', name: 'api_admin_users_show', requirements: ['id' => '\d+'], methods: ['GET'])]
    #[IsGranted('ROLE_USER')]
    public function showUser(
        #[MapEntity(id: 'id')] User $user,
        UserAccess $access,
    ): JsonResponse {
        $actor = $this->getUser();
        if (!$actor instanceof User || !$access->canViewUser($actor, $user)) {
            throw $this->createAccessDeniedException();
        }

        return ApiJson::data(UserApiMapper::toArray($user));
    }

    #[Route(
        '/users/{id}',
        name: 'api_admin_users_update',
        requirements: ['id' => '\d+'],
        methods: ['PATCH'],
    )]
    #[IsGranted('user.edit')]
    #[IsCsrfTokenValid('admin_api', tokenKey: 'X-CSRF-TOKEN', tokenSource: IsCsrfTokenValid::SOURCE_HEADER)]
    public function updateUser(
        #[MapEntity(id: 'id')] User $user,
        Request $request,
        UserUpdater $updater,
    ): JsonResponse {
        try {
            $payload = json_decode($request->getContent(), true, 512, JSON_THROW_ON_ERROR);
        } catch (\JsonException) {
            return ApiJson::error('invalid_json', 'Request body must be valid JSON.', 400);
        }

        $input = UpdateUserInput::fromPayload($payload);
        if (!$input->isValid()) {
            return ApiJson::error(
                'validation_failed',
                'User could not be updated.',
                422,
                $input->fieldErrors,
            );
        }

        try {
            $updated = $updater->update($user, $input);
        } catch (UserEmailTakenException) {
            return ApiJson::error(
                'email_taken',
                'A user with this email already exists.',
                409,
                ['email' => 'Email is already taken.'],
            );
        } catch (UserInvalidRoleException $e) {
            return ApiJson::error('invalid_role', $e->getMessage(), 409);
        } catch (UserRoleNotFoundException) {
            return ApiJson::error('invalid_role', 'One or more roles were not found.', 409);
        } catch (UserSiteNotFoundException) {
            return ApiJson::error('site_not_found', 'One or more sites were not found.', 409);
        } catch (UserLastAdminException $e) {
            return ApiJson::error('last_admin', $e->getMessage(), 409);
        }

        return ApiJson::data(UserApiMapper::toArray($updated));
    }

    #[Route(
        '/users/{id}',
        name: 'api_admin_users_delete',
        requirements: ['id' => '\d+'],
        methods: ['DELETE'],
    )]
    #[IsGranted('user.delete')]
    #[IsCsrfTokenValid('admin_api', tokenKey: 'X-CSRF-TOKEN', tokenSource: IsCsrfTokenValid::SOURCE_HEADER)]
    public function deleteUser(
        #[MapEntity(id: 'id')] User $user,
        UserDeleter $deleter,
    ): Response {
        $actor = $this->getUser();
        $actorEntity = $actor instanceof User ? $actor : null;

        try {
            $deleter->delete($user, $actorEntity);
        } catch (UserSelfDeleteException $e) {
            return ApiJson::error('self_delete', $e->getMessage(), 409);
        } catch (UserLastAdminException $e) {
            return ApiJson::error('last_admin', $e->getMessage(), 409);
        }

        return new Response(null, Response::HTTP_NO_CONTENT);
    }

    #[Route(
        '/users/{id}/password',
        name: 'api_admin_users_password',
        requirements: ['id' => '\d+'],
        methods: ['POST'],
    )]
    #[IsGranted('ROLE_USER')]
    #[IsCsrfTokenValid('admin_api', tokenKey: 'X-CSRF-TOKEN', tokenSource: IsCsrfTokenValid::SOURCE_HEADER)]
    public function setUserPassword(
        #[MapEntity(id: 'id')] User $user,
        Request $request,
        UserPasswordSetter $passwordSetter,
        UserAccess $access,
    ): JsonResponse {
        $actor = $this->getUser();
        if (!$actor instanceof User || !$access->canSetPassword($actor, $user)) {
            throw $this->createAccessDeniedException();
        }

        $isSelf = $actor->getId() !== null && $actor->getId() === $user->getId();

        try {
            $payload = json_decode($request->getContent(), true, 512, JSON_THROW_ON_ERROR);
        } catch (\JsonException) {
            return ApiJson::error('invalid_json', 'Request body must be valid JSON.', 400);
        }

        $input = SetUserPasswordInput::fromPayload($payload, requireCurrentPassword: $isSelf);
        if (!$input->isValid()) {
            return ApiJson::error(
                'validation_failed',
                'Password could not be set.',
                422,
                $input->fieldErrors,
            );
        }

        try {
            $passwordSetter->setPassword($user, $input);
        } catch (UserPasswordMismatchException $e) {
            return ApiJson::error(
                'password_mismatch',
                $e->getMessage(),
                409,
                ['currentPassword' => 'Current password is incorrect.'],
            );
        }

        return ApiJson::data(['ok' => true]);
    }

    #[Route('/hosts', name: 'api_admin_hosts', methods: ['GET'])]
    #[IsGranted('host.list')]
    public function hosts(SiteHostRepository $hosts): JsonResponse
    {
        $data = array_map(
            static fn (SiteHost $host): array => HostApiMapper::toArray($host),
            $hosts->findBy([], ['host' => 'ASC']),
        );

        return ApiJson::data($data);
    }

    #[Route('/hosts', name: 'api_admin_hosts_create', methods: ['POST'])]
    #[IsGranted('host.edit')]
    #[IsCsrfTokenValid('admin_api', tokenKey: 'X-CSRF-TOKEN', tokenSource: IsCsrfTokenValid::SOURCE_HEADER)]
    public function createHost(Request $request, HostCreator $creator): JsonResponse
    {
        try {
            $payload = json_decode($request->getContent(), true, 512, JSON_THROW_ON_ERROR);
        } catch (\JsonException) {
            return ApiJson::error('invalid_json', 'Request body must be valid JSON.', 400);
        }

        $input = CreateHostInput::fromPayload($payload);
        if (!$input->isValid()) {
            return ApiJson::error(
                'validation_failed',
                'Host could not be created.',
                422,
                $input->fieldErrors,
            );
        }

        try {
            $host = $creator->create($input);
        } catch (HostHostTakenException) {
            return ApiJson::error(
                'host_taken',
                'A host with this hostname already exists.',
                409,
                ['host' => 'Hostname is already taken.'],
            );
        }

        return ApiJson::data(HostApiMapper::toArray($host), 201);
    }

    #[Route('/hosts/{id}', name: 'api_admin_hosts_show', requirements: ['id' => '\d+'], methods: ['GET'])]
    #[IsGranted('host.list')]
    public function showHost(#[MapEntity(id: 'id')] SiteHost $host): JsonResponse
    {
        return ApiJson::data(HostApiMapper::toArray($host));
    }

    #[Route('/hosts/{id}', name: 'api_admin_hosts_update', requirements: ['id' => '\d+'], methods: ['PATCH'])]
    #[IsGranted('host.edit')]
    #[IsCsrfTokenValid('admin_api', tokenKey: 'X-CSRF-TOKEN', tokenSource: IsCsrfTokenValid::SOURCE_HEADER)]
    public function updateHost(
        #[MapEntity(id: 'id')] SiteHost $host,
        Request $request,
        HostUpdater $updater,
        AdminEntryResolverInterface $entryResolver,
        TokenStorageInterface $tokenStorage,
    ): JsonResponse {
        try {
            $payload = json_decode($request->getContent(), true, 512, JSON_THROW_ON_ERROR);
        } catch (\JsonException) {
            return ApiJson::error('invalid_json', 'Request body must be valid JSON.', 400);
        }

        $input = UpdateHostInput::fromPayload($payload);
        if (!$input->isValid()) {
            return ApiJson::error(
                'validation_failed',
                'Host could not be updated.',
                422,
                $input->fieldErrors,
            );
        }

        try {
            $result = $updater->update($host, $input);
        } catch (HostSiteNotFoundException) {
            return ApiJson::error(
                'site_not_found',
                'The selected site does not exist.',
                404,
                ['siteId' => 'Site not found.'],
            );
        } catch (HostHostTakenException) {
            return ApiJson::error(
                'host_taken',
                'A host with this hostname already exists.',
                409,
                ['host' => 'Hostname is already taken.'],
            );
        } catch (HostNotVerifiedForAssignException) {
            return ApiJson::error(
                'not_assignable',
                'Only verified, unassigned hosts can be assigned to a site.',
                422,
                ['siteId' => 'Host must be verified and unassigned.'],
            );
        } catch (HostAlreadyAssignedException) {
            return ApiJson::error(
                'already_assigned',
                'Host is already assigned to a site.',
                422,
                ['siteId' => 'Host is already assigned.'],
            );
        } catch (HostAdminSurfaceNotAllowedException) {
            return ApiJson::error(
                'admin_surface_main_only',
                'Admin surface is only allowed on the Main site.',
                422,
                ['surface' => 'Admin surface requires the Main site (slug "main").'],
            );
        } catch (HostProtectedException $e) {
            return ApiJson::error(
                'host_protected',
                $e->getMessage(),
                409,
            );
        }

        return ApiJson::data($this->hostPayloadWithSessionEnd(
            $request,
            HostApiMapper::toArray($result->host),
            $result->accessModeReset,
            $entryResolver,
            $tokenStorage,
        ));
    }

    #[Route('/hosts/{id}', name: 'api_admin_hosts_delete', requirements: ['id' => '\d+'], methods: ['DELETE'])]
    #[IsGranted('host.edit')]
    #[IsCsrfTokenValid('admin_api', tokenKey: 'X-CSRF-TOKEN', tokenSource: IsCsrfTokenValid::SOURCE_HEADER)]
    public function deleteHost(
        Request $request,
        #[MapEntity(id: 'id')] SiteHost $host,
        HostDeleter $deleter,
        AdminEntryResolverInterface $entryResolver,
        TokenStorageInterface $tokenStorage,
    ): JsonResponse {
        try {
            $accessModeReset = $deleter->delete($host);
        } catch (HostProtectedException $e) {
            return ApiJson::error(
                'host_protected',
                $e->getMessage(),
                409,
            );
        }

        return ApiJson::data($this->hostPayloadWithSessionEnd(
            $request,
            ['deleted' => true],
            $accessModeReset,
            $entryResolver,
            $tokenStorage,
        ));
    }

    #[Route('/hosts/{id}/assign', name: 'api_admin_hosts_assign', requirements: ['id' => '\d+'], methods: ['POST'])]
    #[IsGranted('host.edit')]
    #[IsCsrfTokenValid('admin_api', tokenKey: 'X-CSRF-TOKEN', tokenSource: IsCsrfTokenValid::SOURCE_HEADER)]
    public function assignHost(
        #[MapEntity(id: 'id')] SiteHost $host,
        Request $request,
        HostAssigner $assigner,
    ): JsonResponse {
        try {
            $payload = json_decode($request->getContent(), true, 512, JSON_THROW_ON_ERROR);
        } catch (\JsonException) {
            return ApiJson::error('invalid_json', 'Request body must be valid JSON.', 400);
        }

        $input = AssignHostInput::fromPayload($payload);
        if (!$input->isValid()) {
            return ApiJson::error(
                'validation_failed',
                'Host could not be assigned.',
                422,
                $input->fieldErrors,
            );
        }

        try {
            $updated = $assigner->assign($host, (int) $input->siteId);
        } catch (HostSiteNotFoundException) {
            return ApiJson::error(
                'site_not_found',
                'The selected site does not exist.',
                404,
                ['siteId' => 'Site not found.'],
            );
        } catch (HostNotVerifiedForAssignException) {
            return ApiJson::error(
                'not_assignable',
                'Only verified, unassigned hosts can be assigned to a site.',
                422,
            );
        } catch (HostAlreadyAssignedException) {
            return ApiJson::error(
                'already_assigned',
                'Host is already assigned to a site.',
                422,
            );
        } catch (HostAdminSurfaceNotAllowedException) {
            return ApiJson::error(
                'admin_surface_main_only',
                'Admin surface is only allowed on the Main site.',
                422,
                [
                    'siteId' => 'Admin-surface hosts can only be assigned to the Main site.',
                    'surface' => 'Admin surface requires the Main site (slug "main").',
                ],
            );
        }

        return ApiJson::data(HostApiMapper::toArray($updated));
    }

    #[Route('/hosts/{id}/unassign', name: 'api_admin_hosts_unassign', requirements: ['id' => '\d+'], methods: ['POST'])]
    #[IsGranted('host.edit')]
    #[IsCsrfTokenValid('admin_api', tokenKey: 'X-CSRF-TOKEN', tokenSource: IsCsrfTokenValid::SOURCE_HEADER)]
    public function unassignHost(
        Request $request,
        #[MapEntity(id: 'id')] SiteHost $host,
        HostUnassigner $unassigner,
        AdminEntryResolverInterface $entryResolver,
        TokenStorageInterface $tokenStorage,
    ): JsonResponse {
        try {
            $result = $unassigner->unassign($host);
        } catch (HostProtectedException $e) {
            return ApiJson::error(
                'host_protected',
                $e->getMessage(),
                409,
            );
        }

        return ApiJson::data($this->hostPayloadWithSessionEnd(
            $request,
            HostApiMapper::toArray($result->host),
            $result->accessModeReset,
            $entryResolver,
            $tokenStorage,
        ));
    }

    #[Route('/hosts/{id}/verify', name: 'api_admin_hosts_verify', requirements: ['id' => '\d+'], methods: ['POST'])]
    #[IsGranted('host.verify')]
    #[IsCsrfTokenValid('admin_api', tokenKey: 'X-CSRF-TOKEN', tokenSource: IsCsrfTokenValid::SOURCE_HEADER)]
    public function verifyHost(
        #[MapEntity(id: 'id')] SiteHost $host,
        HostVerifier $verifier,
    ): JsonResponse {
        try {
            $updated = $verifier->verify($host);
        } catch (HostNotPendingException) {
            return ApiJson::error(
                'not_pending',
                'Only pending hosts can be verified.',
                422,
            );
        } catch (HostVerificationFailedException) {
            return ApiJson::error(
                'verification_failed',
                'Ownership could not be verified. Ensure the hostname points at this install and try again.',
                422,
            );
        }

        return ApiJson::data(HostApiMapper::toArray($updated));
    }

    #[Route('/settings', name: 'api_admin_settings', methods: ['GET'])]
    #[IsGranted('settings.list')]
    public function settings(
        WebhemiConfigLoader $configLoader,
        AdminEntryResolverInterface $entryResolver,
        SiteHostRepository $hosts,
        KernelInterface $kernel,
    ): JsonResponse {
        $config = $configLoader->get();
        $adminHost = $hosts->findMainAdminHost();

        return ApiJson::data(SettingsApiMapper::toArray(
            $config,
            $entryResolver->resolve()->effectiveMode,
            $adminHost,
            $kernel->getEnvironment(),
        ));
    }

    #[Route('/settings', name: 'api_admin_settings_update', methods: ['PATCH'])]
    #[IsGranted('settings.edit')]
    #[IsCsrfTokenValid('admin_api', tokenKey: 'X-CSRF-TOKEN', tokenSource: IsCsrfTokenValid::SOURCE_HEADER)]
    public function updateSettings(
        Request $request,
        WebhemiConfigLoader $configLoader,
        AdminEntryResolverInterface $entryResolver,
        SiteHostRepository $hosts,
        TokenStorageInterface $tokenStorage,
        KernelInterface $kernel,
    ): JsonResponse {
        try {
            $payload = json_decode($request->getContent(), true, 512, JSON_THROW_ON_ERROR);
        } catch (\JsonException) {
            return ApiJson::error('invalid_json', 'Request body must be valid JSON.', 400);
        }

        $input = UpdateSettingsInput::fromPayload($payload);
        if (!$input->isValid()) {
            return ApiJson::error(
                'validation_failed',
                'Settings could not be updated.',
                422,
                $input->fieldErrors,
            );
        }

        $environment = $kernel->getEnvironment();
        if (
            null !== $input->symfonyDebugToolbar
            && !SymfonyDebugToolbarSupport::isEditable($environment)
        ) {
            return ApiJson::error(
                'toolbar_not_editable',
                'Symfony debug toolbar can only be changed in the dev or stage environment.',
                422,
                ['symfonyDebugToolbar' => 'Not editable in this environment.'],
            );
        }

        $previous = $configLoader->get();
        $previousAccess = $previous->adminAccess;
        $adminHost = $hosts->findMainAdminHost();
        if (
            AdminAccessMode::Domain === $input->adminAccess
            && !$adminHost instanceof SiteHost
        ) {
            return ApiJson::error(
                'domain_unavailable',
                'Domain admin access requires a verified, enabled Main-site admin host.',
                422,
                ['adminAccess' => 'No healthy admin host is available for domain mode.'],
            );
        }

        $config = $previous;
        if ($input->adminAccess instanceof AdminAccessMode) {
            $config = $config->withAdminAccess($input->adminAccess);
        }
        if (null !== $input->symfonyDebugToolbar) {
            $config = $config->withSymfonyDebugToolbar($input->symfonyDebugToolbar);
        }
        $configLoader->save($config);
        $entry = $entryResolver->resolve();
        $adminHost = $hosts->findMainAdminHost();
        $accessChanged = $input->adminAccess instanceof AdminAccessMode
            && $previousAccess !== $input->adminAccess;
        $loginUrl = null;
        $sessionEnded = false;

        if ($accessChanged) {
            $loginUrl = $this->adminLoginUrl($request, $entry);
            $tokenStorage->setToken(null);
            $session = $request->hasSession() ? $request->getSession() : null;
            $session?->invalidate();
            $sessionEnded = true;
        }

        return ApiJson::data(SettingsApiMapper::toArray(
            $configLoader->get(),
            $entry->effectiveMode,
            $adminHost,
            $environment,
            $loginUrl,
            $sessionEnded,
        ));
    }

    #[Route('/me', name: 'api_admin_me', methods: ['GET'])]
    #[IsGranted('ROLE_USER')]
    public function me(UserAccess $access): JsonResponse
    {
        $user = $this->getUser();
        if (!$user instanceof User) {
            return $this->json([
                'user' => null,
                'roles' => [],
            ]);
        }

        return $this->json([
            'user' => $user->getUserIdentifier(),
            'id' => $user->getId(),
            'email' => $user->getEmail(),
            'roles' => $user->getRoles(),
            'capabilities' => $access->capabilities($user),
        ]);
    }

    /**
     * @param array<string, mixed> $data
     *
     * @return array<string, mixed>
     */
    private function hostPayloadWithSessionEnd(
        Request $request,
        array $data,
        bool $accessModeReset,
        AdminEntryResolverInterface $entryResolver,
        TokenStorageInterface $tokenStorage,
    ): array {
        if (!$accessModeReset) {
            return $data;
        }

        $entry = $entryResolver->resolve();
        $loginUrl = $this->adminLoginUrl($request, $entry);
        $tokenStorage->setToken(null);
        $session = $request->hasSession() ? $request->getSession() : null;
        $session?->invalidate();

        if (null !== $loginUrl) {
            $data['loginUrl'] = $loginUrl;
        }
        $data['sessionEnded'] = true;
        $data['accessModeReset'] = true;

        return $data;
    }

    private function adminLoginUrl(Request $request, CanonicalAdminEntry $entry): ?string
    {
        $hostname = $entry->canonicalHostname();
        if (null === $hostname || $hostname === '') {
            return null;
        }

        $path = AdminAccessMode::Domain === $entry->effectiveMode
            ? '/login'
            : rtrim($entry->adminPath, '/') . '/login';

        $scheme = $request->getScheme();
        $port = $request->getPort();
        $isDefaultPort = ('http' === $scheme && 80 === $port)
            || ('https' === $scheme && 443 === $port);
        $authority = $hostname . ($isDefaultPort ? '' : ':' . $port);

        return $scheme . '://' . $authority . $path;
    }
}
