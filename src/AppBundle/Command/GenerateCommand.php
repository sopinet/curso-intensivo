<?php
// src/AppBundle/Command/GeneratePdfCommand.php
namespace AppBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class GenerateCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('app:generate');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln("Comando funcionando");
    }
}