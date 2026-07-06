<?php

declare(strict_types=1);

namespace SignWell\Sdk\Test\Regression;

use Illuminate\Contracts\Config\Repository as ConfigRepository;
use Illuminate\Contracts\Foundation\CachesConfiguration;
use Illuminate\Support\Env;
use PHPUnit\Framework\TestCase;
use SignWell\Sdk\Configuration;
use SignWell\Sdk\Laravel\SignWellServiceProvider;

final class LaravelServiceProviderTest extends TestCase
{
    public function testRegisterSetsConfiguredDefaultConfigurationForStaticHelpers(): void
    {
        $previous = Configuration::getDefaultConfiguration();
        $app = new FakeLaravelApplication(new FakeConfigRepository([
            'signwell' => [
                'api_key' => 'laravel-key',
                'host' => 'https://api.signwell.test/',
            ],
        ]));

        try {
            (new SignWellServiceProvider($app))->register();

            $config = $app->make(Configuration::class);
            self::assertInstanceOf(Configuration::class, $config);
            self::assertSame($config, Configuration::getDefaultConfiguration());
            self::assertSame('laravel-key', $config->getApiKey('X-Api-Key'));
            self::assertSame('https://api.signwell.test', $config->getHost());
        } finally {
            Configuration::setDefaultConfiguration($previous);
        }
    }

    public function testRegisterMergesPackageConfigFromEnvironmentVariables(): void
    {
        $previous = Configuration::getDefaultConfiguration();
        $previousApiKey = getenv('SIGNWELL_API_KEY');
        $previousHost = getenv('SIGNWELL_API_HOST');

        putenv('SIGNWELL_API_KEY=env-key');
        putenv('SIGNWELL_API_HOST=https://env.signwell.test/');
        Env::enablePutenv();

        $app = new FakeLaravelApplication(new FakeConfigRepository([]), configurationIsCached: false);

        try {
            (new SignWellServiceProvider($app))->register();

            $config = $app->make(Configuration::class);
            self::assertInstanceOf(Configuration::class, $config);
            self::assertSame($config, Configuration::getDefaultConfiguration());
            self::assertSame('env-key', $config->getApiKey('X-Api-Key'));
            self::assertSame('https://env.signwell.test', $config->getHost());
        } finally {
            self::restoreEnvironmentVariable('SIGNWELL_API_KEY', $previousApiKey);
            self::restoreEnvironmentVariable('SIGNWELL_API_HOST', $previousHost);
            Env::enablePutenv();
            Configuration::setDefaultConfiguration($previous);
        }
    }

    public function testRegisterRejectsUnsafeConfiguredHost(): void
    {
        $previous = Configuration::getDefaultConfiguration();
        $app = new FakeLaravelApplication(new FakeConfigRepository([
            'signwell' => [
                'api_key' => 'laravel-key',
                'host' => 'http://api.signwell.test',
            ],
        ]));

        try {
            (new SignWellServiceProvider($app))->register();
            self::fail('Expected unsafe Laravel host to be rejected.');
        } catch (\InvalidArgumentException $error) {
            self::assertStringContainsString('HTTPS URL', $error->getMessage());
        } finally {
            Configuration::setDefaultConfiguration($previous);
        }
    }

    private static function restoreEnvironmentVariable(string $key, string|false $value): void
    {
        if ($value === false) {
            putenv($key);

            return;
        }

        putenv("{$key}={$value}");
    }
}

final class FakeLaravelApplication implements CachesConfiguration
{
    /** @var array<string, mixed> */
    private array $instances = [];

    /** @var array<string, mixed> */
    private array $singletons = [];

    /** @var array<string, mixed> */
    private array $bindings = [];

    /** @var array<string, string> */
    private array $aliases = [];

    public function __construct(
        private readonly ConfigRepository $config,
        private readonly bool $configurationIsCached = true
    ) {
    }

    /** @param array<string, mixed> $parameters */
    public function make(string $abstract, array $parameters = []): mixed
    {
        unset($parameters);

        if ($abstract === 'config') {
            return $this->config;
        }

        if (array_key_exists($abstract, $this->instances)) {
            return $this->instances[$abstract];
        }

        throw new \RuntimeException("No fake Laravel binding registered for {$abstract}.");
    }

    public function instance(string $abstract, mixed $instance): mixed
    {
        $this->instances[$abstract] = $instance;

        return $instance;
    }

    public function singleton(string $abstract, mixed $concrete = null): void
    {
        $this->singletons[$abstract] = $concrete;
    }

    public function alias(string $abstract, string $alias): void
    {
        $this->aliases[$alias] = $abstract;
    }

    public function bind(string $abstract, mixed $concrete = null): void
    {
        $this->bindings[$abstract] = $concrete;
    }

    public function configurationIsCached()
    {
        return $this->configurationIsCached;
    }

    public function getCachedConfigPath()
    {
        return '';
    }

    public function getCachedServicesPath()
    {
        return '';
    }
}

final class FakeConfigRepository implements ConfigRepository
{
    /** @param array<string, mixed> $items */
    public function __construct(private array $items)
    {
    }

    public function has($key)
    {
        $sentinel = new \stdClass();

        return $this->get($key, $sentinel) !== $sentinel;
    }

    public function get($key, $default = null)
    {
        if (is_array($key)) {
            $values = [];
            foreach ($key as $itemKey => $itemDefault) {
                if (is_int($itemKey)) {
                    $values[$itemDefault] = $this->get($itemDefault);
                } else {
                    $values[$itemKey] = $this->get($itemKey, $itemDefault);
                }
            }

            return $values;
        }

        return $this->getValue((string) $key, $default);
    }

    public function all()
    {
        return $this->items;
    }

    public function set($key, $value = null)
    {
        if (is_array($key)) {
            foreach ($key as $itemKey => $itemValue) {
                $this->setValue((string) $itemKey, $itemValue);
            }

            return;
        }

        $this->setValue((string) $key, $value);
    }

    public function prepend($key, $value)
    {
        $values = $this->get($key, []);
        $values = is_array($values) ? $values : [];
        array_unshift($values, $value);
        $this->set($key, $values);
    }

    public function push($key, $value)
    {
        $values = $this->get($key, []);
        $values = is_array($values) ? $values : [];
        $values[] = $value;
        $this->set($key, $values);
    }

    private function getValue(string $key, mixed $default): mixed
    {
        $value = $this->items;
        foreach (explode('.', $key) as $segment) {
            if (!is_array($value) || !array_key_exists($segment, $value)) {
                return $default;
            }

            $value = $value[$segment];
        }

        return $value;
    }

    private function setValue(string $key, mixed $value): void
    {
        $target = &$this->items;
        foreach (explode('.', $key) as $segment) {
            if (!isset($target[$segment]) || !is_array($target[$segment])) {
                $target[$segment] = [];
            }

            $target = &$target[$segment];
        }

        $target = $value;
    }
}
