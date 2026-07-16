<?php

declare(strict_types=1);

namespace App\Command;

use App\Entity\Permission;
use App\Entity\Role;
use App\Entity\Site;
use App\Entity\SiteHost;
use App\Entity\SurfaceType;
use App\Entity\User;
use App\Repository\PermissionRepository;
use App\Repository\RoleRepository;
use App\Repository\SiteHostRepository;
use App\Repository\SiteRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

#[AsCommand(name: 'app:seed', description: 'Seed RBAC roles, default site/hosts, and optional admin user')]
final class SeedCommand extends Command
{
    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly UserRepository $users,
        private readonly RoleRepository $roles,
        private readonly PermissionRepository $permissions,
        private readonly SiteRepository $sites,
        private readonly SiteHostRepository $hosts,
        private readonly UserPasswordHasherInterface $passwordHasher,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addOption('admin-email', null, InputOption::VALUE_REQUIRED, 'Admin email', 'admin@webhemi.local')
            ->addOption('admin-password', null, InputOption::VALUE_REQUIRED, 'Admin password', 'ChangeMe!')
            ->addOption('admin-host', null, InputOption::VALUE_REQUIRED, 'Admin hostname', 'admin.webhemi.local')
            ->addOption('site-host', null, InputOption::VALUE_REQUIRED, 'Site hostname', 'www.webhemi.local');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $permissionDefs = [
            'site.list' => 'List sites',
            'site.edit' => 'Edit sites',
            'host.list' => 'List hosts',
            'host.edit' => 'Edit hosts',
            'host.verify' => 'Verify host ownership',
            'user.list' => 'List users',
            'user.edit' => 'Edit users',
            'role.list' => 'List roles',
            'role.edit' => 'Edit roles',
        ];

        $permissionMap = [];
        foreach ($permissionDefs as $name => $label) {
            $permission = $this->permissions->findOneBy(['name' => $name]);
            if (!$permission instanceof Permission) {
                $permission = (new Permission())->setName($name)->setLabel($label);
            }
            $this->em->persist($permission);
            $permissionMap[$name] = $permission;
        }

        $adminRole = $this->roles->findOneBy(['name' => 'ROLE_ADMIN']);
        if (!$adminRole instanceof Role) {
            $adminRole = (new Role())->setName('ROLE_ADMIN')->setLabel('Administrator');
        }
        $this->em->persist($adminRole);

        $editorRole = $this->roles->findOneBy(['name' => 'ROLE_EDITOR']);
        if (!$editorRole instanceof Role) {
            $editorRole = (new Role())->setName('ROLE_EDITOR')->setLabel('Editor');
        }
        foreach (['site.list', 'site.edit', 'host.list', 'host.edit', 'host.verify'] as $name) {
            $editorRole->addPermission($permissionMap[$name]);
        }
        $this->em->persist($editorRole);

        $site = $this->sites->findOneBy(['slug' => 'main']);
        if (!$site instanceof Site) {
            $site = (new Site())->setSlug('main')->setName('Main site')->setIsEnabled(true);
        }
        $this->em->persist($site);

        $adminHostName = (string) $input->getOption('admin-host');
        $siteHostName = (string) $input->getOption('site-host');

        $adminHost = $this->hosts->findOneBy(['host' => $adminHostName]) ?? (new SiteHost())
            ->setHost($adminHostName)
            ->setSurface(SurfaceType::Admin)
            ->setStatus('active')
            ->setIsActive(true);
        $adminHost->setSite($site);
        $this->em->persist($adminHost);

        $publicHost = $this->hosts->findOneBy(['host' => $siteHostName]) ?? (new SiteHost())
            ->setHost($siteHostName)
            ->setSurface(SurfaceType::Site)
            ->setStatus('active')
            ->setIsActive(true);
        $publicHost->setSite($site);
        $this->em->persist($publicHost);

        $email = (string) $input->getOption('admin-email');
        $user = $this->users->findOneBy(['email' => $email]) ?? (new User())->setEmail($email);
        $user->setPassword($this->passwordHasher->hashPassword($user, (string) $input->getOption('admin-password')));
        $user->addRole($adminRole);
        $this->em->persist($user);

        $this->em->flush();

        $io->success(sprintf(
            'Seeded admin %s / hosts %s + %s',
            $email,
            $adminHostName,
            $siteHostName,
        ));

        return Command::SUCCESS;
    }
}
