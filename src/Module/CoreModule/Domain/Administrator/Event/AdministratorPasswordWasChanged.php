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

namespace ParkManager\Module\CoreModule\Domain\Administrator\Event;

use ParkManager\Module\CoreModule\Domain\Administrator\AdministratorId;

final class AdministratorPasswordWasChanged
{
    /** @var AdministratorId */
    private $id;

    /** @var string|null */
    private $newPassword;

    public function __construct(AdministratorId $id, ?string $newPassword)
    {
        $this->id          = $id;
        $this->newPassword = $newPassword;
    }

    public function getId(): AdministratorId
    {
        return $this->id;
    }

    public function getPassword(): ?string
    {
        return $this->newPassword;
    }
}