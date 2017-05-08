<?php
// src/AppBundle/Command/GeneratePdfCommand.php
namespace AppBundle\Command;

use AppBundle\Entity\Card;
use AppBundle\Service\GenerateService;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Monolog\Logger;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Filesystem\Filesystem;

class GenerateCommand extends ContainerAwareCommand
{
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

        $output->writeln("Vamos a intentar generar la carta número ".$cardId);
        /** @var GenerateService $generateService */
        $generateService = $this->getContainer()->get('app.generate');
        if ($generateService->generate($cardId, $format)) {
            $output->writeln("Carta generada con éxito.");
        } else {
            $output->writeln("<error>Error al generar la carta.</error>");
        }
    }
}