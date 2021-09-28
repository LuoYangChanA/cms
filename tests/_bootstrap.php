<?php

use craft\test\TestSetup;

ini_set('date.timezone', 'UTC');
date_default_timezone_set('UTC');

// Use the current installation of Craft
define('CRAFT_TESTS_PATH', __DIR__);
define('CRAFT_STORAGE_PATH', __DIR__ . DIRECTORY_SEPARATOR . '_craft' . DIRECTORY_SEPARATOR . 'storage');
define('CRAFT_TEMPLATES_PATH', __DIR__ . DIRECTORY_SEPARATOR . '_craft' . DIRECTORY_SEPARATOR . 'templates');
define('CRAFT_CONFIG_PATH', __DIR__ . DIRECTORY_SEPARATOR . '_craft' . DIRECTORY_SEPARATOR . 'config');
define('CRAFT_MIGRATIONS_PATH', __DIR__ . DIRECTORY_SEPARATOR . '_craft' . DIRECTORY_SEPARATOR . 'migrations');
define('CRAFT_TRANSLATIONS_PATH', __DIR__ . DIRECTORY_SEPARATOR . '_craft' . DIRECTORY_SEPARATOR . 'translations');
define('CRAFT_VENDOR_PATH', dirname(__DIR__) . DIRECTORY_SEPARATOR . 'vendor');

$devMode = true;

$compiledTemplates = CRAFT_STORAGE_PATH . DIRECTORY_SEPARATOR . 'runtime' . DIRECTORY_SEPARATOR . 'compiled_classes';
if (is_dir($compiledTemplates)) {
    foreach (new DirectoryIterator($compiledTemplates) as $file) {
        if (!$file->isDot() && $file->getExtension() === 'php') {
            include $compiledTemplates . DIRECTORY_SEPARATOR . $file;
        }
    }
}

TestSetup::configureCraft();
