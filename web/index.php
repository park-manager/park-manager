<?php

/*
 * Copyright (c) the Contributors as noted in the AUTHORS file.
 *
 * This file is part of the Park-Manager project.
 *
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */

use ParkManager\Bundle\CoreBundle\EntryPoints\Http\Application;
use Symfony\Component\ClassLoader\ApcClassLoader;
use Symfony\Component\Debug\Debug;

// Don't use the class caches when xdebug is enabled
// so its easier to debug errors.
$loadClassCache = !isset($_SERVER['APP_DEBUG'], $_COOKIE['XDEBUG_SESSION']);

if ($loadClassCache) {
    $loader = require_once __DIR__.'/../var/bootstrap.php.cache';
} else {
    $loader = require_once __DIR__.'/../app/autoload.php';
}

if (isset($_SERVER['APP_DEBUG'])) {
    Debug::enable();
} elseif (extension_loaded('apc')) {
    $apcLoader = new ApcClassLoader('parkmanager', $loader);
    $loader->unregister();
    $apcLoader->register(true);
}

$application = new Application($loadClassCache);
$application->run();
