<?php

namespace Menu\CoreBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction()
    {
        return $this->render('MenuCoreBundle:Default:index.html.twig');
    }

    public function helpAction()
    {
        return $this->render('MenuCoreBundle:Default:index.html.twig');
    }

    public function aboutAction()
    {
        return $this->render('MenuCoreBundle:Default:index.html.twig');
    }

    public function menu1Action()
    {
        return $this->render('MenuCoreBundle:Default:index.html.twig');
    }

    public function menu2Action()
    {
        return $this->render('MenuCoreBundle:Default:index.html.twig');
    }
    public function searchAction()
    {
        return $this->render('MenuCoreBundle:Default:index.html.twig');
    }
}
