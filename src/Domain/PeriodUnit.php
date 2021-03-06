<?php

declare(strict_types=1);

/*
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */

namespace ParkManager\Domain;

/**
 * A PeriodUnit defines the period format in which a report must be provided.
 *
 * If HOUR is selected the report will provide the aggregated result
 * by each hour, within the selected range.
 *
 * If YEAR is selected the report will provide the aggregated result
 * by each year, within the selected range.
 */
final class PeriodUnit
{
    use EnumTrait;

    public const HOUR = 'hour';
    public const DAY = 'day';
    public const WEEK = 'week';
    public const MONTH = 'month';
    public const YEAR = 'year';
}
