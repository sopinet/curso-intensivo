<?php
// src/AppBundle/Command/GeneratePdfCommand.php
namespace AppBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class GeneratePdfCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('app:generatePdf');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln("Comando funcionando");
    }
}