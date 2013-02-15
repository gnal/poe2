<?php

namespace Poe\CoreBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Validator\Constraints\Type;
use Symfony\Component\Validator\Constraints\MinLength;
use Symfony\Component\Validator\Constraints\Choice;

use Poe\CoreBundle\Entity\Item;

class SearchFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $validPropChoices = [
            'averagePhysicalDamage',
            'averageElementalDamage',
            'criticalStrikeChance',
            'attacksPerSecond',
            'armour',
            'evasionRating',
            'energyShield',
        ];

        $validModChoices = [
            'increasedPhysicalDamage',
            'increasedStunDuration',
            'intelligence',
            'dexterity',
            'strength',
            'increasedAttackSpeed',
            'increasedCastSpeed',
            'manaOnKill',
            'lifeOnKill',
            'increasedElementalDamageWeapons',
            'accuracyRating',
            'lifeLeech',
            'manaLeech',
            'chaosResist',
            'coldResist',
            'lightningResist',
            'fireResist',
            'reducedStunThreshold',
            'lifeOnHit',
            'maxEnergyShield',
        ];

        $prop = [
            'averagePhysicalDamage' => 'Avg Physical Dmg',
            'averageElementalDamage' => 'Avg Elemental Dmg',
            'criticalStrikeChance' => 'Crit Strike Chance',
            'attacksPerSecond' => 'APS',
            'armour' => 'Armour',
            'evasionRating' => 'ER',
            'energyShield' => 'ES',
        ];

        $mod = [
            'increasedPhysicalDamage' => 'Physical Dmg %',
            'increasedStunDuration' => 'Stun Duration',
            'intelligence' => 'Intelligence',
            'dexterity' => 'Dexterity',
            'strength' => 'Strength',
            'increasedAttackSpeed' => 'Attack Speed %',
            'increasedCastSpeed' => 'Cast Speed %',
            'manaOnKill' => 'Mana on Kill',
            'lifeOnKill' => 'Life on Kill',
            'increasedElementalDamageWeapons' => 'Elemental Dmg %',
            'accuracyRating' => 'Accuracy Rating',
            'lifeLeech' => 'Life Leech',
            'manaLeech' => 'Mana Leech',
            'chaosResist' => 'Chaos Resist',
            'coldResist' => 'Cold Resist',
            'lightningResist' => 'Lightning Resist',
            'fireResist' => 'Fire Resist',
            'reducedStunThreshold' => 'Reduced Stun Threshold',
            'lifeOnHit' => 'Life on Hit',
            'maxEnergyShield' => 'Max Energy Shield',
        ];

        foreach (range(20, 1) as $range) {
            $quality[$range] = $range;
        }

        $builder
            ->  add('league', 'choice', [
                'attr' => [
                    'class' => 'all',
                ],
                'choices' => [
                    Item::LEAGUE_DEFAULT => 'Default',
                    Item::LEAGUE_HARDCORE => 'Hardcore',
                ],
            ])
            ->add('type', 'entity', [
                'empty_value' => '',
                'attr' => [
                    'class' => 'all',
                ],
                'class' => 'PoeCoreBundle:ItemType',
                'query_builder' => function(EntityRepository $er) {
                    return $er->createQueryBuilder('a')
                        ->andWhere('a.lvl = 0')
                        ->addOrderBy('a.name', 'ASC')
                    ;
                },
            ])
            ->add('name', 'text', [
                'constraints' => [new MinLength(['limit' => 3])],
                'attr' => [
                    'placeholder' => 'Name',
                ],
            ])
            ->add('frameType', 'choice', [
                'label' => ' ',
                'empty_value' => '',
                'attr' => [
                    'class' => 'all',
                ],
                'choices' => [
                    Item::FRAME_TYPE_NORMAL => 'Normal',
                    Item::FRAME_TYPE_MAGIC => 'Magic',
                    Item::FRAME_TYPE_RARE => 'Rare',
                    Item::FRAME_TYPE_UNIQUE => 'Unique',
                ],
            ])

            ->add('quality', 'choice', [
                'label' => ' ',
                'empty_value' => '',
                'attr' => [
                    'class' => 'all',
                ],
                'choices' => $quality,
            ])

            ->add('prop1', 'choice', [
                'constraints' => [new Choice(['choices' => $validPropChoices])],
                'attr' => [
                    'class' => 'none',
                ],
                'label' => ' ',
                'empty_value' => '',
                'choices' => $prop,
            ])
            ->add('prop1val', 'text', [
                'constraints' => [new Type(['type' => 'numeric'])],
                'attr' => [
                    'style' => 'width: 36px;',
                ],
            ])

            ->add('prop2', 'choice', [
                'constraints' => [new Choice(['choices' => $validPropChoices])],
                'attr' => [
                    'class' => 'none',
                ],
                'label' => ' ',
                'empty_value' => '',
                'choices' => $prop,
            ])
            ->add('prop2val', 'text', [
                'constraints' => [new Type(['type' => 'numeric'])],
                'attr' => [
                    'style' => 'width: 36px;',
                ],
            ])

            ->add('prop3', 'choice', [
                'constraints' => [new Choice(['choices' => $validPropChoices])],
                'attr' => [
                    'class' => 'none',
                ],
                'label' => ' ',
                'empty_value' => '',
                'choices' => $prop,
            ])
            ->add('prop3val', 'text', [
                'constraints' => [new Type(['type' => 'numeric'])],
                'attr' => [
                    'style' => 'width: 36px;',
                ],
            ])

            ->add('prop4', 'choice', [
                'constraints' => [new Choice(['choices' => $validPropChoices])],
                'attr' => [
                    'class' => 'none',
                ],
                'label' => ' ',
                'empty_value' => '',
                'choices' => $prop,
            ])
            ->add('prop4val', 'text', [
                'constraints' => [new Type(['type' => 'numeric'])],
                'attr' => [
                    'style' => 'width: 36px;',
                ],
            ])

            ->add('mod1', 'choice', [
                'constraints' => [new Choice(['choices' => $validModChoices])],
                'attr' => [
                    'class' => 'none',
                ],
                'label' => ' ',
                'empty_value' => '',
                'choices' => $mod,
            ])
            ->add('mod1val', 'text', [
                'constraints' => [new Type(['type' => 'numeric'])],
                'attr' => [
                    'style' => 'width: 36px;',
                ],
            ])
            ->add('mod2', 'choice', [
                'constraints' => [new Choice(['choices' => $validModChoices])],
                'attr' => [
                    'class' => 'none',
                ],
                'label' => ' ',
                'empty_value' => '',
                'choices' => $mod,
            ])
            ->add('mod2val', 'text', [
                'constraints' => [new Type(['type' => 'numeric'])],
                'attr' => [
                    'style' => 'width: 36px;',
                ],
            ])
            ->add('mod3', 'choice', [
                'constraints' => [new Choice(['choices' => $validModChoices])],
                'attr' => [
                    'class' => 'none',
                ],
                'label' => ' ',
                'empty_value' => '',
                'choices' => $mod,
            ])
            ->add('mod3val', 'text', [
                'constraints' => [new Type(['type' => 'numeric'])],
                'attr' => [
                    'style' => 'width: 36px;',
                ],
            ])
            ->add('mod4', 'choice', [
                'constraints' => [new Choice(['choices' => $validModChoices])],
                'attr' => [
                    'class' => 'none',
                ],
                'label' => ' ',
                'empty_value' => '',
                'choices' => $mod,
            ])
            ->add('mod4val', 'text', [
                'constraints' => [new Type(['type' => 'numeric'])],
                'attr' => [
                    'style' => 'width: 36px;',
                ],
            ])
            ->add('mod5', 'choice', [
                'constraints' => [new Choice(['choices' => $validModChoices])],
                'attr' => [
                    'class' => 'none',
                ],
                'label' => ' ',
                'empty_value' => '',
                'choices' => $mod,
            ])
            ->add('mod5val', 'text', [
                'constraints' => [new Type(['type' => 'numeric'])],
                'attr' => [
                    'style' => 'width: 36px;',
                ],
            ])
            ->add('mod6', 'choice', [
                'constraints' => [new Choice(['choices' => $validModChoices])],
                'attr' => [
                    'class' => 'none',
                ],
                'label' => ' ',
                'empty_value' => '',
                'choices' => $mod,
            ])
            ->add('mod6val', 'text', [
                'constraints' => [new Type(['type' => 'numeric'])],
                'attr' => [
                    'style' => 'width: 36px;',
                ],
            ])
        ;
    }

    public function getName()
    {
        return 'poe_search';
    }
}
