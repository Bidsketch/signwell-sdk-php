<?php

declare(strict_types=1);

namespace SignWell\Sdk\Test\Regression;

use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase;
use SignWell\Sdk\Configuration;
use SignWell\Sdk\Errors\WaitForCompletionTimeoutError;
use SignWell\Sdk\Models\AdditionalFieldsInnerInnerValue;
use SignWell\Sdk\Models\DocumentResponse;
use SignWell\Sdk\Models\DocumentResponseFieldsInnerInnerValue;
use SignWell\Sdk\Models\DropdownOption;
use SignWell\Sdk\Models\TemplateFieldValuesInnerValue;
use SignWell\Sdk\Models\UpdateDocumentAndSendRequest;
use SignWell\Sdk\ObjectSerializer;
use SignWell\Sdk\Resources\DocumentApi;
use SignWell\Sdk\Resources\TemplateApi;

final class GeneratedResourceHelpersTest extends TestCase
{
    public function testPrimitiveUnionsPreserveResponseValuesAndRoundTrip(): void
    {
        foreach ([AdditionalFieldsInnerInnerValue::class, DocumentResponseFieldsInnerInnerValue::class, TemplateFieldValuesInnerValue::class] as $class) {
            foreach (['t', 'f', 'approved', '123', '', true, false, 0, 12.5] as $value) {
                /** @var AdditionalFieldsInnerInnerValue|DocumentResponseFieldsInnerInnerValue|TemplateFieldValuesInnerValue $model */
                $model = ObjectSerializer::deserialize($value, $class);
                self::assertTrue($model->hasActualInstance());
                self::assertSame($value, $model->getActualInstance());
                self::assertSame($value, ObjectSerializer::sanitizeForSerialization($model));
            }
        }
        $documents = new DocumentApi($this->client([
            new Response(200, ['Content-Type' => 'application/json'], '{"id":"doc_1","fields":[[{"type":"checkbox","value":"t"},{"type":"checkbox","value":"f"},{"type":"text","value":"123"}]]}'),
        ]), $this->config());
        $document = $documents->getDocument('doc_1');
        self::assertInstanceOf(DocumentResponse::class, $document);
        $fields = $document->getFields()[0];
        self::assertSame('t', $fields[0]->getValue()->getActualInstance());
        self::assertSame('f', $fields[1]->getValue()->getActualInstance());
        self::assertSame('123', $fields[2]->getValue()->getActualInstance());
    }

    public function testDropdownOptionsAcceptStringsAndDetailedObjects(): void
    {
        $simple = ObjectSerializer::deserialize('A', DropdownOption::class);
        self::assertInstanceOf(DropdownOption::class, $simple);
        self::assertSame('A', ObjectSerializer::sanitizeForSerialization($simple));
        $detailed = ObjectSerializer::deserialize((object) ['name' => 'B', 'api_id' => 'b'], DropdownOption::class);
        self::assertInstanceOf(DropdownOption::class, $detailed);
        self::assertSame('B', $detailed->getName());
        self::assertSame('b', $detailed->getApiId());
        $serialized = ObjectSerializer::sanitizeForSerialization($detailed);
        self::assertIsObject($serialized);
        self::assertSame('B', $serialized->name);
    }

    public function testListResourcesSerializeRawQueryFilters(): void
    {
        $history = [];
        $documents = new DocumentApi($this->client([
            new Response(200, ['Content-Type' => 'application/json'], '{"documents":[],"current_page":1,"next_page":null,"total_count":0,"total_pages":1}'),
        ], $history), $this->config());

        $documents->listDocuments(page: 2, limit: 25, query: 'name:Classic AND status:completed');

        parse_str($history[0]['request']->getUri()->getQuery(), $documentQuery);
        self::assertSame('name:Classic AND status:completed', $documentQuery['query']);
        self::assertSame('2', $documentQuery['page']);
        self::assertSame('25', $documentQuery['limit']);

        $history = [];
        $templates = new TemplateApi($this->client([
            new Response(200, ['Content-Type' => 'application/json'], '{"templates":[],"current_page":1,"next_page":null,"total_count":0,"total_pages":1}'),
        ], $history), $this->config());

        $templates->listTemplates(page: 3, limit: 10, query: 'name:NDA AND archived:false');

        parse_str($history[0]['request']->getUri()->getQuery(), $templateQuery);
        self::assertSame('name:NDA AND archived:false', $templateQuery['query']);
        self::assertSame('3', $templateQuery['page']);
        self::assertSame('10', $templateQuery['limit']);
    }

    public function testDocumentAndTemplatePaginationIteratorsYieldPagesAndItems(): void
    {
        $documentHistory = [];
        $documents = new DocumentApi($this->client([
            new Response(200, ['Content-Type' => 'application/json'], '{"documents":[{"id":"doc_1"}],"current_page":1,"next_page":2,"total_count":2,"total_pages":2}'),
            new Response(200, ['Content-Type' => 'application/json'], '{"documents":[{"id":"doc_2"}],"current_page":2,"next_page":null,"total_count":2,"total_pages":2}'),
            new Response(200, ['Content-Type' => 'application/json'], '{"documents":[{"id":"doc_1"}],"current_page":1,"next_page":2,"total_count":2,"total_pages":2}'),
            new Response(200, ['Content-Type' => 'application/json'], '{"documents":[{"id":"doc_2"}],"current_page":2,"next_page":null,"total_count":2,"total_pages":2}'),
        ], $documentHistory), $this->config());

        $pages = iterator_to_array($documents->iterateDocumentPages(query: 'status:Completed'), false);
        $items = iterator_to_array($documents->iterateDocuments(query: 'status:Completed'), false);

        self::assertSame([1, 2], array_map(static fn ($page): ?int => $page->getCurrentPage(), $pages));
        self::assertSame(['doc_1', 'doc_2'], array_map(static fn ($document): ?string => $document->getId(), $items));
        self::assertSame(['1', '2', '1', '2'], array_map(static function (array $entry): string {
            parse_str($entry['request']->getUri()->getQuery(), $query);

            return (string) $query['page'];
        }, $documentHistory));

        $templates = new TemplateApi($this->client([
            new Response(200, ['Content-Type' => 'application/json'], '{"templates":[{"id":"tpl_1"}],"current_page":1,"next_page":2,"total_count":2,"total_pages":2}'),
            new Response(200, ['Content-Type' => 'application/json'], '{"templates":[{"id":"tpl_2"}],"current_page":2,"next_page":null,"total_count":2,"total_pages":2}'),
        ]), $this->config());

        $templateItems = iterator_to_array($templates->iterateTemplates(query: 'name:NDA'), false);
        self::assertSame(['tpl_1', 'tpl_2'], array_map(static fn ($template): ?string => $template->getId(), $templateItems));
    }

    public function testUpdateDocumentAliasDelegatesToSendDocumentOperation(): void
    {
        $history = [];
        $documents = new DocumentApi($this->client([
            new Response(200, ['Content-Type' => 'application/json'], '{"id":"doc_123"}'),
        ], $history), $this->config());

        $result = $documents->updateDocument('doc_123', new UpdateDocumentAndSendRequest(['subject' => 'Updated']));

        self::assertInstanceOf(DocumentResponse::class, $result);
        self::assertStringContainsString('/api/v1/documents/doc_123/send', (string) $history[0]['request']->getUri());
    }

    public function testWaitForCompletionReturnsTerminalDocumentAndTimesOut(): void
    {
        $documents = new DocumentApi($this->client([
            new Response(200, ['Content-Type' => 'application/json'], '{"id":"doc_123","status":"In Progress"}'),
            new Response(200, ['Content-Type' => 'application/json'], '{"id":"doc_123","status":"Completed"}'),
        ]), $this->config());

        $result = $documents->waitForCompletion('doc_123', ['intervalMs' => 0, 'maxAttempts' => 3]);
        self::assertSame('Completed', $result->getStatus());

        $documents = new DocumentApi($this->client([
            new Response(200, ['Content-Type' => 'application/json'], '{"id":"doc_123","status":"In Progress"}'),
        ]), $this->config());

        try {
            $documents->waitForCompletion('doc_123', ['intervalMs' => 0, 'maxAttempts' => 1]);
            self::fail('Expected wait helper timeout.');
        } catch (WaitForCompletionTimeoutError $error) {
            self::assertInstanceOf(DocumentResponse::class, $error->lastDocument);
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
