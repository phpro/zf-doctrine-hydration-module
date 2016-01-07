<?php

namespace Phpro\DoctrineHydrationModule\Service;

use Doctrine\Common\Persistence\ObjectManager;
use DoctrineModule\Persistence\ObjectManagerAwareInterface;
use DoctrineModule\Stdlib\Hydrator;
use Phpro\DoctrineHydrationModule\Hydrator\DoctrineHydrator;
use Phpro\DoctrineHydrationModule\Hydrator\ODM\MongoDB;
use Zend\ServiceManager\AbstractFactoryInterface;
use Zend\ServiceManager\Exception\ServiceNotCreatedException;
use Zend\ServiceManager\Exception\ServiceNotFoundException;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\Stdlib\Exception\InvalidCallbackException;
use Zend\Hydrator\AbstractHydrator;
use Zend\Hydrator\Filter\FilterComposite;
use Zend\Hydrator\Filter\FilterInterface;
use Zend\Hydrator\FilterEnabledInterface;
use Zend\Hydrator\HydratorInterface;
use Zend\Hydrator\Strategy\StrategyInterface;
use Zend\Hydrator\StrategyEnabledInterface;
use Zend\Hydrator\NamingStrategy\NamingStrategyInterface;
use Zend\Hydrator\NamingStrategyEnabledInterface;

/**
 * Class DoctrineHydratorFactory.
 */
class DoctrineHydratorFactory implements AbstractFactoryInterface
{
    const FACTORY_NAMESPACE = 'doctrine-hydrator';

    const OBJECT_MANAGER_TYPE_ODM_MONGODB = 'ODM/MongoDB';
    const OBJECT_MANAGER_TYPE_ORM = 'ORM';

    /**
     * Cache of canCreateServiceWithName lookups.
     *
     * @var array
     */
    protected $lookupCache = array();

    /**
     * Determine if we can create a service with name.
     *
     * @param ServiceLocatorInterface $hydratorManager
     * @param                         $name
     * @param                         $requestedName
     *
     * @return bool
     *
     * @throws \Zend\ServiceManager\Exception\ServiceNotFoundException
     */
    public function canCreateServiceWithName(ServiceLocatorInterface $hydratorManager, $name, $requestedName)
    {
        if (array_key_exists($requestedName, $this->lookupCache)) {
            return $this->lookupCache[$requestedName];
        }

        $serviceManager = $hydratorManager->getServiceLocator();

        if (!$serviceManager->has('Config')) {
            return false;
        }

        // Validate object is set
        $config = $serviceManager->get('Config');
        $namespace = self::FACTORY_NAMESPACE;
        if (!isset($config[$namespace]) || !is_array($config[$namespace]) || !isset($config[$namespace][$requestedName])) {
            $this->lookupCache[$requestedName] = false;

            return false;
        }

        // Validate object manager
        $config = $config[$namespace];
        if (!isset($config[$requestedName]) || !isset($config[$requestedName]['object_manager'])) {
            throw new ServiceNotFoundException(sprintf(
                '%s requires that a valid "object_manager" is specified for hydrator %s; no service found',
                __METHOD__,
                $requestedName
            ));
        }

        // Validate object class
        if (!isset($config[$requestedName]['entity_class'])) {
            throw new ServiceNotFoundException(sprintf(
                '%s requires that a valid "entity_class" is specified for hydrator %s; no service found',
                __METHOD__,
                $requestedName
            ));
        }

        $this->lookupCache[$requestedName] = true;

        return true;
    }

    /**
     * @param ServiceLocatorInterface $hydratorManager
     * @param                         $name
     * @param                         $requestedName
     *
     * @return DoctrineHydrator
     */
    public function createServiceWithName(ServiceLocatorInterface $hydratorManager, $name, $requestedName)
    {
        $serviceManager = $hydratorManager->getServiceLocator();

        $config = $serviceManager->get('Config');
        $config = $config[self::FACTORY_NAMESPACE][$requestedName];

        $objectManager = $this->loadObjectManager($serviceManager, $config);

        $extractService = null;
        $hydrateService = null;

        $useEntityHydrator = (array_key_exists('use_generated_hydrator', $config) && $config['use_generated_hydrator']);
        $useCustomHydrator = (array_key_exists('hydrator', $config));

        if ($useEntityHydrator) {
            $hydrateService = $this->loadEntityHydrator($serviceManager, $config, $objectManager);
        }

        if ($useCustomHydrator) {
            $extractService = $hydratorManager->get($config['hydrator']);
            $hydrateService = $extractService;
        }

        # Use DoctrineModuleHydrator by default
        if (!isset($extractService, $hydrateService)) {
            $doctrineModuleHydrator = $this->loadDoctrineModuleHydrator($serviceManager, $config, $objectManager);
            $extractService = ($extractService ?: $doctrineModuleHydrator);
            $hydrateService = ($hydrateService ?: $doctrineModuleHydrator);
        }

        $this->configureHydrator($extractService, $serviceManager, $config, $objectManager);
        $this->configureHydrator($hydrateService, $serviceManager, $config, $objectManager);

        $hydrator = new DoctrineHydrator($extractService, $hydrateService);

        return $hydrator;
    }

    /**
     * @param $objectManager
     *
     * @return string
     *
     * @throws \Zend\ServiceManager\Exception\ServiceNotCreatedException
     */
    protected function getObjectManagerType($objectManager)
    {
        if (class_exists('\\Doctrine\\ODM\\MongoDB\\DocumentManager')
            && $objectManager instanceof \Doctrine\ODM\MongoDB\DocumentManager) {
            return self::OBJECT_MANAGER_TYPE_ODM_MONGODB;
        } elseif (class_exists('\\Doctrine\\ORM\\EntityManager')
            && $objectManager instanceof \Doctrine\ORM\EntityManager) {
            return self::OBJECT_MANAGER_TYPE_ORM;
        }

        throw new ServiceNotCreatedException('Unknown object manager type: '.get_class($objectManager));
    }

    /**
     * @param ServiceLocatorInterface $serviceManager
     * @param                         $config
     *
     * @return ObjectManager
     *
     * @throws \Zend\ServiceManager\Exception\ServiceNotCreatedException
     */
    protected function loadObjectManager(ServiceLocatorInterface $serviceManager, $config)
    {
        if (!$serviceManager->has($config['object_manager'])) {
            throw new ServiceNotCreatedException('The object_manager could not be found.');
        }

        $objectManager = $serviceManager->get($config['object_manager']);

        return $objectManager;
    }

    /**
     * @param ServiceLocatorInterface $serviceManager
     * @param                         $config
     * @param                         $objectManager
     *
     * @return null|HydratorInterface
     */
    protected function loadEntityHydrator(ServiceLocatorInterface $serviceManager, $config, $objectManager)
    {
        $objectManagerType = $this->getObjectManagerType($objectManager);
        if ($objectManagerType != self::OBJECT_MANAGER_TYPE_ODM_MONGODB) {
            return;
        }

        $hydratorFactory = $objectManager->getHydratorFactory();
        $hydrator = $hydratorFactory->getHydratorFor($config['entity_class']);

        return $hydrator;
    }

    /**
     * @param ServiceLocatorInterface $serviceManager
     * @param                         $config
     * @param ObjectManager           $objectManager
     *
     * @return HydratorInterface
     */
    protected function loadDoctrineModuleHydrator(ServiceLocatorInterface $serviceManager, $config, $objectManager)
    {
        $objectManagerType = $this->getObjectManagerType($objectManager);

        if ($objectManagerType == self::OBJECT_MANAGER_TYPE_ODM_MONGODB) {
            $hydrator = new MongoDB\DoctrineObject($objectManager, $config['by_value']);
        } else {
            $hydrator = new Hydrator\DoctrineObject($objectManager, $config['by_value']);
        }

        return $hydrator;
    }

    /**
     * @param                         $hydrator
     * @param ServiceLocatorInterface $serviceManager
     * @param                         $config
     * @param                         $objectManager
     *
     * @throws \Zend\ServiceManager\Exception\ServiceNotCreatedException
     */
    public function configureHydrator($hydrator, ServiceLocatorInterface $serviceManager, $config, $objectManager)
    {
        $this->configureHydratorFilters($hydrator, $serviceManager, $config, $objectManager);
        $this->configureHydratorStrategies($hydrator, $serviceManager, $config, $objectManager);
        $this->configureHydratorNamingStrategy($hydrator, $serviceManager, $config, $objectManager);
    }

    /**
     * @param                         $hydrator
     * @param ServiceLocatorInterface $serviceManager
     * @param                         $config
     * @param                         $objectManager
     *
     * @throws \Zend\ServiceManager\Exception\ServiceNotCreatedException
     */
    public function configureHydratorNamingStrategy($hydrator, ServiceLocatorInterface $serviceManager, $config, $objectManager)
    {
        if (!($hydrator instanceof NamingStrategyEnabledInterface) || !isset($config['naming_strategy'])) {
            return;
        }

        $namingStrategyKey = $config['naming_strategy'];
        if (!$serviceManager->has($namingStrategyKey)) {
            throw new ServiceNotCreatedException(sprintf('Invalid naming strategy %s.', $namingStrategyKey));
        }

        $namingStrategy = $serviceManager->get($namingStrategyKey);
        if (!$namingStrategy instanceof NamingStrategyInterface) {
            throw new ServiceNotCreatedException(sprintf('Invalid naming strategy class %s', get_class($namingStrategy)));
        }

        // Attach object manager:
        if ($namingStrategy instanceof ObjectManagerAwareInterface) {
            $namingStrategy->setObjectManager($objectManager);
        }

        $hydrator->setNamingStrategy($namingStrategy);
    }

    /**
     * @param                         $hydrator
     * @param ServiceLocatorInterface $serviceManager
     * @param                         $config
     * @param                         $objectManager
     *
     * @throws \Zend\ServiceManager\Exception\ServiceNotCreatedException
     */
    protected function configureHydratorStrategies($hydrator, ServiceLocatorInterface $serviceManager, $config, $objectManager)
    {
        if (!($hydrator instanceof StrategyEnabledInterface) || !isset($config['strategies']) || !is_array($config['strategies'])) {
            return;
        }

        foreach ($config['strategies'] as $field => $strategyKey) {
            if (!$serviceManager->has($strategyKey)) {
                throw new ServiceNotCreatedException(sprintf('Invalid strategy %s for field %s', $strategyKey, $field));
            }

            $strategy = $serviceManager->get($strategyKey);
            if (!$strategy instanceof StrategyInterface) {
                throw new ServiceNotCreatedException(sprintf('Invalid strategy class %s for field %s', get_class($strategy), $field));
            }

            // Attach object manager:
            if ($strategy instanceof ObjectManagerAwareInterface) {
                $strategy->setObjectManager($objectManager);
            }

            $hydrator->addStrategy($field, $strategy);
        }
    }

    /**
     * Add filters to the Hydrator based on a predefined configuration format, if specified.
     *
     * @param AbstractHydrator        $hydrator
     * @param ServiceLocatorInterface $serviceManager
     * @param                         $config
     * @param                         $objectManager
     */
    protected function configureHydratorFilters($hydrator, $serviceManager, $config, $objectManager)
    {
        if (!($hydrator instanceof FilterEnabledInterface) || !isset($config['filters']) || !is_array($config['filters'])) {
            return;
        }

        foreach ($config['filters'] as $name => $filterConfig) {
            $conditionMap = array(
                'and' => FilterComposite::CONDITION_AND,
                'or' => FilterComposite::CONDITION_OR,
            );
            $condition = isset($filterConfig['condition']) ?
                            $conditionMap[$filterConfig['condition']] :
                            FilterComposite::CONDITION_OR;

            $filterService = $filterConfig['filter'];
            if (!$serviceManager->has($filterService)) {
                throw new ServiceNotCreatedException(
                    sprintf('Invalid filter %s for field %s: service does not exist', $filterService, $name)
                );
            }

            $filterService = $serviceManager->get($filterService);
            if (!$filterService instanceof FilterInterface) {
                throw new InvalidCallbackException(
                    sprintf('Filter service %s must implement FilterInterface'), get_class($filterService)
                );
            }

            if ($filterService instanceof ObjectManagerAwareInterface) {
                $filterService->setObjectManager($objectManager);
            }
            $hydrator->addFilter($name, $filterService, $condition);
        }
    }
}
