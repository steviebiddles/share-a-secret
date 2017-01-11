<?php

namespace AppBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class SecretClearCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('app:secrets:clear')
            ->setDescription('Clear expired secrets.')
            ->setHelp("This command clears any expired secrets...")
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $secretRepository = $this->getContainer()->get('app.secret.repository');

        $output->writeln(sprintf("Removed %d expired secret(s).", $secretRepository->removeInactiveSecrets()));
    }
}
