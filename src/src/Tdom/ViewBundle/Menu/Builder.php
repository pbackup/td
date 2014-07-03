<?php

namespace Tdom\ViewBundle\Menu;

use Knp\Menu\FactoryInterface;
use Symfony\Component\DependencyInjection\ContainerAware;

class Builder extends ContainerAware
{
    public function mainMenu(FactoryInterface $factory, array $options)
    {
        $menu = $factory->createItem('root');
        $menu->setChildrenAttribute('class', 'nav navbar-nav navbar-right');
        $menu->addChild('User', array('uri' => '#'))->setAttributes(array('class' => 'drop-down', 'data-toggle' => 'dropdown'));
        $menu['User']->addChild('Profile', array('uri' => '#'))->setAttribute('divider_append', true);
        $menu['User']->addChild('Logout', array('uri' => '#'));

        return $menu;
    }
}
