<?php

// Newer PHP versions than Kirby has been released for are fine in tests
const KIRBY_PHP_VERSION_CHECK = false;

require __DIR__ . '/../vendor/autoload.php';

\Kirby\Cms\App::$enableWhoops = false;

// Register the plugin once, the test cases create their own Kirby instances
require_once __DIR__ . '/../index.php';
