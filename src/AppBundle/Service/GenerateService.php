<?php
namespace AppBundle\Service;

use AppBundle\Entity\Card;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Knp\Bundle\SnappyBundle\Snappy\LoggableGenerator;
use Monolog\Logger;
use Symfony\Bundle\TwigBundle\Debug\TimedTwigEngine;
use Symfony\Component\Filesystem\Filesystem;

class GenerateService {
    const FORMAT_PDF = 'pdf';
    const FORMAT_HTML = 'html';

    private $entityManager;
    private $loggerPdf;
    private $templating;
    private $knpSnappyPdf;

    public function __construct(EntityManager $entityManager, Logger $loggerPdf, TimedTwigEngine $templating, LoggableGenerator $knpSnappyPdf)
    {
        $this->entityManager = $entityManager;
        $this->loggerPdf = $loggerPdf;
        $this->templating = $templating;
        $this->knpSnappyPdf = $knpSnappyPdf;
    }

    public function generateHtml(Card $card, $override = true) {
        $html = $this->templating->render('previewCard.html.twig', array(
            'card' => $card
        ));

        $fs = new Filesystem();
        $filename = 'card.html';
        // Si el fichero que vamos a generar ya existe y hemos marcado sobreescribir, lo eliminamos previamente
        if ($fs->exists($filename) && $override) {
            $fs->remove($filename);
            $this->loggerPdf->info('Ya existía un fichero con el mismo nombre, se ha eliminado');
        } else if ($override == false) {
            return $filename;
        }

        $fs->dumpFile($filename, $html);

        if ($fs->exists($filename)) {
            $this->loggerPdf->info('Fichero generado con éxito.');
            return $filename;
        } else {
            $this->loggerPdf->error('El fichero no se ha podido generar.');
            return false;
        }
    }

    public function generatePdf(Card $card, $override = true) {
        $html = $this->templating->render('previewCard.html.twig', array(
            'card' => $card
        ));

        $fs = new Filesystem();
        $filename = 'card.pdf';
        // Si el fichero que vamos a generar ya existe y hemos marcado sobreescribir, lo eliminamos previamente
        if ($fs->exists($filename) && $override) {
            $fs->remove($filename);
            $this->loggerPdf->info('Ya existía un fichero con el mismo nombre, se ha eliminado');
        } else if ($override == false) {
            return $filename;
        }

        $this->knpSnappyPdf->generateFromHtml(
            $html,
            $filename
        );

        if ($fs->exists($filename)) {
            $this->loggerPdf->info('Fichero generado con éxito.');
            return $filename;
        } else {
            $this->loggerPdf->error('El fichero no se ha podido generar.');
            return false;
        }
    }

    public function generate($cardId, $format = self::FORMAT_HTML) {
        if ($format == null) {
            $format = self::FORMAT_HTML;
        }

        /** @var EntityRepository $repositoryCard */
        $repositoryCard = $this->entityManager->getRepository("AppBundle:Card");
        /** @var Card $card */
        $card = $repositoryCard->findOneById($cardId);

        if (!$card instanceof Card) {
            $this->loggerPdf->error('No se ha encontrado la carta especificada en la base de datos.');
        } else {
            $this->loggerPdf->info('Carta encontrada en la base de datos.');
            if ($format == self::FORMAT_HTML) {
                return $this->generateHtml($card);
            } else if ($format == self::FORMAT_PDF) {
                return $this->generatePdf($card);
            } else {
                $this->loggerPdf->error('Formato no válido.');
                return false;
            }
        }
    }
}
?>