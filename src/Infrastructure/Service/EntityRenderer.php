<?php

declare(strict_types=1);

/*
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */

namespace ParkManager\Infrastructure\Service;

use ParkManager\Domain\ResultSet;
use Symfony\Contracts\Translation\LocaleAwareInterface;
use Twig\Environment as TwigEnvironment;

/**
 * Renders a view-ready version of an entity.
 *
 * Similar to Serializer, but always returns a view-version
 * of the entity information. Either HTML or Markdown.
 *
 * Automatically tries to find the template by `[domain-layer][subDomain/]/[entityName].{html}.twig`
 * all in lowercase. Either `webhosting/ftp/user.html.twig`.
 *
 * The templates should contain two blocks ('short', 'detailed'), which are rendered separately.
 */
final class EntityRenderer
{
    private TwigEnvironment $twig;
    private LocaleAwareInterface $localeAware;

    public function __construct(TwigEnvironment $twig, LocaleAwareInterface $localeAware)
    {
        $this->twig = $twig;
        $this->localeAware = $localeAware;
    }

    /**
     * @param class-string $entityName
     */
    public function getEntityLabel(string $entityName): string
    {
        if (! str_starts_with($entityName, 'ParkManager\\Domain\\')) {
            throw new \InvalidArgumentException(sprintf('Expected %s to begin with "ParkManager\\Domain\\"', $entityName));
        }

        $class = mb_substr(ltrim($entityName, '\\'), 19); // Strips `ParkManager\Domain\`
        $parts = explode('\\', $class);

        // Normalize 'Space\Space' to 'Space'.
        if (\count($parts) > 1 && $parts[\count($parts) - 1] === $parts[\count($parts) - 2]) {
            array_pop($parts);
        }

        return mb_strtolower(implode('.', $parts));
    }

    /**
     * Returns the rendered short version of the Entity information,
     * usually for errors messages and generic data-list items.
     *
     * @param array<string, mixed> $extra Pass addition information to the template context
     */
    public function short(object $entity, array $extra = [], ?string $locale = null, string $format = 'html'): string
    {
        return $this->renderTemplate($entity, $extra, $format, 'short', $locale);
    }

    /**
     * @param array<string, mixed> $extra
     *
     * @phpstan-param 'short'|'detailed' $block
     */
    private function renderTemplate(object $entity, array $extra, string $format, string $block, ?string $locale): string
    {
        $currentLocale = $this->localeAware->getLocale();

        try {
            if ($locale) {
                $this->localeAware->setLocale($locale);
            }

            return $this->twig->load(sprintf('entity_rendering/%s.%s.twig', $this->resolveTemplate($entity), $format))->renderBlock(
                $block,
                ['entity' => $entity] + $extra
            );
        } finally {
            if ($locale) {
                $this->localeAware->setLocale($currentLocale);
            }
        }
    }

    private function resolveTemplate(object $entity): string
    {
        $class = mb_substr(\get_class($entity), 19); // Strips `ParkManager\Domain\`

        return mb_strtolower(str_replace('\\', '/', $class));
    }

    /**
     * Returns the rendered detailed version of the Entity information,
     * usually when transferring ownership or during confirmation.
     *
     * @param array<string, mixed> $extra Pass addition information to the template context
     */
    public function detailed(object $entity, array $extra = [], ?string $locale = null, string $format = 'html'): string
    {
        return $this->renderTemplate($entity, $extra, $format, 'detailed', $locale);
    }

    /**
     * @param array<class-string, ResultSet<mixed>> $sets
     * @param array<string, mixed>                  $extra
     */
    public function listedBySet(array $sets, array $extra = [], ?string $locale = null): string
    {
        $currentLocale = $this->localeAware->getLocale();
        $renderedSets = [];

        try {
            if ($locale) {
                $this->localeAware->setLocale($locale);
            }

            foreach ($sets as $name => $resultSet) {
                if ($resultSet->getNbResults() < 1) {
                    continue;
                }

                // Reset for each new set.
                $resolveTemplate = null;

                $label = $this->getEntityLabel($name);
                $renderedSets[$label] = [];

                foreach ($resultSet as $entity) {
                    $resolveTemplate ??= $this->resolveTemplate($entity);

                    $renderedSets[$label][] = $this->twig->load(sprintf('entity_rendering/%s.%s.twig', $resolveTemplate, 'html'))
                        ->renderBlock('short', ['entity' => $entity] + $extra)
                    ;
                }
            }

            return $this->twig->render('entity_rendering/listed.html.twig', ['sets' => $renderedSets] + $extra);
        } finally {
            if ($locale) {
                $this->localeAware->setLocale($currentLocale);
            }
        }
    }
}
