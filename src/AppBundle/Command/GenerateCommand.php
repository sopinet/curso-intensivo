<?php
// src/AppBundle/Command/GeneratePdfCommand.php
namespace AppBundle\Command;

use AppBundle\Entity\Card;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Filesystem\Filesystem;

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

        $output->writeln("Vamos a intentar generar la carta número ".$cardId." en formato ".$format."...");

        /** @var EntityManager $entityManager */
        $entityManager = $this->getContainer()->get('doctrine.orm.entity_manager');
        /** @var EntityRepository $repositoryCard */
        $repositoryCard = $entityManager->getRepository("AppBundle:Card");
        /** @var Card $card */
        $card = $repositoryCard->findOneById($cardId);

        // Si la carta no existe
        if (!$card instanceof Card) {
            $output->writeln("<error>No se ha encontrado la carta especificada en la base de datos.</error>");
        // Si la carta sí existe
        } else {
            $output->writeln('<comment>Carta encontrada en la base de datos.</comment>');
            $html = $this->getContainer()->get('templating')->render('previewCard.html.twig', array(
                'cardNumber' => $card->getId()
            ));

            $fs = new Filesystem();
            // Asignamos el nombre del fichero a generar a una variable para poder trabajar con algunas comprobaciones
            // de forma común para cualquier tipo de formato especificado.
            $filenameForGenerate = 'card.' . $format;

            // Si el fichero que vamos a generar ya existe lo eliminamos previamente
            if ($fs->exists($filenameForGenerate)) {
                $output->writeln("<comment>Ya existía un fichero " . $format . " con el mismo nombre, se ha eliminado.</comment>");
                $fs->remove($filenameForGenerate);
            }

            // Ahora, según el formato especificado, se genera un HTML o un PDF
            if ($format == self::FORMAT_HTML) {
                $fs->dumpFile($filenameForGenerate, $html);
            }
            else {
                $this->getContainer()->get('knp_snappy.pdf')->generateFromHtml(
                    $html,
                    $filenameForGenerate
                );
            }

            // Se hace una comprobación final para indicar si el fichero existe, en cuyo caso entenderemos que
            // éste se ha generado correctamente.
            if ($fs->exists($filenameForGenerate)) {
                $output->writeln('<info>El comando se ha generado con éxito y el fichero '.$format.' se ha generado.</info>');
            } else {
                $output->writeln('<error>Ha ocurrido un error y el fichero '.$format.' no se ha generado.</error>');
            }
        }
    }
}