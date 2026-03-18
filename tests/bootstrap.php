<?php

declare(strict_types=1);

$cacheDirectory = dirname(__DIR__) . DIRECTORY_SEPARATOR . '.phpunit.cache';

if (! is_dir($cacheDirectory)) {
    mkdir($cacheDirectory, 0777, true);
}

$packagesCache = $cacheDirectory . DIRECTORY_SEPARATOR . 'packages.php';
$servicesCache = $cacheDirectory . DIRECTORY_SEPARATOR . 'services.php';

putenv('APP_PACKAGES_CACHE=' . $packagesCache);
putenv('APP_SERVICES_CACHE=' . $servicesCache);

$_ENV['APP_PACKAGES_CACHE'] = $packagesCache;
$_ENV['APP_SERVICES_CACHE'] = $servicesCache;
$_SERVER['APP_PACKAGES_CACHE'] = $packagesCache;
$_SERVER['APP_SERVICES_CACHE'] = $servicesCache;

require dirname(__DIR__) . DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR . 'autoload.php';
