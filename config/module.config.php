<?php
return array(
    'service_manager' => array(
      'invokables' => array(
        'DoctrineHydrationModule\Strategy\ODM\MongoDB\PersistentCollection' => 'Phpro\DoctrineHydrationModule\Hydrator\Strategy\ODM\MongoDB\PersistentCollection',
        'DoctrineHydrationModule\Strategy\ODM\MongoDB\ReferencedCollection' => 'Phpro\DoctrineHydrationModule\Hydrator\Strategy\ODM\MongoDB\ReferencedCollection',
      ),
    ),
    'hydrators' => array(
        'abstract_factories' => array(
            'Phpro\DoctrineHydrationModule\Service\DoctrineHydratorFactory',
        ),
    ),
);