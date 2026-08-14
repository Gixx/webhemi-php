<?php

declare(strict_types=1);

namespace App\Command;

use App\Config\AdminAccessMode;
use App\Config\WebhemiConfig;
use App\Config\WebhemiConfigLoader;
use App\Entity\Role;
use App\Entity\Site;
use App\Entity\SiteHost;
use App\Entity\SurfaceType;
use App\Entity\User;
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

#[AsCommand(name: 'app:seed', description: 'Seed protected RBAC roles, default site/hosts, and optional admin user')]
final class SeedCommand extends Command
{
    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly UserRepository $users,
        private readonly RoleRepository $roles,
        private readonly SiteRepository $sites,
        private readonly SiteHostRepository $hosts,
        private readonly UserPasswordHasherInterface $passwordHasher,
        private readonly WebhemiConfigLoader $webhemiConfig,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addOption('admin-email', null, InputOption::VALUE_REQUIRED, 'Admin email', 'admin@webhemi.local')
            ->addOption('admin-password', null, InputOption::VALUE_REQUIRED, 'Admin password', 'admin')
            ->addOption('admin-host', null, InputOption::VALUE_REQUIRED, 'Admin hostname', 'admin.webhemi.local')
            ->addOption('site-host', null, InputOption::VALUE_REQUIRED, 'Site hostname', 'www.webhemi.local');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        // Permission catalog stays empty at seed — operators add rows for testing.
        $adminRole = $this->ensureSystemRole(Role::ADMIN, 'Administrator');
        $this->ensureSystemRole(Role::SITE_ADMIN, 'Site Administrator');

        $site = $this->sites->findOneBy(['slug' => 'main']);
        if (!$site instanceof Site) {
            $site = (new Site())
                ->setSlug('main')
                ->setName('Main site')
                ->setThemeId(Site::DEFAULT_THEME_ID)
                ->setIsEnabled(true);
        }
        $site->setIsProtected(true);
        $this->em->persist($site);

        $adminHostName = (string) $input->getOption('admin-host');
        $siteHostName = (string) $input->getOption('site-host');

        // Dev fixtures: skip ownership probe; land verified + enabled + assigned
        // see docs/host-ownership-verification-flow.md
        $adminHost = $this->hosts->findOneBy(['host' => $adminHostName]) ?? (new SiteHost())->setHost($adminHostName);
        $adminHost
            ->setSurface(SurfaceType::Admin)
            ->setVerification('verified')
            ->setIsEnabled(true)
            ->setIsProtected(false)
            ->setSite($site);
        $this->em->persist($adminHost);

        $publicHost = $this->hosts->findOneBy(['host' => $siteHostName]) ?? (new SiteHost())->setHost($siteHostName);
        $publicHost
            ->setSurface(SurfaceType::Site)
            ->setVerification('verified')
            ->setIsEnabled(true)
            ->setIsProtected(true)
            ->setSite($site);
        $this->em->persist($publicHost);

        $email = (string) $input->getOption('admin-email');
        $user = $this->users->findOneBy(['email' => $email]) ?? (new User())->setEmail($email);
        $user->setPassword($this->passwordHasher->hashPassword($user, (string) $input->getOption('admin-password')));
        $user->addRole($adminRole);
        $this->em->persist($user);

        $this->em->flush();

        // Local seed creates an admin-surface host → domain access matches routing intent.
        $defaults = WebhemiConfig::defaults();
        $this->webhemiConfig->ensureFileExists(new WebhemiConfig(
            adminAccess: AdminAccessMode::Domain,
            adminPath: $defaults->adminPath,
            adminApiPath: $defaults->adminApiPath,
            publicApiPath: $defaults->publicApiPath,
            loginPath: $defaults->loginPath,
            registerPath: $defaults->registerPath,
        ));

        $io->success(sprintf(
            'Seeded admin %s (ROLE_ADMIN + ROLE_SITE_ADMIN roles, empty permissions) / hosts %s + %s',
            $email,
            $adminHostName,
            $siteHostName,
        ));

        return Command::SUCCESS;
    }

    private function ensureSystemRole(string $name, string $label): Role
    {
        $role = $this->roles->findOneBy(['name' => $name]);
        if (!$role instanceof Role) {
            $role = (new Role())->setName($name)->setLabel($label);
        }
        $role->setLabel($label)->setIsReadOnly(true);
        $this->em->persist($role);

        return $role;
    }
}
