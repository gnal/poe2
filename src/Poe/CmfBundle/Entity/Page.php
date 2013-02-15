<?php

namespace Poe\CmfBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Msi\CmfBundle\Entity\Page as BasePage;

/**
 * @ORM\Entity
 */
class Page extends BasePage
{
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\ManyToMany(targetEntity="Block", mappedBy="pages")
     */
    protected $blocks;

    /**
     * @ORM\ManyToOne(targetEntity="Site")
     */
    protected $site;

    /**
     * @ORM\OneToMany(targetEntity="PageTranslation", mappedBy="object", cascade={"persist", "remove"})
     */
    protected $translations;
}
