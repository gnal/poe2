<?php

namespace Poe\CmfBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction($name)
    {
        return $this->render('PoeCmfBundle:Default:index.html.twig', array('name' => $name));
    }
}
