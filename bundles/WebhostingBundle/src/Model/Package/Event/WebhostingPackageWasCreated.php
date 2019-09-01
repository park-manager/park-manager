<?php

declare(strict_types=1);

/*
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */

namespace ParkManager\Bundle\WebhostingBundle\Model\Package\Event;

use ParkManager\Bundle\WebhostingBundle\Model\Package\Capabilities;
use ParkManager\Bundle\WebhostingBundle\Model\Package\WebhostingPackageId;

final class WebhostingPackageWasCreated
{
    private $packageId;
    private $capabilities;

    public function __construct(WebhostingPackageId $id, Capabilities $capabilities)
    {
        $this->packageId    = $id;
        $this->capabilities = $capabilities;
    }

    public function id(): WebhostingPackageId
    {
        return $this->packageId;
    }

    public function capabilities(): Capabilities
    {
        return $this->capabilities;
    }
}