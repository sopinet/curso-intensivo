<?php

namespace AppBundle\Controller;

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
     * @Route("/previewCard/{cardId}", name="panel_previewCard")
     * @Method("GET")
     * @return Response
     */
    public function previewCardAction($cardId)
    {
        var_dump($this->request->query->all());

        // Lo hemos inyectado en el Controlador, así que ya no es necesario
        // $entityManager = $this->get('doctrine.orm.entity_manager');

        /** @var EntityRepository $repositoryCard */
        $repositoryCard = $this->entityManager->getRepository("AppBundle:Card");

        $card = $repositoryCard->findOneBy(array('id' => $cardId));

        if ($card == 0 || $card == null) {
            throw $this->createNotFoundException('The card does not exist');

            // También se podría devolver un String y el código HTTP 404
            // return new Response("notFound", Response::HTTP_NOT_FOUND);
        }

        return $this->render('previewCard.html.twig', array(
            'cardNumber' => $cardId
        ));

        // También se podría devolver un JSON
        // return new JsonResponse(array('cardNumber' => $card));

        // También se podría devolver simplemente un String
        // return new Response("previewCard - ".$card);
    }
}