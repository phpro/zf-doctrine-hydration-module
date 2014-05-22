<?php
$finder = Symfony\CS\Finder\DefaultFinder::create()
    ->exclude('vendor')
    ->exclude('config')
    ->in(__DIR__ . '/src')
    ->in(__DIR__ . '/test')
    ->notName('phpunit.xml')
    ->notName('atlassian-ide-plugin.xml');
$config = Symfony\CS\Config\Config::create();
$config->fixers(Symfony\CS\FixerInterface::PSR2_LEVEL);

// FIXME: when https://github.com/fabpot/PHP-CS-Fixer/issues/311 is solved
$config->fixers(array('-Psr0Fixer'));

$config->finder($finder);
return $config;
