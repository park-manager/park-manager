<?php

declare(strict_types=1);

/*
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */

namespace ParkManager\UI\Web\Action\Admin\Webhosting\Plan;

use ParkManager\Domain\Webhosting\Constraint\Plan;
use ParkManager\Domain\Webhosting\Space\WebhostingSpaceRepository;
use ParkManager\UI\Web\Response\TwigResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

final class ShowWebhostingPlan extends AbstractController
{
    #[Route(path: 'webhosting/plan/{plan}/', name: 'park_manager.admin.webhosting.plan.show', methods: ['GET', 'HEAD'])]
    public function __invoke(Request $request, Plan $plan): TwigResponse
    {
        $usedBySpacesNb = $this->get(WebhostingSpaceRepository::class)->allWithAssignedPlan($plan->id)->getNbResults();

        return new TwigResponse('admin/webhosting/plan/show.html.twig', [
            'plan' => $plan,
            'spaces_count' => $usedBySpacesNb,
        ]);
    }

    public static function getSubscribedServices(): array
    {
        return parent::getSubscribedServices() + [WebhostingSpaceRepository::class];
    }
}
