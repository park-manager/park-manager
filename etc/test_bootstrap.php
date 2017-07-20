<?php

declare(strict_types=1);

/*
 * Copyright (c) the Contributors as noted in the AUTHORS file.
 *
 * This file is part of the Park-Manager project.
 *
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */

use Symfony\Component\Dotenv\Dotenv;

umask(0000);

require __DIR__.'/../vendor/autoload.php';

if (!getenv('APP_ENV')) {
    $dotEnv = new Dotenv();
    $dotEnv->populate(['APP_ENV' => 'test', 'APP_DEBUG' => '1']);
    $dotEnv->load(__DIR__.'/../.env');
}

// Try to set-up the database.
// ....
//$pomm = new \PommProject\Foundation\Pomm(['park_manager' => ['dns' => getenv('DATABASE_URL')]]);
//$pomm->setDefaultBuilder('park_manager');
//$pomm->getDefaultSession()->getAllClientForType('query')->query();