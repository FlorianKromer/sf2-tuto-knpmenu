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
    public function __construct(FactoryInterface $factory)
    {
        $this->factory = $factory;
    }

    public function createPrimaryMenu(Request $request)
    {
        $menu = $this->factory->createItem('root');

        $menu->addChild('Home', array('route' => 'menu_core_homepage'));
        $menu->addChild('Menu1', array('route' => 'menu_core_menu1'));
        $menu->addChild('Menu2', array('route' => 'menu_core_menu1'));
        $menu->addChild('About', array('route' => 'menu_core_about'));
        $menu->addChild('Help', array('route' => 'menu_core_help'));

        return $menu;
    }
}