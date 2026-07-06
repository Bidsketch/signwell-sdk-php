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
}
