<?php
return array(
    'service_manager' => array(
      'invokables' => array(
        'DoctrineHydrationModule\Strategy\ODM\MongoDB\DateTimeField' => 'Phpro\DoctrineHydrationModule\Hydrator\ODM\MongoDB\Strategy\DateTimeField',
        'DoctrineHydrationModule\Strategy\ODM\MongoDB\DefaultRelation' => 'Phpro\DoctrineHydrationModule\Hydrator\ODM\MongoDB\Strategy\DefaultRelation',
        'DoctrineHydrationModule\Strategy\ODM\MongoDB\EmbeddedCollection' => 'Phpro\DoctrineHydrationModule\Hydrator\ODM\MongoDB\Strategy\EmbeddedCollection',
        'DoctrineHydrationModule\Strategy\ODM\MongoDB\EmbeddedField' => 'Phpro\DoctrineHydrationModule\Hydrator\ODM\MongoDB\Strategy\EmbeddedField',
        'DoctrineHydrationModule\Strategy\ODM\MongoDB\ReferencedCollection' => 'Phpro\DoctrineHydrationModule\Hydrator\ODM\MongoDB\Strategy\ReferencedCollection',
        'DoctrineHydrationModule\Strategy\ODM\MongoDB\ReferencedField' => 'Phpro\DoctrineHydrationModule\Hydrator\ODM\MongoDB\Strategy\ReferencedField',
        'DoctrineHydrationModule\Strategy\ODM\MongoDB\EmbeddedReferenceCollection' => 'Phpro\DoctrineHydrationModule\Hydrator\ODM\MongoDB\Strategy\EmbeddedReferenceCollection',
        'DoctrineHydrationModule\Strategy\ODM\MongoDB\EmbeddedReferenceField' => 'Phpro\DoctrineHydrationModule\Hydrator\ODM\MongoDB\Strategy\EmbeddedReferenceField',
      ),

      'shared' => array(
          'DoctrineHydrationModule\Strategy\ODM\MongoDB\DateTimeField' => false,
          'DoctrineHydrationModule\Strategy\ODM\MongoDB\DefaultRelation' => false,
          'DoctrineHydrationModule\Strategy\ODM\MongoDB\EmbeddedCollection' => false,
          'DoctrineHydrationModule\Strategy\ODM\MongoDB\EmbeddedField' => false,
          'DoctrineHydrationModule\Strategy\ODM\MongoDB\ReferencedCollection' => false,
          'DoctrineHydrationModule\Strategy\ODM\MongoDB\ReferencedField' => false,
          'DoctrineHydrationModule\Strategy\ODM\MongoDB\EmbeddedReferenceCollection' => false,
          'DoctrineHydrationModule\Strategy\ODM\MongoDB\EmbeddedReferenceField' => false,
      ),
    ),
    'hydrators' => array(
        'abstract_factories' => array(
            'Phpro\DoctrineHydrationModule\Service\DoctrineHydratorFactory',
        ),
    ),
);
