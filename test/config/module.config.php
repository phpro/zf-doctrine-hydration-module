<?php
return array(
    'doctrine-hydrator' => array(
        'custom-hydrator' => array(
            'entity_class' => 'App\Entity\EntityClass',
            'object_manager' => 'doctrine.default.object-manager',
            'by_value' => true,
            'use_generated_hydrator' => false,
            'naming_strategy' => 'custom.naming_strategy',
            'strategies' => array(
                'fieldname' => 'custom.strategy',
            ),
            'filters' => array(
                'custom.filter.name' => array(
                    'condition' => 'and', //FilterComposite::CONDITION_AND,
                    'filter' => 'custom.filter',
                ),
            ),
        ),
    ),
);
