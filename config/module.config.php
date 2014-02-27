<?php
return array(
    'service_manager' => array(
      'invokables' => array(
        'DoctrineHydrationModule\Strategy\ODM\MongoDB\EmbeddedCollection' => 'Phpro\DoctrineHydrationModule\Hydrator\ODM\MongoDB\Strategy\EmbeddedCollection',
        'DoctrineHydrationModule\Strategy\ODM\MongoDB\EmbeddedField' => 'Phpro\DoctrineHydrationModule\Hydrator\ODM\MongoDB\Strategy\EmbeddedField',
        'DoctrineHydrationModule\Strategy\ODM\MongoDB\ReferencedCollection' => 'Phpro\DoctrineHydrationModule\Hydrator\ODM\MongoDB\Strategy\ReferencedCollection',
        'DoctrineHydrationModule\Strategy\ODM\MongoDB\ReferencedField' => 'Phpro\DoctrineHydrationModule\Hydrator\ODM\MongoDB\Strategy\ReferencedField',
      ),
    ),
    'hydrators' => array(
        'abstract_factories' => array(
            'Phpro\DoctrineHydrationModule\Service\DoctrineHydratorFactory',
        ),
    ),
);