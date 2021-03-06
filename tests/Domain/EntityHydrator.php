<?php

declare(strict_types=1);

/*
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */

namespace ParkManager\Tests\Domain;

use LogicException;
use ReflectionClass;

/**
 * The EntityHydrator helps with testing entities that perform
 * some internal logic after the hydration process.
 *
 * The Doctrine Hydration process uses reflection
 * to initialize a new object instance and set the properties
 * values. Without using the original constructor.
 *
 * Borrowed some stuff from https://github.com/PHP-CS-Fixer/AccessibleObject
 *
 * @template T of object
 * @mixin T
 */
final class EntityHydrator
{
    /** @var ReflectionClass<T> */
    private ReflectionClass $reflection;

    /** @var T */
    private object $object;

    /**
     * @param class-string<T> $class
     */
    private function __construct(string $class)
    {
        $this->reflection = new ReflectionClass($class);
        $this->object = $this->reflection->newInstanceWithoutConstructor();
    }

    /**
     * @param class-string<T> $class
     *
     * @return self<T>
     */
    public static function hydrateEntity(string $class): self
    {
        return new self($class);
    }

    public function __get(string $name): mixed
    {
        if (! property_exists($this->object, $name)) {
            throw new LogicException(sprintf('Cannot get non existing property %s->%s.', $this->object::class, $name));
        }

        $property = $this->reflection->getProperty($name);
        $property->setAccessible(true);

        return $property->getValue($this->object);
    }

    /**
     * @return $this
     */
    public function set(string $name, mixed $value): self
    {
        if (! property_exists($this->object, $name)) {
            throw new LogicException(sprintf('Cannot set non existing property %s->%s = %s.', $this->object::class, $name, var_export($value, true)));
        }

        $property = $this->reflection->getProperty($name);
        $property->setAccessible(true);
        $property->setValue($this->object, $value);

        return $this;
    }

    /**
     * @return T
     */
    public function getEntity(): object
    {
        return $this->object;
    }
}
