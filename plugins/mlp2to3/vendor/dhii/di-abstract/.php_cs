<?php

require_once __DIR__.DIRECTORY_SEPARATOR.'vendor/autoload.php';
$config = Dhii\Configuration\PHPCSFixer\Config::create();
$fixers = $config->getFixers();

$toRemove = array();
foreach ($toRemove as $_fixer) {
    if (($removeIndex = array_search($_fixer, $fixers)) === false) {
        continue;
    }

    unset($fixers[$removeIndex]);
}

$toAdd = array();
foreach ($toAdd as $_fixer) {
    if (($removeIndex = array_search($_fixer, $fixers)) !== false) {
        continue;
    }

    $fixers[] = $_fixer;
}

$config->fixers($fixers);
$config->getFinder()->in(__DIR__.DIRECTORY_SEPARATOR.'src');
return $config;
