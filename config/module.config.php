<?php
return array(
    'service_manager' => array(
      'invokables' => array(
        'DoctrineHydrationModule\Strategy\ODM\MongoDB\EmbeddedField' => 'Phpro\DoctrineHydrationModule\Hydrator\Strategy\ODM\MongoDB\EmbeddedField',
        'DoctrineHydrationModule\Strategy\ODM\MongoDB\ReferencedField' => 'Phpro\DoctrineHydrationModule\Hydrator\Strategy\ODM\MongoDB\ReferencedField',
      ),
    ),
    'hydrators' => array(
        'abstract_factories' => array(
            'Phpro\DoctrineHydrationModule\Service\DoctrineHydratorFactory',
        ),
    ),
);