<?php

declare(strict_types=1);

namespace SignWell\Sdk\Test\Regression;

use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase;
use SignWell\Sdk\ApiException;
use SignWell\Sdk\Configuration;
use SignWell\Sdk\Errors\UnsupportedContentTypeError;
use SignWell\Sdk\Models\BulkSendCsvTemplateResponse;
use SignWell\Sdk\Models\CompletedPdfUrlResponse;
use SignWell\Sdk\Models\Nom151CertificateResponse;
use SignWell\Sdk\Models\Nom151UrlResponse;
use SignWell\Sdk\Resources\BulkSendApi;
use SignWell\Sdk\Resources\DocumentApi;
use SignWell\Sdk\Resources\RegionalApi;

final class ResponseModesTest extends TestCase
{
    public function testRateLimitRetriesReplayPostsAndKeepTypedErrors(): void
    {
        foreach ([[429, '0', 2, 3], [429, '0', 0, 1], [429, '120', 2, 1], [500, '0', 2, 1]] as [$status, $header, $limit, $expected]) {
            $history = [];
            $responses = array_map(static fn ($unused) => new Response($status, ['Content-Type' => 'application/json', 'Retry-After' => $header], '{}'), range(1, $expected));
            $api = new DocumentApi($this->client($responses, $history), $this->config()->setMaxRateLimitRetries($limit));
            try {
                $api->createDocument(new \SignWell\Sdk\Models\DocumentRequest(['name' => 'Retry document', 'test_mode' => true]));
                self::fail('Expected status error');
            } catch (ApiException $error) {
                self::assertSame($status, $error->getCode());
            }
            self::assertCount($expected, $history);
            self::assertCount(1, array_unique(array_map(static fn ($entry) => (string) $entry['request']->getBody(), $history)));
        }
    }

    public function testRetryDownloadAndAsyncRequestsDiscardRejectedStreams(): void
    {
        $rejected = new \GuzzleHttp\Psr7\Response(429, ['Retry-After' => '0'], 'limited');
        $api = new DocumentApi($this->client([$rejected, new Response(200, ['Content-Type' => 'application/pdf'], '%PDF')]), $this->config());
        $file = $api->getCompletedPdf('doc_1');
        self::assertSame('%PDF', $file->fread(4));
        self::assertFalse($rejected->getBody()->isReadable());
        $api = new DocumentApi($this->client([new Response(429, ['Retry-After' => '0'], '{}'), new Response(200, ['Content-Type' => 'application/json'], '{"id":"doc_1"}')]), $this->config());
        self::assertSame('doc_1', $api->getDocumentAsync('doc_1')->wait()->getId());
    }

    public function testBulkSendCsvTemplateDefaultsToBinaryMode(): void
    {
        $history = [];
        $api = new BulkSendApi($this->client([new Response(200, [
            'Content-Type' => 'application/octet-stream',
            'content-disposition' => ['inline; filename=template.csv'],
        ], 'csv')], $history), $this->config());

        $result = $api->getBulkSendCsvTemplate(['00000000-0000-0000-0000-000000000000']);

        self::assertInstanceOf(\SplFileObject::class, $result);
        self::assertSame('csv', $result->fread(3));
        self::assertSame('application/octet-stream', $history[0]['request']->getHeaderLine('Accept'));
    }

    public function testBulkSendCsvTemplateUsesJsonForBase64Mode(): void
    {
        $history = [];
        $api = new BulkSendApi($this->client([
            new Response(200, ['Content-Type' => 'application/json'], '{"csv_template_base64":"Y3N2"}'),
        ], $history), $this->config());

        $result = $api->getBulkSendCsvTemplate(['00000000-0000-0000-0000-000000000000'], true);

        self::assertInstanceOf(BulkSendCsvTemplateResponse::class, $result);
        self::assertSame('application/json', $history[0]['request']->getHeaderLine('Accept'));
    }

    public function testCompletedPdfDefaultsToBinaryMode(): void
    {
        $history = [];
        $api = new DocumentApi($this->client([new Response(200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => ['inline; filename=../../document.pdf'],
        ], '%PDF')], $history), $this->config());

        $result = $api->getCompletedPdf('doc_123');

        self::assertInstanceOf(\SplFileObject::class, $result);
        self::assertSame('%PDF', $result->fread(4));
        self::assertSame('document.pdf', basename($result->getPathname()));
        self::assertSame('application/octet-stream', $history[0]['request']->getHeaderLine('Accept'));
    }

    public function testCompletedPdfUsesJsonForUrlOnlyMode(): void
    {
        $history = [];
        $api = new DocumentApi($this->client([
            new Response(200, ['Content-Type' => 'application/json'], '{"file_url":"https://example.com/document.pdf"}'),
        ], $history), $this->config());

        $result = $api->getCompletedPdf('doc_123', true);

        self::assertInstanceOf(CompletedPdfUrlResponse::class, $result);
        self::assertSame('https://example.com/document.pdf', $result->getFileUrl());
        self::assertSame('application/json', $history[0]['request']->getHeaderLine('Accept'));
    }

    public function testNom151ResponseModesAndConflictValidation(): void
    {
        $api = new RegionalApi($this->client([
            new Response(200, [
                'Content-Type' => 'application/octet-stream',
                'Content-Disposition' => ['inline; filename=nom151.zip'],
            ], 'zip'),
            new Response(200, ['Content-Type' => 'application/json'], '{"file_url":"https://example.com/nom151.zip"}'),
            new Response(200, ['Content-Type' => 'application/json'], '{"nom151":{"status":"issued"}}'),
        ]), $this->config());

        self::assertInstanceOf(\SplFileObject::class, $api->getNom151Certificate('doc_123'));
        self::assertInstanceOf(Nom151UrlResponse::class, $api->getNom151Certificate('doc_123', true));
        self::assertInstanceOf(Nom151CertificateResponse::class, $api->getNom151Certificate('doc_123', false, true));

        $this->expectException(\InvalidArgumentException::class);
        $api->getNom151Certificate('doc_123', true, true);
    }

    public function testUnsupportedJsonContentTypeRaisesTypedError(): void
    {
        $api = new DocumentApi($this->client([
            new Response(200, ['Content-Type' => 'text/plain'], '{"file_url":"https://example.com/document.pdf"}'),
        ]), $this->config());

        $this->expectException(UnsupportedContentTypeError::class);
        $api->getCompletedPdf('doc_123', true);
    }

    public function testApiExceptionMessagesDoNotExposeQueryStrings(): void
    {
        $api = new DocumentApi($this->client([
            new Response(500, ['Content-Type' => 'application/json'], '{"error":"failed"}'),
        ]), $this->config());

        try {
            $api->listDocuments(page: 2, limit: 25);
            self::fail('Expected API exception');
        } catch (ApiException $error) {
            self::assertStringContainsString('https://www.signwell.test/api/v1/documents', $error->getMessage());
            self::assertStringNotContainsString('?', $error->getMessage());
            self::assertStringNotContainsString('page=2', $error->getMessage());
            self::assertStringNotContainsString('limit=25', $error->getMessage());
        }
    }

    /**
     * @param list<Response> $responses
     * @param array<array{request: \Psr\Http\Message\RequestInterface, response: \Psr\Http\Message\ResponseInterface|null, error: mixed, options: array<mixed>}>|\ArrayAccess<int, array{request: \Psr\Http\Message\RequestInterface, response: \Psr\Http\Message\ResponseInterface|null, error: mixed, options: array<mixed>}>|null $history
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
