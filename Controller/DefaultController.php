<?php

namespace Chigi\Bundle\ChijiBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction($name)
    {
        return $this->render('ChigiChijiBundle:Default:index.html.twig', array('name' => $name));
    }
}
