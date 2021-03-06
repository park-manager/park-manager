<?php

declare(strict_types=1);

/*
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */

namespace ParkManager\UI\Web\Action\Admin\Webhosting\DomainName;

use ParkManager\Application\Command\DomainName\AssignDomainNameToSpace;
use ParkManager\Domain\DomainName\DomainName;
use ParkManager\Domain\TranslatableMessage;
use ParkManager\Domain\Webhosting\Space\Exception\WebhostingSpaceBeingRemoved;
use ParkManager\Domain\Webhosting\Space\Space;
use ParkManager\UI\Web\Form\Type\ConfirmationForm;
use ParkManager\UI\Web\Response\RouteRedirectResponse;
use ParkManager\UI\Web\Response\TwigResponse;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

final class MakeDomainNamePrimaryOfSpace
{
    #[Route(path: 'webhosting/space/{space}/domain-name/{domainName}/make-primary', name: 'park_manager.admin.webhosting.space.domain_name.make_primary', methods: ['GET', 'POST'])]
    public function __invoke(Request $request, FormFactoryInterface $formFactory, Space $space, DomainName $domainName): RouteRedirectResponse | TwigResponse
    {
        if ($domainName->space === null) {
            return RouteRedirectResponse::toRoute('park_manager.admin.list_domain_names')->withFlash(
                type: 'error',
                message: 'flash.domain_name_not_space_owned',
                arguments: ['name' => $domainName->namePair->toString()]
            );
        }

        if ($domainName->space->isMarkedForRemoval()) {
            throw new WebhostingSpaceBeingRemoved($domainName->space->primaryDomainLabel);
        }

        if ($domainName->space !== $space) {
            return RouteRedirectResponse::toRoute('park_manager.admin.webhosting.space.domain_name.set_primary', ['space' => $space->id, 'domainName' => $domainName->id]);
        }

        $form = $formFactory->create(ConfirmationForm::class, $domainName, [
            'confirmation_title' => new TranslatableMessage('webhosting.domain_name.make_primary.heading', ['domain_name' => $domainName->toString()]),
            'confirmation_message' => new TranslatableMessage('webhosting.domain_name.make_primary.message', ['current_name' => $space->primaryDomainLabel]),
            'confirmation_label' => 'label.make_primary',
            'cancel_route' => ['name' => 'park_manager.admin.webhosting.space.list_domain_names', 'arguments' => ['space' => $space->id]],
            'command_factory' => static fn (): object => new AssignDomainNameToSpace($domainName->id, $space->id, true),
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            return RouteRedirectResponse::toRoute('park_manager.admin.webhosting.space.list_domain_names', ['space' => $space->id])
                ->withFlash(type: 'success', message: 'flash.domain_name_marked_as_primary')
            ;
        }

        return new TwigResponse('admin/webhosting/domain_name/make_primary.html.twig', [
            'form' => $form->createView(),
            'domainName' => $domainName,
            'space' => $space,
        ]);
    }
}
