<?php

declare(strict_types=1);

namespace App\Command;

use App\Entity\Role;
use App\Entity\User;
use App\Repository\RoleRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

#[AsCommand(name: 'app:create-admin', description: 'Create or update an administrator user')]
final class CreateAdminUserCommand extends Command
{
    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly UserRepository $users,
        private readonly RoleRepository $roles,
        private readonly UserPasswordHasherInterface $passwordHasher,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addArgument('email', InputArgument::REQUIRED)
            ->addArgument('password', InputArgument::REQUIRED);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $email = (string) $input->getArgument('email');
        $password = (string) $input->getArgument('password');

        $adminRole = $this->roles->findOneBy(['name' => 'ROLE_ADMIN']);
        if (!$adminRole instanceof Role) {
            $adminRole = (new Role())->setName('ROLE_ADMIN')->setLabel('Administrator');
            $this->em->persist($adminRole);
        }

        $user = $this->users->findOneBy(['email' => strtolower($email)]) ?? (new User())->setEmail($email);
        $user->setPassword($this->passwordHasher->hashPassword($user, $password));
        $user->addRole($adminRole);
        $this->em->persist($user);
        $this->em->flush();

        $io->success(sprintf('Admin user ready: %s', $user->getEmail()));

        return Command::SUCCESS;
    }
}
