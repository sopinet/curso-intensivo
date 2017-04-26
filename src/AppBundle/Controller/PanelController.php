<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Card;
use AppBundle\Repository\CardRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Finder\Exception\AccessDeniedException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use JMS\DiExtraBundle\Annotation\Inject;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

/**
 * @Route("/panel")
 */
class PanelController extends Controller
{
    /**
     * @Inject("request", strict = false)
     */
    private $request;

    /**
     * @Inject("doctrine.orm.entity_manager")
     */
    private $entityManager;

    /**
     * @Route("/", name="panel_dashboard")
     */
    public function dashboardAction()
    {
        // return $this->render('dashboard.html.twig');
    }

    /**
     * @Route("/preferences", name="panel_preferences")
     */
    public function preferencesAction()
    {
        return new Response("preferences");
    }

    /**
     * @Route("/searchCard/{stringSearch}", name="panel_searchCard")
     * @param $stringSearch
     * @return Response
     */
    public function searchCardAction($stringSearch) {
        /** @var CardRepository $repositoryCard */
        $repositoryCard = $this->entityManager->getRepository("AppBundle:Card");
        $results = $repositoryCard->customFindByString($stringSearch);

        if (count($results) > 0) {
            var_dump("Se han encontrado " . count($results) . " resultados, vamos a mostrar el primero...");
            return $this->redirect(
                $this->generateUrl('panel_card_show', array(
                    'id' => $results[0]->getId()
                ))
            );
        } else {
            return new Response("No se han encontrado resultados.");
        }
    }

    /**
     * @Route("/previewCard/{card}", name="panel_previewCard")
     * @Security("is_granted('view', card)")
     * @Method("GET")
     * @return Response
     */
    public function previewCardAction(Card $card)
    {
        return $this->render('previewCard.html.twig', array(
            'card' => $card
        ));
    }
}