<?php

namespace LP\CoreBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction($name)
    {
        return $this->render('LPCoreBundle:Default:index.html.twig', array('name' => $name));
    }
}
