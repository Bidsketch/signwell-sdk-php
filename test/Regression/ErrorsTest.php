<?php

declare(strict_types=1);

namespace SignWell\Sdk\Test\Regression;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\RequestOptions;
use PHPUnit\Framework\TestCase;
use SignWell\Sdk\Configuration;
use SignWell\Sdk\Errors\ApiConnectionError;
use SignWell\Sdk\Errors\ApiTimeoutError;
use SignWell\Sdk\Errors\NotFoundError;
use SignWell\Sdk\Errors\RateLimitError;
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

    public function testDefaultTimeoutOptionsAreSentWithRequests(): void
    {
        $history = [];
        $api = new DocumentApi($this->client([
            new Response(200, ['Content-Type' => 'application/json'], '{}'),
        ], $history), $this->config());

        $api->getDocument('doc_123');

        self::assertSame(10.0, $history[0]['options'][RequestOptions::CONNECT_TIMEOUT]);
        self::assertSame(30.0, $history[0]['options'][RequestOptions::TIMEOUT]);
    }

    public function testTimeoutTransportFailuresRaiseTimeoutError(): void
    {
        $request = new Request('GET', 'https://www.signwell.test/api/v1/documents/doc_123?api_key=secret');
        $api = new DocumentApi($this->client([
            new ConnectException('cURL error 28: Operation timed out', $request, null, ['errno' => 28]),
        ]), $this->config());

        try {
            $api->getDocument('doc_123');
            self::fail('Expected timeout error.');
        } catch (ApiTimeoutError $error) {
            self::assertStringContainsString('https://www.signwell.test/api/v1/documents/doc_123', $error->getMessage());
            self::assertStringNotContainsString('api_key=secret', $error->getMessage());
        }
    }

    public function testResponseLessRequestExceptionsRaiseConnectionError(): void
    {
        $request = new Request('GET', 'https://www.signwell.test/api/v1/documents/doc_123');
        $api = new DocumentApi($this->client([
            new RequestException('Connection refused', $request),
        ]), $this->config());

        $this->expectException(ApiConnectionError::class);

        $api->getDocument('doc_123');
    }

    public function testHttpErrorsFalseStillRaisesTypedStatusErrors(): void
    {
        $client = new Client([
            'handler' => HandlerStack::create(new MockHandler([
                new Response(404, ['Content-Type' => 'application/json'], '{"message":"not found"}'),
                new Response(429, ['Content-Type' => 'application/json'], '{"message":"rate limited"}'),
            ])),
            RequestOptions::HTTP_ERRORS => false,
        ]);
        $api = new DocumentApi($client, $this->config());

        try {
            $api->getDocument('missing');
            self::fail('Expected 404 to raise typed error.');
        } catch (NotFoundError $error) {
            self::assertSame(404, $error->getCode());
        }

        try {
            $api->getDocument('limited');
            self::fail('Expected 429 to raise typed error.');
        } catch (RateLimitError $error) {
            self::assertSame(429, $error->getCode());
        }
    }

    public function testDebugOutputRedactsSecretsAndOmitsQueryStrings(): void
    {
        $debugFile = tempnam(sys_get_temp_dir(), 'signwell-debug-');
        self::assertIsString($debugFile);

        try {
            $config = $this->config()
                ->setDebug(true)
                ->setDebugFile($debugFile);
            $api = new DocumentApi($this->client([
                new Response(200, ['Content-Type' => 'application/json'], '{"debug_body_marker":true}'),
            ]), $config);

            $api->listDocuments(page: 2, limit: 25);

            $debugOutput = (string) file_get_contents($debugFile);
            self::assertStringContainsString('[REDACTED]', $debugOutput);
            self::assertStringNotContainsString('test-key', $debugOutput);
            self::assertStringNotContainsString('page=2', $debugOutput);
            self::assertStringNotContainsString('limit=25', $debugOutput);
            self::assertStringNotContainsString('debug_body_marker', $debugOutput);
        } finally {
            if (is_string($debugFile) && file_exists($debugFile)) {
                unlink($debugFile);
            }
        }
    }

    /**
     * @param list<Response|\Throwable> $responses
     * @param array|\ArrayAccess<int, array>|null $history
     */
    private function client(array $responses, array|\ArrayAccess|null &$history = null): Client
    {
        $mock = new MockHandler($responses);
        $stack = HandlerStack::create($mock);
        if ($history !== null) {
            $stack->push(Middleware::history($history));
        }

        return new Client(['handler' => $stack]);
    }

    private function config(): Configuration
    {
        return (new Configuration())
            ->setHost('https://www.signwell.test')
            ->setApiKey('X-Api-Key', 'test-key');
    }
}
