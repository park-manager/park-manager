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

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use ParkManager\Module\CoreModule\Infrastructure\UserInterface\Web\Common\Form\Handler\ServiceBusFormFactory;
use ParkManager\Module\CoreModule\Infrastructure\UserInterface\Web\Form\Type\Security\ChangePasswordType;
use ParkManager\Module\CoreModule\Infrastructure\UserInterface\Web\Form\Type\Security\ConfirmPasswordResetType;

return function (ContainerConfigurator $c) {
    $di = $c->services()->defaults()
        ->autowire()
        ->autoconfigure()
        ->private();

    $di->set(ServiceBusFormFactory::class)
        ->arg('$commandBus', ref('park_manager.command_bus'))
        ->arg('$queryBus', ref('park_manager.query_bus'));

    $di->set(ChangePasswordType::class);
    $di->set(ConfirmPasswordResetType::class);
};