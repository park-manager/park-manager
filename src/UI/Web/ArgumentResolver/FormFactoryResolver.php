<?php

declare(strict_types=1);

/*
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */

namespace ParkManager\UI\Web\ArgumentResolver;

use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Controller\ArgumentValueResolverInterface;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;

final class FormFactoryResolver implements ArgumentValueResolverInterface
{
    public function __construct(private FormFactoryInterface $formFactory)
    {
    }

    public function supports(Request $request, ArgumentMetadata $argument): bool
    {
        return ! $argument->isVariadic() && $argument->getType() === FormFactoryInterface::class;
    }

    public function resolve(Request $request, ArgumentMetadata $argument): iterable
    {
        yield $this->formFactory;
    }
}
