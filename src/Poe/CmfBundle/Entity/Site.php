<?php

namespace Poe\CmfBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Msi\CmfBundle\Entity\Site as BaseSite;

/**
 * @ORM\Entity
 */
class Site extends BaseSite
{
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\OneToMany(targetEntity="SiteTranslation", mappedBy="object", cascade={"persist", "remove"})
     */
    protected $translations;
}
