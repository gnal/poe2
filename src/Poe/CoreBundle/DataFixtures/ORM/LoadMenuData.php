<?php

namespace Poe\CoreBundle\DataFixtures\ORM;

use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;

class LoadMenuData extends AbstractFixture implements ContainerAwareInterface, OrderedFixtureInterface
{
    protected $container;
    protected $rootManager;
    protected $nodeManager;

    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
        $this->rootManager = $container->get('msi_cmf.menu_root_manager');
        $this->nodeManager = $container->get('msi_cmf.menu_node_manager');
    }

    public function load(ObjectManager $manager)
    {
        // ADMIN MENU
        // root
        $root = $this->rootManager->create();
        $this->rootManager->createTranslations($root, array('en'));

        $root->getTranslation()->setPublished(true)->setName('admin');
        $manager->persist($root);
        $manager->flush();
        // siite
        $menu = $this->rootManager->create();
        $this->nodeManager->createTranslations($menu, array('en'));
        $menu->getTranslation()->setRoute('@msi_cmf_site_admin_list');
        $menu->setParent($root);
        $menu->getTranslation()->setPublished(true)->setName('Sites');
        $manager->persist($menu);
        // security
        $security = $this->rootManager->create();
        $this->nodeManager->createTranslations($security, array('en'));
        $security->setParent($root);
        $security->getTranslation()->setPublished(true)->setName('Security');
        $manager->persist($security);
            // users
            $menu = $this->rootManager->create();
            $this->nodeManager->createTranslations($menu, array('en'));
            $menu->getTranslation()->setRoute('@msi_user_user_admin_list');
            $menu->setParent($security);
            $menu->getTranslation()->setPublished(true)->setName('Users');
            $manager->persist($menu);
            // groups
            $menu = $this->rootManager->create();
            $this->nodeManager->createTranslations($menu, array('en'));
            $menu->getTranslation()->setRoute('@msi_user_group_admin_list');
            $menu->setParent($security);
            $menu->getTranslation()->setPublished(true)->setName('Groups');
            $manager->persist($menu);
        // menu
        $menu = $this->rootManager->create();
        $this->nodeManager->createTranslations($menu, array('en'));
        $menu->getTranslation()->setRoute('@msi_cmf_menu_root_admin_list');
        $menu->setParent($root);
        $menu->getTranslation()->setPublished(true)->setName('Menus');
        $manager->persist($menu);


        // Content menu
        $content = $this->rootManager->create();
        $this->nodeManager->createTranslations($content, array('en'));
        $content->setParent($root);
        $content->getTranslation()->setPublished(true)->setName('Content');
        $manager->persist($content);
            // pages
            $menu = $this->rootManager->create();
            $this->nodeManager->createTranslations($menu, array('en'));
            $menu->getTranslation()->setRoute('@msi_cmf_page_admin_list');
            $menu->setParent($content);
            $menu->getTranslation()->setPublished(true)->setName('Pages');
            $manager->persist($menu);
            // blocks
            $menu = $this->rootManager->create();
            $this->nodeManager->createTranslations($menu, array('en'));
            $menu->getTranslation()->setRoute('@msi_cmf_block_admin_list');
            $menu->setParent($content);
            $menu->getTranslation()->setPublished(true)->setName('Blocks');
            $manager->persist($menu);

        // FLUSH
        $manager->flush();
    }

    public function getOrder()
    {
        return 4;
    }
}
