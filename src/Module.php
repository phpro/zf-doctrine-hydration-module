<?php

namespace Phpro\DoctrineHydrationModule;

class Module
{
    /**
     * @return array
     */
    public function getConfig()
    {
        return include __DIR__.'/../config/module.config.php';
    }
}
