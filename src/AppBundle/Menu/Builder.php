<?php

namespace AppBundle\Menu;

use Knp\Menu\FactoryInterface;
use Symfony\Component\HttpFoundation\RequestStack;

class Builder
{
    private $factory;

    /**
     * @param FactoryInterface $factory
     */
    public function __construct(FactoryInterface $factory, $repository, $security)
    {
        $this->factory = $factory;
        $this->repository = $repository;
        $this->security = $security;
    }

    public function createMainMenu(RequestStack $requestStack)
    {
        $menu = $this->factory->createItem('root');
        $menu->setChildrenAttributes(array('class' => 'nav navbar-nav'));

        $menu->addChild('Home', array('route' => 'homepage'));

        $menu->addChild('Recently Generated Docs', array('route' => 'about'));

        $menu['Recently Generated Docs']->setAttributes(array('class' => 'dropdown'));
        $menu['Recently Generated Docs']->setLinkAttributes(array('class' => 'dropdown-toggle', 'data-toggle' => 'dropdown', 'role' => 'button', 'aria-expanded' => 'false'));
        $menu['Recently Generated Docs']->setChildrenAttributes(array('class' => 'dropdown-menu'));

        foreach ($this->repository->getRecentProjects() as $project) {
            $menu['Recently Generated Docs']->addChild(sprintf('%s/%s', $project->getOwner()->getUsername(), $project->getName()), array(
                'route' => 'project',
                'routeParameters' => array('owner' => $project->getOwner()->getUsername(), 'project' => $project->getName()
            )));
        }

        $menu['Recently Generated Docs']->addChild('')->setAttributes(array('class' => 'divider'));;
        $menu['Recently Generated Docs']->addChild('More Docs...', array('route' => 'projects'));

        $menu->addChild('About', array('route' => 'about'));

        return $menu;
    }

    public function createNavbarRightMenu(RequestStack $requestStack)
    {
        $menu = $this->factory->createItem('root');
        $menu->setChildrenAttributes(array('class' => 'nav navbar-nav navbar-right'));

        if ($this->security->isGranted(array('ROLE_ADMIN'))) {
            $menu->addChild('Admin', array('route' => 'sonata_admin_redirect'));
        }

        if ($this->security->isGranted(array('ROLE_USER'))) {
            $menu->addChild('Logout', array('route' => 'sonata_user_security_logout'));
        } else {
            $menu->addChild('Login', array('route' => 'sonata_user_security_login'));
            $menu->addChild('Register', array('route' => 'sonata_user_registration_register'));
        }

        return $menu;
    }
}

