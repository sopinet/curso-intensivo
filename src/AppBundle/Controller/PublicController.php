<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PublicController extends Controller
{
    /**
     * @Route("/", name="public_landing")
     */
    public function landingAction()
    {
        return new Response("Landing");
        // return $this->render('landing.html.twig');
    }
}