[![Build status](https://api.travis-ci.org/phpro/zf-doctrine-hydration-module.svg)](http://travis-ci.org/phpro/zf-doctrine-hydration-module)

# Doctrine Hydration Module
This module provides a configurable way to create new doctrine hydrators.
By using the configurable API, it is easy to create a custom hydrator for any use case you want.

For MongoDB ODM, a specific hydrator is added. This hydrator will be able to handle Referenced documents and Embedded Documents.
It is also possible to hydrate advanced documents with discriminator maps.

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

use_generated_hydrator will only be used with mongoDB ODM and will use the generated hydrators instead of the Doctrine Module Hydrator.
Strategies will not work when this option is set to `true`.


From here on, you can get the hydrator by calling `get('hydrator-manager-key')` on the HydratorManager.

#Custom strategies:
## MongoDB ODM
- EmbeddedCollection: Used for embedded collections
- EmbeddedField: Used for embedded fields
- ReferencedCollection: Used for referenced collections
- ReferencedField: Used for referenced fields.
- EmbeddedReferenceCollection: This is a custom strategy that can be used in an API to display all fields in a referenced object. The hydration works as a regular referenced object.
- EmbeddedReferenceField: This is a custom strategy that can be used in an API to display all fields in a referenced object. The hydration works as a regular referenced object.

# Testing
This package is fully tested with Cs fixer and PhpUnit. The MongoDB tests require mongodb to be installed on your machine. You can skip these tests by adding a testsuite to the command:
```sh
# Php coding standards:
./vendor/bin/php-cs-fixer fix . --dry-run

# Phpunit:
./vendor/bin/phpunit -c"tests/phpunit.xml"

# Testing one testsuite:
./vendor/bin/phpunit -c"tests/phpunit.xml" --testsuite="Main"
./vendor/bin/phpunit -c"tests/phpunit.xml" --testsuite="ODM"
```