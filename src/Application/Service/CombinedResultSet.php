<?php

declare(strict_types=1);

/*
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */

namespace ParkManager\Application\Service;

use Doctrine\Common\Collections\Expr\Expression;
use Generator;
use ParkManager\Domain\ResultSet;
use Traversable;

/**
 * @template-implements ResultSet<mixed>
 */
final class CombinedResultSet implements ResultSet
{
    /** @var array<int, ResultSet<mixed>> */
    private array $resultSets;
    private ?int $limit = null;
    private ?int $offset = null;
    /** @var array<int, string>|null */
    private ?array $ordering = null;
    public ?Expression $expression = null;
    /** @var array<int, string|int>|null */
    private ?array $limitedToIds = null;
    /** @var array<int, Traversable>|null */
    private ?array $iterators = null;
    private ?int $nbResults = null;

    /**
     * @param ResultSet<mixed> ...$resultSets
     */
    public function __construct(ResultSet ...$resultSets)
    {
        $this->resultSets = $resultSets;
    }

    public function setLimit(?int $limit, ?int $offset = null): static
    {
        $this->limit = $limit;
        $this->offset = $offset;
        $this->iterators = null;

        return $this;
    }

    public function setOrdering(?string $field, ?string $order): static
    {
        if ($field === null || $order === null) {
            $this->ordering = null;
        } else {
            $this->ordering = [$field, $order];
        }

        $this->iterators = null;

        return $this;
    }

    public function limitToIds(?array $ids): static
    {
        $this->limitedToIds = $ids;
        $this->iterators = null;

        return $this;
    }

    public function getNbResults(): int
    {
        $this->init();

        return $this->nbResults;
    }

    /**
     * @return Generator<int, mixed, mixed, void>
     */
    public function getIterator(): Traversable
    {
        $this->init();

        foreach ($this->iterators as $iterator) {
            foreach ($iterator as $row) {
                yield $row;
            }
        }
    }

    public function count(): int
    {
        return $this->getNbResults();
    }

    private function init(): void
    {
        if ($this->iterators !== null) {
            return;
        }

        $this->nbResults = 0;
        $this->iterators = [];

        foreach ($this->resultSets as $resultSet) {
            $resultSet = $resultSet
                ->setLimit($this->limit, $this->offset)
                ->filter($this->expression)
                ->setOrdering($this->ordering[0] ?? null, $this->ordering[1] ?? null)
                ->limitToIds($this->limitedToIds)
            ;

            $this->nbResults += $resultSet->getNbResults();
            $this->iterators[] = $resultSet->getIterator();
        }
    }

    public function filter(?Expression $expression): static
    {
        $this->expression = $expression;
        $this->iterators = null;

        return $this;
    }
}
