<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Card;
use AppBundle\Repository\CardRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use JMS\DiExtraBundle\Annotation\Inject;

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
        $url = $this->generateUrl('panel_previewCard', array('card' => '1'));

        var_dump($url);

        return new RedirectResponse($url);

        // También se podría hacer la redirección a URL directamente
        // return $this->redirect($url);

        // También se podría devolver un String
        // return new Response("dashboard");
        // return $this->render('dashboard.html.twig');
    }

    /**
     * @Route("/preferences", name="panel_preferences")
     */
    public function preferencesAction()
    {
        $mailer = $this->get('mailer');
        var_dump($mailer);

        var_dump($this->container->getParameter('locale'));

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
     * @Method("GET")
     * @return Response
     */
    public function previewCardAction(Card $card)
    {
        var_dump($this->request->query->all());

        // Lo hemos inyectado en el Controlador, así que ya no es necesario
        // $entityManager = $this->get('doctrine.orm.entity_manager');

        // No hace falta buscar la carta por ID, porque Symfony lo hace automáticamente
        // $repositoryCard = $this->entityManager->getRepository("AppBundle:Card");
        // $card = $repositoryCard->findOneBy(array('id' => $cardId));

        if ($card == null) {
            throw $this->createNotFoundException('The card does not exist');

            // También se podría devolver un String y el código HTTP 404
            // return new Response("notFound", Response::HTTP_NOT_FOUND);
        }

        return $this->redirect(
            $this->generateUrl('panel_card_show', array(
                'id' => $card->getId()
            ))
        );

        // Se podría renderizar un TWIG para este Controlador
        /**
        return $this->render('previewCard.html.twig', array(
            'cardNumber' => $cardId
        ));
        **/

        // También se podría devolver un JSON
        // return new JsonResponse(array('cardNumber' => $card));

        // También se podría devolver simplemente un String
        // return new Response("previewCard - ".$card);
    }
}