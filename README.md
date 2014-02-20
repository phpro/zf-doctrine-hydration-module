# Doctrine Hydration Module
This module provides a configurable way to create new doctrine hydrators.
By using the configurable API, it is easy to create a custom hydrator for any use case you want.

#Installation

## Add to composer.json
```
"phpro/doctrine-hydration-module": "dev-master"
```

## Add to application config
```php
return array(
    'modules' => array(
        'Phpro\\DoctrineHydrationModule',
        // other libs...
    ),
    // Other config
);
```

### Hydrator configuration
```php
return array(
    'doctrine-hydrator' => array(
        'hydrator-manager-key' => array(
            'entity_class' => 'App\Entity\EntityClass',
            'object_manager' => 'object manager key in the service manager',
            'by_value' => true,
            'use_generated_hydrator' => true,
            'strategies' => [
                'fieldname' => 'strategy key in service manager',
            ],
        ),
    ),
);
```

From here on, you can get the hydrator by calling `get('hydrator-manager-key')` on the HydratorManager.