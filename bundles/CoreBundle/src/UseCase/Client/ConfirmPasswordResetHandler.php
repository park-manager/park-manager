<?php

declare(strict_types=1);

/*
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */

namespace ParkManager\Bundle\CoreBundle\UseCase\Client;

use ParkManager\Bundle\CoreBundle\Model\Client\ClientRepository;
use ParkManager\Bundle\CoreBundle\Model\Client\Exception\PasswordResetConfirmationRejected;

final class ConfirmPasswordResetHandler
{
    /** @var ClientRepository */
    private $repository;

    public function __construct(ClientRepository $repository)
    {
        $this->repository = $repository;
    }

    public function __invoke(ConfirmPasswordReset $command): void
    {
        $token       = $command->token();
        $client      = $this->repository->getByPasswordResetToken($token->selector());
        $exception   = null;

        // Cannot use finally here as the exception triggers the global exception handler
        // making the overall process unpredictable.

        try {
            $client->confirmPasswordReset($token, $command->password());
            $this->repository->save($client);
        } catch (PasswordResetConfirmationRejected $e) {
            $this->repository->save($client);

            throw $e;
        }
    }
}