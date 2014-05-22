<?php
/**
 * @license   http://opensource.org/licenses/BSD-3-Clause BSD-3-Clause
 * @copyright Copyright (c) 2013 Zend Technologies USA Inc. (http://www.zend.com)
 */

namespace Phpro\DoctrineHydrationModule;
use Zend\ModuleManager\Feature\AutoloaderProviderInterface;
use Zend\ModuleManager\Feature\ConfigProviderInterface;


class Module
    implements AutoloaderProviderInterface, ConfigProviderInterface
{

    /**
     * @return array
     */
    public function getAutoloaderConfig()
    {
        return array(
            'Zend\Loader\StandardAutoloader' => array(
                'namespaces' => array(
                    __NAMESPACE__ => __DIR__,
                ),
            ),
        );
    }

    /**
     * @return array|mixed|\Traversable
     */
    public function getConfig()
    {
        return include __DIR__ . '/../config/module.config.php';
    }
}
