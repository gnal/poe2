<?php

namespace Poe\CmfBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Msi\CmfBundle\Entity\MenuTranslation as BaseMenuTranslation;

/**
 * @ORM\Entity
 */
class MenuTranslation extends BaseMenuTranslation
{
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\ManyToOne(targetEntity="Menu", inversedBy="translations")
     */
    protected $object;
}
