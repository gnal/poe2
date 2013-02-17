<?php

namespace Poe\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @ORM\Entity
 */
class ItemProperty
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
     * @ORM\ManyToOne(targetEntity="Item", inversedBy="properties")
     * @ORM\JoinColumn(name="item_id", referencedColumnName="id", onDelete="CASCADE")
     */
    protected $item;

    /**
     * @ORM\ManyToOne(targetEntity="Property")
     */
    protected $property;

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

    public function getProperty()
    {
        return $this->property;
    }

    public function setProperty($property)
    {
        $this->property = $property;

        return $this;
    }

    public function getId()
    {
        return $this->id;
    }
}
