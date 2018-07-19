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

namespace ParkManager\Component\User\Model\Command;

use ParkManager\Component\Security\Token\SplitToken;

final class ConfirmEmailAddressChange
{
    private $token;

    public function __construct(SplitToken $token)
    {
        $this->token = $token;
    }

    public function token(): SplitToken
    {
        return $this->token;
    }
}
