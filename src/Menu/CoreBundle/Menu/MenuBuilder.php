<?php
namespace Menu\CoreBundle\Menu;

use Knp\Menu\FactoryInterface;
use Symfony\Component\HttpFoundation\Request;

class MenuBuilder
{
    private $factory;

    /**
     * @param FactoryInterface $factory
     */
    public function __construct(FactoryInterface $factory, $translator)
    {
        $this->factory = $factory;
        $this->translator = $translator;
    }

    public function createPrimaryMenu(Request $request)
    {
        $menu = $this->factory->createItem('root');

        $menu->addChild('menu_core.menu.home', array('route' => 'menu_core_homepage'))
                ->setLabel($this->translator->trans('menu_core.menu.home', array(), 'menu' ));
        $menu->addChild('menu_core.menu.menu1', array('route' => 'menu_core_menu1'))
                ->setLabel($this->translator->trans('menu_core.menu.menu1', array(), 'menu' ));
        $menu->addChild('menu_core.menu.menu2', array('route' => 'menu_core_menu2'))
                ->setLabel($this->translator->trans('menu_core.menu.menu2', array(), 'menu' ));
        $about = $menu->addChild('menu_core.menu.about', array('route' => 'menu_core_about'))
                ->setLabel($this->translator->trans('menu_core.menu.about', array(), 'menu' ));
        $about->addChild('menu_core.menu.help', array('route' => 'menu_core_help'))
                ->setLabel($this->translator->trans('menu_core.menu.help', array(), 'menu' ));
        $about->addChild('menu_core.menu.nvision', array('uri' => 'http://www.nvision.lu'))
                ->setAttribute('target', "_blank")
                ->setLabel($this->translator->trans('menu_core.menu.nvision', array(), 'menu' ));

        return $menu;
    }
}