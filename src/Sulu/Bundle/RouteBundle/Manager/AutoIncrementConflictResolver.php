<?php

/*
 * This file is part of Sulu.
 *
 * (c) Sulu GmbH
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Sulu\Bundle\RouteBundle\Manager;

use Sulu\Bundle\RouteBundle\Entity\RouteRepositoryInterface;
use Sulu\Bundle\RouteBundle\Model\RouteInterface;

class AutoIncrementConflictResolver implements ConflictResolverInterface
{
    public function __construct(private RouteRepositoryInterface $routeRepository)
    {
    }

    public function resolve(RouteInterface $route)
    {
        $i = 1;
        $path = $route->getPath();
        $conflict = $this->routeRepository->findByPath($route->getPath(), $route->getLocale());
        while ($conflict) {
            if ($conflict && $conflict->getEntityClass() === $route->getEntityClass()
                && $conflict->getEntityId() == $route->getEntityId()
            ) {
                // if conflict is found but has the same entity relation return this instead of the newly created route.
                return $conflict;
            }

            $route->setPath($path . '-' . ($i++));
            $conflict = $this->routeRepository->findByPath($route->getPath(), $route->getLocale());
        }

        return $route;
    }
}
