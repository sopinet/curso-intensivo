<?php
namespace AppBundle\EventListener;

use AppBundle\Entity\Card;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Monolog\Logger;

class CardListener
{
    public $loggerListener;

    public function __construct(Logger $loggerListener)
    {
        $this->loggerListener = $loggerListener;
    }

    public function postPersist(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();

        if (!$entity instanceof Card) {
            return;
        }

        // No se usa en este ejemplo
        $entityManager = $args->getEntityManager();

        $this->loggerListener->info("Se ha creado una nueva carta con el nombre: ".$entity->getTitle());
    }
}