<?php
$finder = Symfony\CS\Finder\DefaultFinder::create()
    ->exclude('vendor')
    ->exclude('config')
    ->in(__DIR__ . '/src')
    ->in(__DIR__ . '/test')
    ->name('*.php')
;

$config = Symfony\CS\Config\Config::create();
$config->fixers(Symfony\CS\FixerInterface::PSR2_LEVEL);
$config->finder($finder);

return $config;
