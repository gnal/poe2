<?php

namespace Poe\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @ORM\Entity
 */
class ItemImplicitMod
{
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(type="decimal", scale=1)
     */
    protected $value;

    /**
     * @ORM\ManyToOne(targetEntity="Item", inversedBy="implicitMods")
     * @ORM\JoinColumn(name="item_id", referencedColumnName="id", onDelete="CASCADE")
     */
    protected $item;

    /**
     * @ORM\ManyToOne(targetEntity="ImplicitMod")
     */
    protected $implicitMod;

    public function getValue()
    {
        return $this->value;
    }

    public function setValue($value)
    {
        $this->value = $value;

        return $this;
    }

    public function getItem()
    {
        return $this->item;
    }

    public function setItem($item)
    {
        $this->item = $item;

        return $this;
    }

    public function getImplicittMod()
    {
        return $this->implicitMod;
    }

    public function setImplicittMod($implicitMod)
    {
        $this->implicitMod = $implicitMod;

        return $this;
    }

    public function getId()
    {
        return $this->id;
    }
}
