<?php
$finder = Symfony\CS\Finder\DefaultFinder::create()
    ->exclude('vendor')
    ->exclude('config')
    ->in(__DIR__ . '/src')
    ->in(__DIR__ . '/tests')
    ->notName('phpunit.xml')
    ->notName('atlassian-ide-plugin.xml');
$config = Symfony\CS\Config\Config::create();
$config->fixers(Symfony\CS\FixerInterface::PSR2_LEVEL);
$config->finder($finder);
return $config;
