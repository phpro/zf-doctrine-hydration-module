<?php
return array(
    'doctrine-hydrator' => array(
        'custom-hydrator' => array(
            'entity_class' => 'App\Entity\EntityClass',
            'object_manager' => 'doctrine.default.object-manager',
            'by_value' => true,
            'use_generated_hydrator' => false,
            'strategies' => [
                'fieldname' => 'custom.strategy',
            ],
            'filters' => [
                'custom.filter.name' => [
                    'condition' => 'and', //FilterComposite::CONDITION_AND,
                    'filter' => 'custom.filter',
                ],
            ],
        ),
    ),
);