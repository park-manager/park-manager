<?php

declare(strict_types=1);

/*
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */

namespace ParkManager\Bundle\CoreBundle\UseCase\Client;

use ParkManager\Bundle\CoreBundle\Model\Client\ClientId;

final class DeleteRegistration
{
    private $id;

    public function __construct(string $id)
    {
        $this->id = ClientId::fromString($id);
    }

    public function id(): ClientId
    {
        return $this->id;
    }
}
