<?php

/**
 * Slim Framework (https://slimframework.com).
 *
 * @license https://github.com/slimphp/Slim/blob/5.x/LICENSE.md (MIT License)
 */

declare(strict_types=1);

namespace Slim\Container;

use DI\Container;
use Psr\Container\ContainerInterface;
use Slim\Interfaces\ContainerFactoryInterface;

final class PhpDiContainerFactory implements ContainerFactoryInterface
{
    public function createContainer(array $definitions = []): ContainerInterface
    {
        return new Container($definitions);
    }

    public function addDefinitions(): array
    {
        return [];
    }

    public function addSettings(): array
    {
        return [];
    }
}
