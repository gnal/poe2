<?php

namespace Poe\CoreBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\CssSelector\CssSelector;

use Poe\CoreBundle\Entity\Item;

class CrawlCommand extends ContainerAwareCommand
{
    private $parentTypes = [
        'Shield' => 'Chance to Block',
        '1h Sword' => 'One Handed Sword',
        '2h Sword' => 'Two Handed Sword',
        '1h Axe' => 'One Handed Axe',
        '2h Axe' => 'Two Handed Axe',
        '1h Mace' => 'One Handed Mace',
        '2h Mace' => 'Two Handed Mace',
        'Bow' => 'Bow',
        'Wand' => 'Wand',
        'Dagger' => 'Dagger',
        'Staff' => 'Staff',
        'Claw' => 'Claw',
    ];

    private $bootsKeywords = [
        'Boots',
        'Greaves',
        'Slippers',
        'Shoes',
    ];

    private $gloveKeywords = [
        'Gloves',
        'Gauntlets',
        'Mitts',
    ];

    private $helmetKeywords = [
        'Casque',
        'Hood',
        'Coif',
        'Helmet',
        'Mask',
        'Circlet',
        'Cap',
    ];

    private $beltKeywords = [
        'Belt',
    ];

    protected function configure()
    {
        $this
            ->setName('poe:core:crawl')
            ->setDefinition(array(
                new InputArgument('page', InputArgument::OPTIONAL, 'The forum page'),
            ))
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->getContainer()->get('msi_cmf.translatable_listener')->setSkipPostLoad(true);
        $this->itemManager = $this->getContainer()->get('poe_core.item_manager');
        $this->itemTypeManager = $this->getContainer()->get('poe_core.item_type_manager');
        $page = $input->getArgument('page');
        $this->output = $output;

        $this->process($page);

        $this->output->writeln("<comment>Done!</comment>");
    }

    protected function process($page = 1)
    {
        // for ($page=2; $page < 3; $page++) {
            $hrefs = $this->getForumThreads(306, $page);

            $this->output->writeln('<comment>CRAWLING</comment> forum 306 page '.$page);

            foreach ($hrefs as $href) {
                $data = $this->getThreadData($href);

                if (!$data) {
                    continue;
                }

                preg_match('@([0-9]+)$@', $href, $matches);

                $threadId = $matches[1];

                // we delete all items with this thread id so we dont have duplicates if we happen to crawl same threads
                $dql = 'delete from '.$this->itemManager->getClass().' i where i.threadId = '.$threadId;
                $numDeleted = $this->itemManager->getEntityManager()->createQuery($dql)->execute();
                $this->output->writeln("<error>DELETED</error> ".$numDeleted." for thread #".$threadId);

                $i = 1;
                foreach ($data as $v) {
                    $row = $v[1];

                    if (
                        !$row['verified'] ||
                        $row['frameType'] == 5 ||
                        $row['frameType'] == 4
                    ) {
                        continue;
                    }

                    $item = $this->itemManager->create();

                    $item
                        ->setVerified($row['verified'])
                        ->setFrameType($row['frameType'])
                        ->setSockets('dada')
                        ->setIdentified($row['identified'])
                        ->setAccountName($this->crawler->filter('a.profile-link.post_by_account')->text())
                        ->setThreadId($threadId)
                        // ->setEditedAt($this->crawler->filter('div.last_edited_by')->text())
                    ;

                    // league
                    if ($row['league'] == 'Default') {
                        $item->setLeague(Item::LEAGUE_DEFAULT);
                    } elseif ($row['league'] == 'Hardcore') {
                        $item->setLeague(Item::LEAGUE_HARDCORE);
                    }

                    // type
                    $type = $this->findType($row);
                    if (!$type->getId()) {
                        $this->itemTypeManager->update($type);
                    }

                    // requirements
                    if (isset($row['requirements'])) {
                        foreach ($row['requirements'] as $requirement) {
                            if ($requirement['name'] === 'Level') {
                                $item->setLvlReq($requirement['values'][0][0]);
                            }
                            if ($requirement['name'] === 'Dex') {
                                $item->setDexReq($requirement['values'][0][0]);
                            }
                            if ($requirement['name'] === 'Str') {
                                $item->setStrReq($requirement['values'][0][0]);
                            }
                            if ($requirement['name'] === 'Int') {
                                $item->setIntReq($requirement['values'][0][0]);
                            }
                        }
                    }

                    // mods
                    if (isset($row['explicitMods'])) {
                        foreach ($row['explicitMods'] as $mod) {
                            if (preg_match('@([0-9]+)% increased Physical Damage@', $mod, $matches)) {
                                $item->setIncreasedPhysicalDamage($matches[1]);
                            }
                            if (preg_match('@([0-9]+)% increased Stun Duration on enemies@', $mod, $matches)) {
                                $item->setIncreasedStunDuration($matches[1]);
                            }
                            if (preg_match('@([0-9]+) to Intelligence@', $mod, $matches)) {
                                $item->setIntelligence($matches[1]);
                            }
                            if (preg_match('@([0-9]+) to Dexterity@', $mod, $matches)) {
                                $item->setDexterity($matches[1]);
                            }
                            if (preg_match('@([0-9]+) to Strength@', $mod, $matches)) {
                                $item->setStrength($matches[1]);
                            }
                            if (preg_match('@([0-9]+)% increased Attack Speed@', $mod, $matches)) {
                                $item->setIncreasedAttackSpeed($matches[1]);
                            }
                            if (preg_match('@([0-9]+)% increased Cast Speed@', $mod, $matches)) {
                                $item->setIncreasedCastSpeed($matches[1]);
                            }
                            if (preg_match('@([0-9]+) Mana Gained when you Kill an enemy@', $mod, $matches)) {
                                $item->setManaOnKill($matches[1]);
                            }
                            if (preg_match('@([0-9]+) Life Gained when you Kill an enemy@', $mod, $matches)) {
                                $item->setLifeOnKill($matches[1]);
                            }
                            if (preg_match('@([0-9]+)% increased Elemental Damage with Weapons@', $mod, $matches)) {
                                $item->setIncreasedElementalDamageWeapons($matches[1]);
                            }
                            if (preg_match('@([0-9]+) to Accuracy Rating@', $mod, $matches)) {
                                $item->setAccuracyRating($matches[1]);
                            }
                            if (preg_match('@([0-9]+)% of Physical Attack Damage Leeched back as Life@', $mod, $matches)) {
                                $item->setLifeLeech($matches[1]);
                            }
                            if (preg_match('@([0-9]+)% of Physical Attack Damage Leeched back as Mana@', $mod, $matches)) {
                                $item->setManaLeech($matches[1]);
                            }
                            if (preg_match('@([0-9]+)% to Chaos Resist@', $mod, $matches)) {
                                $item->setChaosResist($matches[1]);
                            }
                            if (preg_match('@([0-9]+)% to Cold Resistance@', $mod, $matches)) {
                                $item->setColdResist($matches[1]);
                            }
                            if (preg_match('@([0-9]+)% to Lightning Resistance@', $mod, $matches)) {
                                $item->setLightningResist($matches[1]);
                            }
                            if (preg_match('@([0-9]+)% to Fire Resistance@', $mod, $matches)) {
                                $item->setFireResist($matches[1]);
                            }
                            if (preg_match('@([0-9]+)% reduced Enemy Stun Threshold@', $mod, $matches)) {
                                $item->setReducedStunThreshold($matches[1]);
                            }
                            if (preg_match('@([0-9]+) Life gained for each enemy hit by your Attacks@', $mod, $matches)) {
                                $item->setLifeOnHit($matches[1]);
                            }
                            if (preg_match('@([0-9]+) to maximum Energy Shield@', $mod, $matches)) {
                                $item->setMaxEnergyShield($matches[1]);
                            }
                        }
                    }

                    // properties
                    if (isset($row['properties'])) {
                        foreach ($row['properties'] as $property) {
                            if ($property['name'] === 'Physical Damage') {
                                $pieces = explode('-', $property['values'][0][0]);
                                $min = $pieces[0];
                                $max = $pieces[1];
                                $item
                                    ->setMinPhysicalDamage($min)
                                    ->setMaxPhysicalDamage($max)
                                ;
                            }
                            if ($property['name'] === 'Elemental Damage') {
                                foreach ($property['values'] as $value) {
                                    if ($value[1] == 4) {
                                        $pieces = explode('-', $value[0]);
                                        $min = $pieces[0];
                                        $max = $pieces[1];
                                        $item
                                            ->setMinFireDamage($min)
                                            ->setMaxFireDamage($max)
                                        ;
                                    }
                                    if ($value[1] == 5) {
                                        $pieces = explode('-', $value[0]);
                                        $min = $pieces[0];
                                        $max = $pieces[1];
                                        $item
                                            ->setMinColdDamage($min)
                                            ->setMaxColdDamage($max)
                                        ;
                                    }
                                    if ($value[1] == 6) {
                                        $pieces = explode('-', $value[0]);
                                        $min = $pieces[0];
                                        $max = $pieces[1];
                                        $item
                                            ->setMinLightningDamage($min)
                                            ->setMaxLightningDamage($max)
                                        ;
                                    }
                                }
                            }
                            if ($property['name'] === 'Quality') {
                                $item->setQuality(str_replace('%', '', $property['values'][0][0]));
                            }
                            if ($property['name'] === 'Armour') {
                                $item->setArmour($property['values'][0][0]);
                            }
                            if ($property['name'] === 'Evasion Rating') {
                                $item->setEvasionRating($property['values'][0][0]);
                            }
                            if ($property['name'] === 'Energy Shield') {
                                $item->setEnergyShield($property['values'][0][0]);
                            }
                            if ($property['name'] === 'Attacks per Second') {
                                $item->setAttacksPerSecond($property['values'][0][0]);
                            }
                            if ($property['name'] === 'Critical Strike Chance') {
                                $item->setCriticalStrikeChance($property['values'][0][0]);
                            }
                            if ($property['name'] === 'Map Level') {
                                $item->setMapLvl($property['values'][0][0]);
                            }
                        }
                    }

                    $item->setType($type);

                    $item->setName($row['name'] ?: $type->getName());

                    if ($dps = $item->calcDps()) {
                        $item->setDps($dps);
                    }

                    if ($value = $item->calcAveragePhysicalDamage()) {
                        $item->setAveragePhysicalDamage($value);
                    }

                    if ($value = $item->calcAverageFireDamage()) {
                        $item->setAverageFireDamage($value);
                    }

                    if ($value = $item->calcAverageColdDamage()) {
                        $item->setAverageColdDamage($value);
                    }

                    if ($value = $item->calcAverageLightningDamage()) {
                        $item->setAverageLightningDamage($value);
                    }

                    if ($value = $item->calcAverageElementalDamage()) {
                        $item->setAverageElementalDamage($value);
                    }

                    $this->itemManager->updateBatch($item, $i);

                    $label = $row['name'] ?: $row['typeLine'];
                    $this->output->writeln("<info>ADD</info> ".$label);
                    $i++;
                }
                $this->itemManager->getEntityManager()->flush();
            }
        // }
    }

    private function findType($row)
    {
        $type = $this->itemTypeManager->getFindByQueryBuilder(['a.name' => $row['typeLine']])->getQuery()->getOneOrNullResult();
        if (!$type) {
            $type = $this->itemTypeManager->create();
            $type->setName($row['typeLine']);
        }

        if ($type->getParent()) {
            return $type;
        }

        if (preg_match('#Ring#', $row['typeLine'])) {
            $parentType = $this->findOrCreateParentType('Ring');
            $type->setParent($parentType);

            return $type;
        }

        if (preg_match('#Amulet#', $row['typeLine'])) {
            $parentType = $this->findOrCreateParentType('Amulet');
            $type->setParent($parentType);

            return $type;
        }

        foreach ($this->bootsKeywords as $keyword) {
            if (preg_match('#'.$keyword.'#', $row['typeLine'])) {
                $parentType = $this->findOrCreateParentType('Boots');
                $type->setParent($parentType);

                return $type;
            }
        }

        foreach ($this->helmetKeywords as $keyword) {
            if (preg_match('#'.$keyword.'#', $row['typeLine'])) {
                $parentType = $this->findOrCreateParentType('Helmet');
                $type->setParent($parentType);

                return $type;
            }
        }

        foreach ($this->gloveKeywords as $keyword) {
            if (preg_match('#'.$keyword.'#', $row['typeLine'])) {
                $parentType = $this->findOrCreateParentType('Glove');
                $type->setParent($parentType);

                return $type;
            }
        }

        foreach ($this->beltKeywords as $keyword) {
            if (preg_match('#'.$keyword.'#', $row['typeLine'])) {
                $parentType = $this->findOrCreateParentType('Belt');
                $type->setParent($parentType);

                return $type;
            }
        }

        if (preg_match('#Flask#', $row['typeLine'])) {
            $parentType = $this->findOrCreateParentType('Flask');
            $type->setParent($parentType);

            return $type;
        }

        if (preg_match('#Quiver#', $row['typeLine'])) {
            $parentType = $this->findOrCreateParentType('Quiver');
            $type->setParent($parentType);

            return $type;
        }

        if (isset($row['properties'])) {
            foreach ($row['properties'] as $property) {
                if (in_array($property['name'], $this->parentTypes)) {
                    $parentType = $this->findOrCreateParentType(array_search($property['name'], $this->parentTypes));
                    $type->setParent($parentType);

                    return $type;
                }
                if (in_array($property['name'], ['Armour', 'Evasion Rating', 'Energy Shield'])) {
                    if ($row['w'] == 2 && $row['h'] == 3) {
                        $parentType = $this->findOrCreateParentType('Chest Armor');
                        $type->setParent($parentType);

                        return $type;
                    }
                }
                if ($property['name'] === 'Level') {
                    $parentType = $this->findOrCreateParentType('Gem');
                    $type->setParent($parentType);

                    return $type;
                }
                if ($property['name'] === 'Map Level') {
                    $parentType = $this->findOrCreateParentType('Map');
                    $type->setParent($parentType);

                    return $type;
                }
            }
        }

        return $type;
    }

    private function findOrCreateParentType($name)
    {
        $parentType = $this->itemTypeManager->getFindByQueryBuilder(['a.name' => $name])->getQuery()->getOneOrNullResult();
        if (!$parentType) {
            $parentType = $this->itemTypeManager->create();
            $parentType->setName($name);
            $this->itemTypeManager->update($parentType);
        }

        return $parentType;
    }

    private function getForumThreads($forum, $page)
    {
        $url = 'http://www.pathofexile.com/forum/view-forum/'.$forum.'/page/'.$page;
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $html = curl_exec($ch);
        curl_close($ch);

        $this->crawler = new Crawler($html);

        $hrefs = [];
        foreach ($this->crawler->filter('.thread .thread_title .title a')->extract(['href']) as $href) {
            $hrefs[] = $href;
        }

        return $hrefs;
    }

    public function getThreadData($href)
    {
        $url = 'http://www.pathofexile.com'.$href;
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $html = curl_exec($ch);
        curl_close($ch);

        $this->output->writeln('<comment>CRAWL</comment> '.$url);

        $this->crawler = new Crawler($html);
        $i=1;
        foreach ($this->crawler->filter('body')->children() as $element) {
            if ($i == count($this->crawler->filter('body')->children())) {
                $foo = substr(substr(trim($element->nodeValue), 122), 0, -34);
            }
            $i++;
        }
        $this->output->writeln(strlen($foo));
        $data = json_decode(utf8_encode($foo), true);

        return $data;

    }
}
