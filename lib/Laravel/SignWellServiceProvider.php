<?php

declare(strict_types=1);

// Source: signwell-sdk-generator/extras/php/overlay/lib/Laravel/SignWellServiceProvider.php
// Do not edit the generated SDK copy directly.

namespace SignWell\Sdk\Laravel;

use Illuminate\Contracts\Config\Repository as ConfigRepository;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Support\ServiceProvider;
use SignWell\Sdk\Client;
use SignWell\Sdk\Configuration;
use SignWell\Sdk\Resources\ApiApplicationApi;
use SignWell\Sdk\Resources\BulkSendApi;
use SignWell\Sdk\Resources\DocumentApi;
use SignWell\Sdk\Resources\MeApi;
use SignWell\Sdk\Resources\RegionalApi;
use SignWell\Sdk\Resources\TemplateApi;

final class SignWellServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__ . '/../../config/signwell.php', 'signwell');

        $configRepository = $this->app->make('config');
        if (!$configRepository instanceof ConfigRepository) {
            throw new \RuntimeException('Laravel config repository is not available.');
        }

        $config = self::configurationFromRepository($configRepository);
        Configuration::setDefaultConfiguration($config);
        $this->app->instance(Configuration::class, $config);

        $this->app->singleton(SignWellManager::class, fn (Application $app): SignWellManager => new SignWellManager($app->make(Configuration::class)));
        $this->app->alias(SignWellManager::class, Client::class);
        $this->app->alias(SignWellManager::class, 'signwell');

        foreach ([DocumentApi::class, TemplateApi::class, BulkSendApi::class, RegionalApi::class, MeApi::class, ApiApplicationApi::class] as $apiClass) {
            $this->app->bind($apiClass, fn (Application $app): object => new $apiClass(config: $app->make(Configuration::class)));
        }
    }

    public function boot(): void
    {
        $configPath = method_exists($this->app, 'configPath')
            ? $this->app->configPath('signwell.php')
            : $this->app->basePath('config/signwell.php');

        $this->publishes([
            __DIR__ . '/../../config/signwell.php' => $configPath,
        ], 'signwell-config');
    }

    private static function configurationFromRepository(ConfigRepository $repository): Configuration
    {
        $config = new Configuration();
        $apiKey = $repository->get('signwell.api_key');
        if (is_string($apiKey) && $apiKey !== '') {
            $config->setApiKey('X-Api-Key', $apiKey);
        }

        $host = $repository->get('signwell.host');
        if (is_string($host) && $host !== '') {
            $config->setHost($host);
        }

        return $config;
    }
}
