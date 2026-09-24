<?php

namespace App\Command;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

#[AsCommand(
    name: 'app:user:create',
    description: 'Crée un utilisateur',
)]
class CreateUserCommand extends Command
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly UserPasswordHasherInterface $passwordHasher,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addOption(
                'email',
                null,
                InputOption::VALUE_OPTIONAL,
                'Email de l\'utilisateur',
                'test@test.fr'
            )
            ->addOption(
                'password',
                null,
                InputOption::VALUE_OPTIONAL,
                'Mot de passe de l\'utilisateur',
                'test'
            )
            ->addOption(
                'name',
                null,
                InputOption::VALUE_OPTIONAL,
                'Nom de l\'utilisateur',
                'test'
            )
            ->addOption(
                'roles',
                null,
                InputOption::VALUE_OPTIONAL,
                'Rôles séparés par des virgules',
                'ROLE_USER'
            );
    }

    protected function execute(
        InputInterface $input,
        OutputInterface $output
    ): int {
        $io = new SymfonyStyle($input, $output);

        $email = $input->getOption('email');
        $password = $input->getOption('password');
        $name = $input->getOption('name');
        $roles = $input->getOption('roles');

        $existingUser = $this->entityManager
            ->getRepository(User::class)
            ->findOneBy(['email' => $email]);

        if ($existingUser !== null) {
            $io->error(sprintf(
                'Un utilisateur avec l\'email "%s" existe déjà.',
                $email
            ));

            return Command::FAILURE;
        }

        $user = new User();

        $user->setEmail($email);
        $user->setName($name);

        $user->setRoles(
            array_filter(
                array_map('trim', explode(',', $roles))
            )
        );

        $hashedPassword = $this->passwordHasher->hashPassword(
            $user,
            $password
        );

        $user->setPassword($hashedPassword);

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        $io->success(sprintf(
            'Utilisateur "%s" créé avec succès.',
            $email
        ));

        return Command::SUCCESS;
    }
}
