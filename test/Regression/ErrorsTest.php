<?php

declare(strict_types=1);

namespace SignWell\Sdk\Test\Regression;

use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase;
use SignWell\Sdk\Configuration;
use SignWell\Sdk\Errors\NotFoundError;
use SignWell\Sdk\Resources\DocumentApi;

final class ErrorsTest extends TestCase
{
    public function testStatusCodesRaiseTypedErrors(): void
    {
        $client = new Client([
            'handler' => HandlerStack::create(new MockHandler([
                new Response(404, ['Content-Type' => 'application/json'], '{"message":"not found"}'),
            ])),
        ]);
        $api = new DocumentApi($client, (new Configuration())->setHost('https://www.signwell.test')->setApiKey('X-Api-Key', 'test-key'));

        try {
            $api->getDocument('missing');
            self::fail('Expected typed NotFoundError');
        } catch (NotFoundError $error) {
            self::assertSame(404, $error->getCode());
            self::assertSame('{"message":"not found"}', $error->getResponseBody());
        }
    }
}
