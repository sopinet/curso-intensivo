<?php
// src/AppBundle/Command/GeneratePdfCommand.php
namespace AppBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class GenerateCommand extends ContainerAwareCommand
{
    const FORMAT_PDF = 'pdf';
    const FORMAT_HTML = 'html';

    protected function configure()
    {
        $this
            ->setName('app:generate')
            ->addArgument(
                'cardId',
                InputArgument::REQUIRED,
                'Indica el ID de la carta que deseas generar'
            )
            ->addOption(
                'format',
                'f',
                InputArgument::OPTIONAL,
                'Indica el formato en el que se renderizará una carta, pdf o html'
            );
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $cardId = $input->getArgument('cardId');
        $format = $input->getOption('format');
        if ($format == null) {
            $format = self::FORMAT_PDF;
        }

        $output->writeln("Comando generate para cardId: ".$cardId. " y opción format: ".$format);
    }
}