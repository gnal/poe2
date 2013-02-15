<?php

namespace Poe\CoreBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

use Poe\CoreBundle\Form\Type\SearchFormType;
use Poe\CoreBundle\Entity\Item;

class SearchController extends Controller
{
    protected $weaponTypes = [
        'Shield',
        '1h Sword',
        '2h Sword',
        '1h Axe',
        '2h Axe',
        '1h Mace',
        '2h Mace',
        'Bow',
        'Wand',
        'Dagger',
        'Staff',
        'Claw',
    ];

    protected $armorTypes = [
        'Ring',
        'Amulet',
        'Boots',
        'Helmet',
        'Glove',
        'Belt',
        'Quiver',
        'Chest Armor',
    ];

    public function searchAction()
    {
        $form = $this->get('form.factory')->create(new SearchFormType());
        $dirty = $this->getRequest()->query->get('poe_search');

        if ($dirty === null) {
            return $this->render('PoeCoreBundle:Item:search.html.twig', ['form' => $form->createView()]);
        }

        $form->bind($this->getRequest());

        if (!$form->isValid()) {
            return $this->render('PoeCoreBundle:Item:search.html.twig', ['form' => $form->createView()]);
        }

        $formData = $form->getData();

        $qb = $this->get('poe_core.item_manager')->getFindByQueryBuilder(
            [
                'a.league' => $formData['league'] !== null ? $formData['league'] : Item::LEAGUE_DEFAULT,
            ],
            [
                'a.type' => 't',
                't.parent' => 'tp',
            ],
            // ['tp.name' => 'ASC', 'a.lvlReq' => 'ASC']
            ['a.dps' => 'DESC']
        );

        for ($i=1; $i < 4; $i++) {
            $prop = 'prop'.$i;
            $propVal = 'prop'.$i.'val';
            if (null !== $formData[$prop]) {
                $qb->andWhere('a.'.$formData[$prop].' >= :'.$formData[$prop].'');
                $qb->setParameter($formData[$prop], $formData[$propVal] ?: 1);
            }
        }

        for ($i=1; $i < 7; $i++) {
            $mod = 'mod'.$i;
            $modVal = 'mod'.$i.'val';
            if (null !== $formData[$mod]) {
                $qb->andWhere('a.'.$formData[$mod].' >= :'.$formData[$mod].'');
                $qb->setParameter($formData[$mod], $formData[$modVal] ?: 1);
            }
        }

        if (null !== $formData['name']) {
            $orX = $qb->expr()->orX();

            $orX->add($qb->expr()->like('a.name', ':likeName'));
            $qb->setParameter('likeName', '%'.$formData['name'].'%');

            $orX->add($qb->expr()->like('t.name', ':likeType'));
            $qb->setParameter('likeType', '%'.$formData['name'].'%');

            $qb->andWhere($orX);
        }

        if (null !== $formData['quality']) {
            $qb->andWhere('a.quality >= :quality')->setParameter('quality', $formData['quality']);
        }

        if (null !== $formData['frameType']) {
            $qb->andWhere('a.frameType = :frameType');
            $qb->setParameter('frameType', $formData['frameType']);
        }

        if (null !== $formData['type']) {
            $type = $this->get('poe_core.item_type_manager')->getOneBy(['a.id' => $formData['type']]);
            $qb->andWhere('t.root = :typeRoot')->setParameter('typeRoot', $type->getRoot());
        }

        if (null !== $formData['minLvlReq']) {
            $qb->andWhere('a.lvlReq >= :minLvlReq');
            $qb->setParameter('minLvlReq', $formData['minLvlReq']);
        }

        if (null !== $formData['maxLvlReq']) {
            $qb->andWhere('a.lvlReq <= :maxLvlReq');
            $qb->setParameter('maxLvlReq', $formData['maxLvlReq']);
        }

        if (null !== $formData['numSockets']) {
            $qb->andWhere('a.numSockets >= :numSockets');
            $qb->setParameter('numSockets', $formData['numSockets']);
        }

        if (null !== $formData['numLinkedSockets']) {
            $qb->andWhere('a.numLinkedSockets >= :numLinkedSockets');
            $qb->setParameter('numLinkedSockets', $formData['numLinkedSockets']);
        }

        $qb->setMaxResults(150);
        // $qb->select(
        //     'a.frameType',
        //     'a.name',
        //     'a.threadId',
        //     'a.dps',
        //     'a.averagePhysicalDamage',
        //     'a.averageFireDamage',
        //     'a.averageColdDamage',
        //     'a.averageLightningDamage',
        //     'a.armour',
        //     'a.evasionRating',
        //     'a.energyShield',
        //     'a.attacksPerSecond',
        //     'a.criticalStrikeChance',
        //     'a.averageElementalDamage'
        // );
        $items = $qb->getQuery()->execute();

        $masterType = '';

        if (isset($items[0]) && in_array($items[0]->getType()->getParent()->getName(), $this->weaponTypes)) {
            $masterType = 'weapon';
        }

        if (isset($items[0]) && in_array($items[0]->getType()->getParent()->getName(), $this->armorTypes)) {
            $masterType = 'armor';
        }

        if ($this->getRequest()->isXmlHttpRequest()) {
            return $this->render('PoeCoreBundle:Item:search_content.html.twig', [
                'form' => $form->createView(),
                'masterType' => $masterType,
                'items' => $items,
            ]);
        }

        return $this->render('PoeCoreBundle:Item:search.html.twig', [
            'form' => $form->createView(),
            'masterType' => $masterType,
            'items' => $items,
        ]);
    }

    public function viewJsonAction()
    {
        $item = $this->get('poe_core.item_manager')->getOneBy(['a.id' => $this->getRequest()->attributes->get('id')]);

        die(print_r(json_decode($item->getJson())));

        return new Response();
    }
}
