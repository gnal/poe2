<?php

namespace Poe\CoreBundle\DataFixtures\ORM;

use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;

class LoadUserData extends AbstractFixture implements ContainerAwareInterface, OrderedFixtureInterface
{
    protected $dic;

    public function setContainer(ContainerInterface $dic = null)
    {
        $this->dic = $dic;
    }

    public function load(ObjectManager $manager)
    {
        $userManager = $this->dic->get('fos_user.user_manager');
        // superadmin
        $user = $userManager->createUser();
        $user
            ->setUsername('superadmin')
            ->setEmail('alexisjoubert@groupemsi.com')
            ->setPlainPassword('coding9966')
            ->setConfirmationToken(null)
            ->setEnabled(true)->setLocked(false)
            ->addRole('ROLE_SUPER_ADMIN')
        ;
        $this->addReference('user1', $user);
        $userManager->updateUser($user);
    }

    public function getOrder()
    {
        return 3;
    }
}
