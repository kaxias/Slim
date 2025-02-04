<?php

/**
 * Slim Framework (https://slimframework.com).
 *
 * @license https://github.com/slimphp/Slim/blob/5.x/LICENSE.md (MIT License)
 */

declare(strict_types=1);

namespace Slim\Builder;

use Psr\Container\ContainerInterface;
use RuntimeException;
use Slim\App;
use Slim\Container\DefaultDefinitions;
use Slim\Container\HttpDefinitions;
use Slim\Container\PhpDiContainerFactory;
use Slim\Interfaces\ContainerFactoryInterface;

/**
 * This class is responsible for building and configuring a Slim application with a dependency injection (DI) container.
 * It provides methods to set up service definitions, configure a custom container factory, and more.
 *
 * Key functionalities include:
 * - Building the Slim `App` instance with configured dependencies.
 * - Customizing the DI container with user-defined service definitions or a custom container factory.
 * - Configuring application settings.
 */
final class AppBuilder
{
    /**
     * @var array Service definitions for the DI container
     */
    private array $definitions = [];

    /**
     * @var ContainerFactoryInterface|null Factory function for creating a custom DI container
     */
    private ?ContainerFactoryInterface $containerFactory = null;

    /**
     * The constructor.
     *
     * Initializes the builder with the default service definitions.
     */
    public function __construct()
    {
        $this->addDefinitions(DefaultDefinitions::class);
        $this->addDefinitions(HttpDefinitions::class);
    }

    /**
     * Builds the Slim application instance using the configured DI container.
     *
     * @return App The fully built Slim application instance
     */
    public function build(): App
    {
        $this->addDefinitions($this->containerFactory->addDefinitions());
        $this->addSettings($this->containerFactory->addSettings());

        return $this->buildContainer()->get(App::class);
    }

    /**
     * Sets the service definitions for the DI container.
     *
     * The method accepts either an array of definitions or the name of a class that provides definitions.
     * If a class name is provided, its definitions are added to the existing ones.
     *
     * @param array|string $definitions An array of service definitions or a class name providing them
     *
     * @throws RuntimeException
     *
     * @return self The current AppBuilder instance for method chaining
     */
    public function addDefinitions(array|string $definitions): self
    {
        if (is_string($definitions)) {
            if (class_exists($definitions)) {
                $definitions = (array)call_user_func(new $definitions());
            } else {
                $definitions = require $definitions;

                if (!is_array($definitions)) {
                    throw new RuntimeException('Definition file should return an array of definitions');
                }
            }
        }

        $this->definitions = array_merge($this->definitions, $definitions);

        return $this;
    }

    /**
     * Sets a custom factory for creating the DI container.
     *
     * @param ContainerFactoryInterface $containerFactory A DI container factory
     *
     * @return self The current AppBuilder instance for method chaining
     */
    public function setContainerFactory(ContainerFactoryInterface $containerFactory): self
    {
        $this->containerFactory = $containerFactory;

        return $this;
    }

    /**
     * Add application-wide settings in the DI container.
     *
     * This method allows the user to configure various settings for the Slim application,
     * by passing an associative array of settings.
     *
     * @param array $settings An associative array of application settings
     *
     * @return self The current AppBuilder instance for method chaining
     */
    public function addSettings(array $settings): self
    {
        $this->addDefinitions(
            [
                'settings' => $settings,
            ]
        );

        return $this;
    }

    /**
     * Creates and configures the DI container.
     *
     * If a custom container factory is set, it will be used to create the container;
     * otherwise, a default container with the provided definitions will be created.
     *
     * @return ContainerInterface The configured DI container
     */
    private function buildContainer(): ContainerInterface
    {
        $this->containerFactory = $this->containerFactory ?? new PhpDiContainerFactory();

        return $this->containerFactory->createContainer($this->definitions);
    }
}
