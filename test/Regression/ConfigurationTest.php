<?php

declare(strict_types=1);

namespace SignWell\Sdk\Test\Regression;

use PHPUnit\Framework\TestCase;
use SignWell\Sdk\Configuration;

final class ConfigurationTest extends TestCase
{
    public function testSetHostAcceptsHttpsUrlsAndNormalizesTrailingSlashes(): void
    {
        $config = new Configuration();

        self::assertSame($config, $config->setHost('https://www.signwell.com/'));
        self::assertSame('https://www.signwell.com', $config->getHost());

        $config->setHost('https://www.signwell.test/base/');
        self::assertSame('https://www.signwell.test/base', $config->getHost());
    }

    public function testSetHostRejectsUnsafeUrls(): void
    {
        $unsafeHosts = [
            '',
            '   ',
            'not-a-url',
            'http://www.signwell.com',
            'https://user:pass@www.signwell.com',
            'https://www.signwell.com?api_key=secret',
            'https://www.signwell.com#fragment',
        ];

        foreach ($unsafeHosts as $host) {
            try {
                (new Configuration())->setHost($host);
                self::fail("Expected host to be rejected: {$host}");
            } catch (\InvalidArgumentException) {
                self::assertTrue(true);
            }
        }
    }

    public function testTimeoutsHaveDefaultsAndCanBeConfigured(): void
    {
        $config = new Configuration();

        self::assertSame(10.0, $config->getConnectTimeout());
        self::assertSame(30.0, $config->getTimeout());

        self::assertSame($config, $config->setConnectTimeout(2));
        self::assertSame($config, $config->setTimeout(7.5));
        self::assertSame(2.0, $config->getConnectTimeout());
        self::assertSame(7.5, $config->getTimeout());

        $config->setConnectTimeout(0);
        $config->setTimeout(0);
        self::assertSame(0.0, $config->getConnectTimeout());
        self::assertSame(0.0, $config->getTimeout());
    }

    public function testTimeoutsRejectNegativeValues(): void
    {
        $config = new Configuration();

        try {
            $config->setConnectTimeout(-0.1);
            self::fail('Expected connect timeout to reject negative values.');
        } catch (\InvalidArgumentException $error) {
            self::assertStringContainsString('Connect timeout', $error->getMessage());
        }

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Timeout must be greater than or equal to 0.');

        $config->setTimeout(-1);
    }
}
