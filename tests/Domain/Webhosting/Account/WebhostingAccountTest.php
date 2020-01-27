<?php

declare(strict_types=1);

/*
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */

namespace ParkManager\Tests\Domain\Webhosting\Account;

use DateTimeImmutable;
use ParkManager\Tests\Infrastructure\Webhosting\Fixtures\MonthlyTrafficQuota;
use ParkManager\Domain\OwnerId;
use ParkManager\Domain\Webhosting\Account\WebhostingAccount;
use ParkManager\Domain\Webhosting\Account\WebhostingAccountId;
use ParkManager\Domain\Webhosting\Plan\Constraints;
use ParkManager\Domain\Webhosting\Plan\WebhostingPlan;
use ParkManager\Domain\Webhosting\Plan\WebhostingPlanId;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 */
final class WebhostingAccountTest extends TestCase
{
    private const ACCOUNT_ID = '374dd50e-9b9f-11e7-9730-acbc32b58315';

    private const OWNER_ID1 = '2a9cd25c-97ca-11e7-9683-acbc32b58315';
    private const OWNER_ID2 = 'ce18c388-9ba2-11e7-b15f-acbc32b58315';

    private const PLAN_ID_1 = '654665ea-9869-11e7-9563-acbc32b58315';
    private const PLAN_ID_2 = 'f5788aae-9aed-11e7-a3c9-acbc32b58315';

    /** @test */
    public function it_registers_an_webhosting_account(): void
    {
        $id = WebhostingAccountId::create();
        $constraints = new Constraints();
        $plan = $this->createWebhostingPlan($constraints);

        $account = WebhostingAccount::register($id, $owner = OwnerId::fromString(self::OWNER_ID1), $plan);

        static::assertEquals($id, $account->getId());
        static::assertEquals($owner, $account->getOwner());
        static::assertSame($plan, $account->getPlan());
        static::assertSame($constraints, $account->getPlanConstraints());
    }

    /** @test */
    public function it_registers_an_webhosting_account_with_custom_constraints(): void
    {
        $id = WebhostingAccountId::create();
        $constraints = new Constraints();

        $account = WebhostingAccount::registerWithCustomConstraints($id, $owner = OwnerId::fromString(self::OWNER_ID1), $constraints);

        static::assertEquals($id, $account->getId());
        static::assertEquals($owner, $account->getOwner());
        static::assertSame($constraints, $account->getPlanConstraints());
        static::assertNull($account->getPlan());
    }

    /** @test */
    public function it_allows_changing_plan_assignment(): void
    {
        $id2 = WebhostingAccountId::create();
        $constraints1 = new Constraints();
        $constraints2 = new Constraints(new MonthlyTrafficQuota(50));
        $plan1 = $this->createWebhostingPlan($constraints1);
        $plan2 = $this->createWebhostingPlan($constraints2, self::PLAN_ID_2);
        $account1 = WebhostingAccount::register(WebhostingAccountId::create(), OwnerId::fromString(self::OWNER_ID1), $plan1);
        $account2 = WebhostingAccount::register($id2, OwnerId::fromString(self::OWNER_ID1), $plan1);

        $account1->assignPlan($plan1);
        $account2->assignPlan($plan2);

        static::assertSame($plan1, $account1->getPlan(), 'Plan should not change');
        static::assertSame($plan1->getConstraints(), $account1->getPlanConstraints(), 'Constraints should not change');

        static::assertSame($plan2, $account2->getPlan());
        static::assertSame($plan1->getConstraints(), $account2->getPlanConstraints());
    }

    /** @test */
    public function it_allows_changing_plan_assignment_with_constraints(): void
    {
        $id2 = WebhostingAccountId::create();
        $constraints1 = new Constraints();
        $constraints2 = new Constraints(new MonthlyTrafficQuota(50));
        $plan1 = $this->createWebhostingPlan($constraints1);
        $plan2 = $this->createWebhostingPlan($constraints2, self::PLAN_ID_2);
        $account1 = WebhostingAccount::register(WebhostingAccountId::create(), OwnerId::fromString(self::OWNER_ID1), $plan1);
        $account2 = WebhostingAccount::register($id2, OwnerId::fromString(self::OWNER_ID1), $plan1);

        $account1->assignPlanWithConstraints($plan1);
        $account2->assignPlanWithConstraints($plan2);

        static::assertSame($plan1, $account1->getPlan(), 'Plan should not change');
        static::assertSame($plan1->getConstraints(), $account1->getPlanConstraints(), 'Constraints should not change');

        static::assertSame($plan2, $account2->getPlan());
        static::assertSame($plan2->getConstraints(), $account2->getPlanConstraints());
    }

    /** @test */
    public function it_updates_account_when_assigning_plan_Constraints_are_different(): void
    {
        $id = WebhostingAccountId::create();
        $plan = $this->createWebhostingPlan(new Constraints());
        $account = WebhostingAccount::register($id, OwnerId::fromString(self::OWNER_ID1), $plan);

        $plan->changeConstraints($newConstraints = new Constraints(new MonthlyTrafficQuota(50)));
        $account->assignPlanWithConstraints($plan);

        static::assertSame($plan, $account->getPlan());
        static::assertSame($plan->getConstraints(), $account->getPlanConstraints());
    }

    /** @test */
    public function it_allows_assigning_custom_specification(): void
    {
        $id = WebhostingAccountId::create();
        $plan = $this->createWebhostingPlan(new Constraints());
        $account = WebhostingAccount::register($id, OwnerId::fromString(self::OWNER_ID1), $plan);

        $account->assignCustomConstraints($newConstraints = new Constraints(new MonthlyTrafficQuota(50)));

        static::assertNull($account->getPlan());
        static::assertSame($newConstraints, $account->getPlanConstraints());
    }

    /** @test */
    public function it_allows_changing_custom_specification(): void
    {
        $id = WebhostingAccountId::create();
        $account = WebhostingAccount::registerWithCustomConstraints($id, OwnerId::fromString(self::OWNER_ID1), new Constraints());

        $account->assignCustomConstraints($newConstraints = new Constraints(new MonthlyTrafficQuota(50)));

        static::assertNull($account->getPlan());
        static::assertSame($newConstraints, $account->getPlanConstraints());
    }

    /** @test */
    public function it_does_not_update_account_Constraints_when_assigning_Constraints_are_same(): void
    {
        $id = WebhostingAccountId::create();
        $Constraints = new Constraints();
        $account = WebhostingAccount::registerWithCustomConstraints($id, OwnerId::fromString(self::OWNER_ID1), $Constraints);

        $account->assignCustomConstraints($Constraints);

        static::assertNull($account->getPlan());
        static::assertSame($Constraints, $account->getPlanConstraints());
    }

    /** @test */
    public function it_supports_switching_the_account_owner(): void
    {
        $account1 = WebhostingAccount::register(
            WebhostingAccountId::fromString(self::ACCOUNT_ID),
            OwnerId::fromString(self::OWNER_ID1),
            $this->createWebhostingPlan(new Constraints())
        );
        $account2 = WebhostingAccount::register(
            $id2 = WebhostingAccountId::fromString(self::ACCOUNT_ID),
            OwnerId::fromString(self::OWNER_ID1),
            $this->createWebhostingPlan(new Constraints())
        );

        $account1->switchOwner($owner1 = OwnerId::fromString(self::OWNER_ID1));
        $account2->switchOwner($owner2 = OwnerId::fromString(self::OWNER_ID2));

        static::assertEquals($owner1, $account1->getOwner());
        static::assertEquals($owner2, $account2->getOwner());
    }

    /** @test */
    public function it_allows_being_marked_for_removal(): void
    {
        $account1 = WebhostingAccount::register(
            WebhostingAccountId::fromString(self::ACCOUNT_ID),
            OwnerId::fromString(self::OWNER_ID1),
            $this->createWebhostingPlan(new Constraints())
        );
        $account2 = WebhostingAccount::register(
            $id2 = WebhostingAccountId::fromString(self::ACCOUNT_ID),
            OwnerId::fromString(self::OWNER_ID1),
            $this->createWebhostingPlan(new Constraints())
        );

        $account2->markForRemoval();
        $account2->markForRemoval();

        static::assertFalse($account1->isMarkedForRemoval());
        static::assertTrue($account2->isMarkedForRemoval());
    }

    /** @test */
    public function it_can_expire(): void
    {
        $account1 = WebhostingAccount::register(
            WebhostingAccountId::fromString(self::ACCOUNT_ID),
            OwnerId::fromString(self::OWNER_ID1),
            $this->createWebhostingPlan(new Constraints())
        );
        $account2 = WebhostingAccount::register(
            $id2 = WebhostingAccountId::fromString(self::ACCOUNT_ID),
            OwnerId::fromString(self::OWNER_ID1),
            $this->createWebhostingPlan(new Constraints())
        );

        $account2->setExpirationDate($date = new DateTimeImmutable('now +6 days'));

        static::assertFalse($account1->isExpired());
        static::assertFalse($account1->isExpired($date->modify('+2 days')));

        static::assertFalse($account2->isExpired($date->modify('-10 days')));
        static::assertTrue($account2->isExpired($date));
        static::assertTrue($account2->isExpired($date->modify('+2 days')));

        $account1->removeExpirationDate();
        $account2->removeExpirationDate();

        static::assertFalse($account1->isExpired());
        static::assertFalse($account2->isExpired($date));
        static::assertFalse($account2->isExpired($date->modify('+2 days')));
    }

    private function createWebhostingPlan(Constraints $Constraints, string $id = self::PLAN_ID_1): WebhostingPlan
    {
        return new WebhostingPlan(WebhostingPlanId::fromString($id), $Constraints);
    }
}