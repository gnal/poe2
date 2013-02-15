<?php

namespace Poe\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @ORM\Entity
 */
class Item
{
    const LEAGUE_HARDCORE = 0;

    const LEAGUE_DEFAULT = 1;

    const FRAME_TYPE_NORMAL = 0;

    const FRAME_TYPE_MAGIC = 1;

    const FRAME_TYPE_RARE = 2;

    const FRAME_TYPE_UNIQUE = 3;

    const FRAME_TYPE_GEM = 4;

    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $name;

    /**
     * @ORM\ManyToOne(targetEntity="ItemType", inversedBy="items")
     */
    protected $type;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    protected $quality;

    /**
     * @ORM\Column(type="decimal", scale=1, nullable=true)
     */
    protected $dps;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    protected $minPhysicalDamage;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    protected $maxPhysicalDamage;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    protected $minFireDamage;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    protected $maxFireDamage;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    protected $minColdDamage;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    protected $maxColdDamage;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    protected $minLightningDamage;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    protected $maxLightningDamage;

    /**
     * @ORM\Column(type="decimal", scale=1, nullable=true)
     */
    protected $averagePhysicalDamage;

    /**
     * @ORM\Column(type="decimal", scale=1, nullable=true)
     */
    protected $averageFireDamage;

    /**
     * @ORM\Column(type="decimal", scale=1, nullable=true)
     */
    protected $averageColdDamage;

    /**
     * @ORM\Column(type="decimal", scale=1, nullable=true)
     */
    protected $averageLightningDamage;

    /**
     * @ORM\Column(type="decimal", scale=1, nullable=true)
     */
    protected $averageElementalDamage;

    /**
     * @ORM\Column(type="decimal", scale=2, nullable=true)
     */
    protected $criticalStrikeChance;

    /**
     * @ORM\Column(type="decimal", scale=2, nullable=true)
     */
    protected $attacksPerSecond;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    protected $armour;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    protected $evasionRating;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    protected $energyShield;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    protected $intReq;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    protected $dexReq;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    protected $strReq;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    protected $lvlReq;

    /**
     * @ORM\Column(type="boolean")
     */
    protected $verified;

    /**
     * @ORM\Column(type="integer")
     */
    protected $frameType;

    /**
     * @ORM\Column(type="integer")
     */
    protected $league;

    /**
     * @ORM\Column(type="string")
     */
    protected $sockets;

    /**
     * @ORM\Column(type="boolean")
     */
    protected $identified;

    /**
     * @ORM\Column(type="string")
     */
    protected $accountName;

    /**
     * @ORM\Column(type="integer")
     */
    protected $threadId;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    protected $mapLvl;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    protected $increasedPhysicalDamage;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    protected $increasedStunDuration;


    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    protected $intelligence;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    protected $dexterity;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    protected $strength;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    protected $increasedAttackSpeed;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    protected $increasedCastSpeed;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    protected $manaOnKill;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    protected $lifeOnKill;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    protected $increasedElementalDamageWeapons;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    protected $accuracyRating;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    protected $lifeLeech;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    protected $manaLeech;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    protected $chaosResist;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    protected $coldResist;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    protected $lightningResist;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    protected $fireResist;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    protected $reducedStunThreshold;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    protected $lifeOnHit;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    protected $maxEnergyShield;

    public function calcDps()
    {
        $combinedAverageDamage = $this->calcAveragePhysicalDamage() + $this->calcAverageFireDamage() + $this->calcAverageColdDamage() + $this->calcAverageLightningDamage();

        $crit = 1 + $this->criticalStrikeChance / 100;

        $dps = $this->attacksPerSecond * $combinedAverageDamage * $crit;

        return $dps;
    }

    public function calcAverageElementalDamage()
    {
        return $this->calcAverageFireDamage() + $this->calcAverageColdDamage() + $this->calcAverageLightningDamage();
    }

    public function calcAveragePhysicalDamage()
    {
        return $this->calcAverageDamage($this->minPhysicalDamage, $this->maxPhysicalDamage);
    }

    public function calcAverageFireDamage()
    {
        return $this->calcAverageDamage($this->minFireDamage, $this->maxFireDamage);
    }

    public function calcAverageColdDamage()
    {
        return $this->calcAverageDamage($this->minColdDamage, $this->maxColdDamage);
    }

    public function calcAverageLightningDamage()
    {
        return $this->calcAverageDamage($this->minLightningDamage, $this->maxLightningDamage);
    }

    public function getAverageElementalDamage()
    {
        return $this->averageElementalDamage;
    }

    public function setAverageElementalDamage($averageElementalDamage)
    {
        $this->averageElementalDamage = $averageElementalDamage;

        return $this;
    }

    public function getIntelligence()
    {
        return $this->int;
    }

    public function setIntelligence($intelligence)
    {
        $this->intelligence = $intelligence;

        return $this;
    }

    public function getDexterity()
    {
        return $this->dexterity;
    }

    public function setDexterity($dexterity)
    {
        $this->dexterity = $dexterity;

        return $this;
    }

    public function getStrength()
    {
        return $this->strength;
    }

    public function setStrength($strength)
    {
        $this->strength = $strength;

        return $this;
    }

    public function getIncreasedAttackSpeed()
    {
        return $this->increasedAttackSpeed;
    }

    public function setIncreasedAttackSpeed($increasedAttackSpeed)
    {
        $this->increasedAttackSpeed = $increasedAttackSpeed;

        return $this;
    }

    public function getIncreasedCastSpeed()
    {
        return $this->increasedCastSpeed;
    }

    public function setIncreasedCastSpeed($increasedCastSpeed)
    {
        $this->increasedCastSpeed = $increasedCastSpeed;

        return $this;
    }

    public function getManaOnKill()
    {
        return $this->manaOnKill;
    }

    public function setManaOnKill($manaOnKill)
    {
        $this->manaOnKill = $manaOnKill;

        return $this;
    }

    public function getLifeOnKill()
    {
        return $this->lifeOnKill;
    }

    public function setLifeOnKill($lifeOnKill)
    {
        $this->lifeOnKill = $lifeOnKill;

        return $this;
    }

    public function getIncreasedElementalDamageWeapons()
    {
        return $this->increasedElementalDamageWeapons;
    }

    public function setIncreasedElementalDamageWeapons($increasedElementalDamageWeapons)
    {
        $this->increasedElementalDamageWeapons = $increasedElementalDamageWeapons;

        return $this;
    }

    public function getAccuracyRating()
    {
        return $this->accuracyRating;
    }

    public function setAccuracyRating($accuracyRating)
    {
        $this->accuracyRating = $accuracyRating;

        return $this;
    }

    public function getLifeLeech()
    {
        return $this->lifeLeech;
    }

    public function setLifeLeech($lifeLeech)
    {
        $this->lifeLeech = $lifeLeech;

        return $this;
    }

    public function getManaLeech()
    {
        return $this->manaLeech;
    }

    public function setManaLeech($manaLeech)
    {
        $this->manaLeech = $manaLeech;

        return $this;
    }

    public function getChaosResist()
    {
        return $this->chaosResist;
    }

    public function setChaosResist($chaosResist)
    {
        $this->chaosResist = $chaosResist;

        return $this;
    }

    public function getColdResist()
    {
        return $this->coldResist;
    }

    public function setColdResist($coldResist)
    {
        $this->coldResist = $coldResist;

        return $this;
    }

    public function getLightningResist()
    {
        return $this->lightningResist;
    }

    public function setLightningResist($lightningResist)
    {
        $this->lightningResist = $lightningResist;

        return $this;
    }

    public function getFireResist()
    {
        return $this->fireResist;
    }

    public function setFireResist($fireResist)
    {
        $this->fireResist = $fireResist;

        return $this;
    }

    public function getReducedStunThreshold()
    {
        return $this->reducedStunThreshold;
    }

    public function setReducedStunThreshold($reducedStunThreshold)
    {
        $this->reducedStunThreshold = $reducedStunThreshold;

        return $this;
    }

    public function getLifeOnHit()
    {
        return $this->lifeOnHit;
    }

    public function setLifeOnHit($lifeOnHit)
    {
        $this->lifeOnHit = $lifeOnHit;

        return $this;
    }

    public function getMaxEnergyShield()
    {
        return $this->maxEnergyShield;
    }

    public function setMaxEnergyShield($maxEnergyShield)
    {
        $this->maxEnergyShield = $maxEnergyShield;

        return $this;
    }

    public function getIncreasedPhysicalDamage()
    {
        return $this->increasedPhysicalDamage;
    }

    public function setIncreasedPhysicalDamage($increasedPhysicalDamage)
    {
        $this->increasedPhysicalDamage = $increasedPhysicalDamage;

        return $this;
    }

    public function getIncreasedStunDuration()
    {
        return $this->increasedStunDuration;
    }

    public function setIncreasedStunDuration($increasedStunDuration)
    {
        $this->increasedStunDuration = $increasedStunDuration;

        return $this;
    }

    public function getAveragePhysicalDamage()
    {
        return $this->averagePhysicalDamage;
    }

    public function setAveragePhysicalDamage($averagePhysicalDamage)
    {
        $this->averagePhysicalDamage = $averagePhysicalDamage;

        return $this;
    }

    public function getAverageFireDamage()
    {
        return $this->averageFireDamage;
    }

    public function setAverageFireDamage($averageFireDamage)
    {
        $this->averageFireDamage = $averageFireDamage;

        return $this;
    }

    public function getAverageColdDamage()
    {
        return $this->averageColdDamage;
    }

    public function setAverageColdDamage($averageColdDamage)
    {
        $this->averageColdDamage = $averageColdDamage;

        return $this;
    }

    public function getAverageLightningDamage()
    {
        return $this->averageLightningDamage;
    }

    public function setAverageLightningDamage($averageLightningDamage)
    {
        $this->averageLightningDamage = $averageLightningDamage;

        return $this;
    }

    public function getDps()
    {
        return $this->dps;
    }

    public function setDps($dps)
    {
        $this->dps = $dps;

        return $this;
    }

    public function getMapLvl()
    {
        return $this->mapLvl;
    }

    public function setMapLvl($mapLvl)
    {
        $this->mapLvl = $mapLvl;

        return $this;
    }

    public function getMinFireDamage()
    {
        return $this->minFireDamage;
    }

    public function setMinFireDamage($minFireDamage)
    {
        $this->minFireDamage = $minFireDamage;

        return $this;
    }

    public function getMaxFireDamage()
    {
        return $this->maxFireDamage;
    }

    public function setMaxFireDamage($maxFireDamage)
    {
        $this->maxFireDamage = $maxFireDamage;

        return $this;
    }

    public function getMinColdDamage()
    {
        return $this->minColdDamage;
    }

    public function setMinColdDamage($minColdDamage)
    {
        $this->minColdDamage = $minColdDamage;

        return $this;
    }

    public function getMaxColdDamage()
    {
        return $this->maxColdDamage;
    }

    public function setMaxColdDamage($maxColdDamage)
    {
        $this->maxColdDamage = $maxColdDamage;

        return $this;
    }

    public function getMinLightningDamage()
    {
        return $this->minLightningDamage;
    }

    public function setMinLightningDamage($minLightningDamage)
    {
        $this->minLightningDamage = $minLightningDamage;

        return $this;
    }

    public function getMaxLightningDamage()
    {
        return $this->maxLightningDamage;
    }

    public function setMaxLightningDamage($maxLightningDamage)
    {
        $this->maxLightningDamage = $maxLightningDamage;

        return $this;
    }

    public function getAttacksPerSecond()
    {
        return $this->attacksPerSecond;
    }

    public function setAttacksPerSecond($attacksPerSecond)
    {
        $this->attacksPerSecond = $attacksPerSecond;

        return $this;
    }

    public function getArmour()
    {
        return $this->armour;
    }

    public function setArmour($armour)
    {
        $this->armour = $armour;

        return $this;
    }

    public function getEvasionRating()
    {
        return $this->evasionRating;
    }

    public function setEvasionRating($evasionRating)
    {
        $this->evasionRating = $evasionRating;

        return $this;
    }

    public function getEnergyShield()
    {
        return $this->energyShield;
    }

    public function setEnergyShield($energyShield)
    {
        $this->energyShield = $energyShield;

        return $this;
    }

    public function getCriticalStrikeChance()
    {
        return $this->criticalStrikeChance;
    }

    public function setCriticalStrikeChance($criticalStrikeChance)
    {
        $this->criticalStrikeChance = $criticalStrikeChance;

        return $this;
    }

    public function getThreadId()
    {
        return $this->threadId;
    }

    public function setThreadId($threadId)
    {
        $this->threadId = $threadId;

        return $this;
    }

    public function getQuality()
    {
        return $this->quality;
    }

    public function setQuality($quality)
    {
        $this->quality = $quality;

        return $this;
    }

    public function getAccountName()
    {
        return $this->accountName;
    }

    public function setAccountName($accountName)
    {
        $this->accountName = $accountName;

        return $this;
    }

    public function getMinPhysicalDamage()
    {
        return $this->minPhysicalDamage;
    }

    public function setMinPhysicalDamage($minPhysicalDamage)
    {
        $this->minPhysicalDamage = $minPhysicalDamage;

        return $this;
    }

    public function getMaxPhysicalDamage()
    {
        return $this->maxPhysicalDamage;
    }

    public function setMaxPhysicalDamage($maxPhysicalDamage)
    {
        $this->maxPhysicalDamage = $maxPhysicalDamage;

        return $this;
    }

    public function getName()
    {
        return $this->name;
    }

    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    public function getType()
    {
        return $this->type;
    }

    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    public function getVerified()
    {
        return $this->verified;
    }

    public function setVerified($verified)
    {
        $this->verified = $verified;

        return $this;
    }

    public function getLeague()
    {
        return $this->league;
    }

    public function setLeague($league)
    {
        $this->league = $league;

        return $this;
    }

    public function getSockets()
    {
        return $this->sockets;
    }

    public function setSockets($sockets)
    {
        $this->sockets = $sockets;

        return $this;
    }

    public function getIdentified()
    {
        return $this->identified;
    }

    public function setIdentified($identified)
    {
        $this->identified = $identified;

        return $this;
    }

    public function getLvlReq()
    {
        return $this->lvlReq;
    }

    public function setLvlReq($lvlReq)
    {
        $this->lvlReq = $lvlReq;

        return $this;
    }

    public function getIntReq()
    {
        return $this->intReq;
    }

    public function setIntReq($intReq)
    {
        $this->intReq = $intReq;

        return $this;
    }

    public function getDexReq()
    {
        return $this->dexReq;
    }

    public function setDexReq($dexReq)
    {
        $this->dexReq = $dexReq;

        return $this;
    }

    public function getStrReq()
    {
        return $this->strReq;
    }

    public function setStrReq($strReq)
    {
        $this->strReq = $strReq;

        return $this;
    }

    public function getId()
    {
        return $this->id;
    }

    public function getFrameType()
    {
        return $this->frameType;
    }

    public function setFrameType($frameType)
    {
        $this->frameType = $frameType;

        return $this;
    }

    public function __toString()
    {
        return (string) $this->type;
    }

    private function calcAverageDamage($min, $max)
    {
        return ($max - $min) / 2 + $min;
    }
}
