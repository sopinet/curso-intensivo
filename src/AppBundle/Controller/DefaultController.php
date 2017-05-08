<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Routing\Annotation\Route;

class DefaultController extends Controller
{
    /**
     * @Route("/appDemo", name="appDemo")
     */
    public function indexAction()
    {
        $this->get('app.generate')->generate();
        return $this->render('appDemo.html.twig');
    }
}