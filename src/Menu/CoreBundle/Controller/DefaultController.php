<?php

namespace Menu\CoreBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction()
    {
        return $this->render('MenuCoreBundle:Default:index.html.twig');
    }
}
