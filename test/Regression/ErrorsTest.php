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
use SignWell\Sdk\Errors\PermissionDeniedError;
use SignWell\Sdk\Errors\RateLimitError;
use SignWell\Sdk\Errors\TransportError;
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

    public function testForbiddenResponsesRaisePermissionDeniedAlias(): void
    {
        $api = new DocumentApi($this->client([
            new Response(403, ['Content-Type' => 'application/json'], '{"message":"forbidden"}'),
        ]), $this->config());

        try {
            $api->getDocument('forbidden');
            self::fail('Expected permission denied error.');
        } catch (PermissionDeniedError $error) {
            self::assertInstanceOf(\SignWell\Sdk\Errors\ForbiddenError::class, $error);
            self::assertSame(403, $error->getCode());
        }
    }

    public function testRateLimitMetadataParsesHeaders(): void
    {
        $api = new DocumentApi($this->client([
            new Response(429, [
                'x-ratelimit-limit' => ['100'],
                'x-ratelimit-remaining' => ['0'],
                'x-ratelimit-reset' => ['1893456000'],
                'retry-after' => ['30'],
            ], '{"message":"rate limited"}'),
        ]), $this->config());

        try {
            $api->getDocument('limited');
            self::fail('Expected rate limit error.');
        } catch (RateLimitError $error) {
            $rateLimit = $error->getRateLimit();
            self::assertNotNull($rateLimit);
            self::assertSame(100.0, $rateLimit->limit);
            self::assertSame(0.0, $rateLimit->remaining);
            self::assertSame(1893456000.0, $rateLimit->reset);
            self::assertSame(30.0, $rateLimit->retryAfter);
            self::assertNotNull($rateLimit->resetAt);
        }
    }

    public function testErrorCompatibilityAliasesResolve(): void
    {
        self::assertTrue(class_exists(PermissionDeniedError::class));
        self::assertTrue(class_exists(\SignWell\Sdk\PermissionDeniedError::class));
        self::assertTrue(is_subclass_of(PermissionDeniedError::class, \SignWell\Sdk\Errors\ForbiddenError::class));
        self::assertTrue(is_subclass_of(TransportError::class, ApiConnectionError::class));
        self::assertTrue(class_exists(\SignWell\Sdk\TransportError::class));
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
