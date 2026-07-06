<?php

declare(strict_types=1);

namespace SignWell\Sdk\Test\Regression;

use PHPUnit\Framework\TestCase;
use SignWell\Sdk\Client;
use SignWell\Sdk\Configuration;
use SignWell\Sdk\Embedded;
use SignWell\Sdk\Errors\NotFoundError;
use SignWell\Sdk\Laravel\Facades\SignWell as SignWellFacade;
use SignWell\Sdk\Laravel\SignWellManager;
use SignWell\Sdk\Models\BulkSendCreateResponse;
use SignWell\Sdk\Models\BulkSendCsvRequest;
use SignWell\Sdk\Models\BulkSendCsvTemplateResponse;
use SignWell\Sdk\Models\BulkSendValidateCsvResponse;
use SignWell\Sdk\Models\CompletedPdfUrlResponse;
use SignWell\Sdk\Models\CreateBulkSendRequest;
use SignWell\Sdk\Models\DocumentFromTemplateRequest;
use SignWell\Sdk\Models\DocumentFromTemplateResponse;
use SignWell\Sdk\Models\DocumentRequest;
use SignWell\Sdk\Models\DocumentResponse;
use SignWell\Sdk\Models\DocumentTemplateRequest;
use SignWell\Sdk\Models\DocumentTemplateResponse;
use SignWell\Sdk\Models\DocumentTemplateUpdateRequest;
use SignWell\Sdk\Models\MeResponse;
use SignWell\Sdk\Models\SendReminderRequest;
use SignWell\Sdk\Models\UpdateDocumentAndSendRequest;
use SignWell\Sdk\Resources\BulkSendApi;
use SignWell\Sdk\Resources\DocumentApi;
use SignWell\Sdk\Resources\MeApi;
use SignWell\Sdk\Resources\TemplateApi;
use SignWell\Sdk\Webhook;
use SignWell\Sdk\Webhook\ReplayStoreCapacityExceededException;

final class ExportsTest extends TestCase
{
    public function testPublicClassesAutoload(): void
    {
        self::assertTrue(class_exists(Client::class));
        self::assertTrue(class_exists(DocumentApi::class));
        self::assertTrue(class_exists(DocumentRequest::class));
        self::assertTrue(class_exists(NotFoundError::class));
        self::assertTrue(class_exists(ReplayStoreCapacityExceededException::class));
        self::assertSame('https://static.signwell.com/assets/embedded.js', Embedded::SCRIPT_URL);
        self::assertTrue(method_exists(Webhook::class, 'verifyEvent'));
    }

    public function testClientBuildsConfiguredResources(): void
    {
        $client = Client::fromApiKey('test-key', host: 'https://api.example.test');

        self::assertSame('test-key', $client->configuration()->getApiKey('X-Api-Key'));
        self::assertSame('https://api.example.test', $client->configuration()->getHost());
        self::assertSame($client->configuration(), $client->documents()->getConfig());
        self::assertSame($client->configuration(), $client->templates()->getConfig());
        self::assertSame($client->configuration(), $client->bulkSends()->getConfig());
        self::assertSame($client->configuration(), $client->regional()->getConfig());
        self::assertSame($client->configuration(), $client->me()->getConfig());
        self::assertSame($client->configuration(), $client->apiApplications()->getConfig());
    }

    public function testClientEmbeddedHelpersUseConfiguredDocumentApi(): void
    {
        $api = new class () extends DocumentApi {
            public DocumentRequest $captured;

            public function createDocument($document_request, string $contentType = self::contentTypes['createDocument'][0])
            {
                $this->captured = $document_request;

                return new DocumentResponse(['id' => 'doc_123']);
            }
        };
        $client = new class (new Configuration(), $api) extends Client {
            public function __construct(Configuration $configuration, private readonly DocumentApi $documentApi)
            {
                parent::__construct($configuration);
            }

            public function documents(): DocumentApi
            {
                return $this->documentApi;
            }
        };

        $result = $client->createSigningDocument([
            'name' => 'NDA',
            'files' => [['name' => 'nda.pdf', 'file_url' => 'https://example.com/nda.pdf']],
            'recipients' => [['name' => 'Jane Doe', 'email' => 'jane@example.com']],
            'fields' => [[['page' => 1, 'x' => 20, 'y' => 60, 'type' => 'signature']]],
        ]);

        self::assertInstanceOf(DocumentResponse::class, $result);
        self::assertTrue($api->captured->getEmbeddedSigning());
    }

    public function testClientWorkflowHelpersWrapCommonGeneratedRequests(): void
    {
        $documents = new class () extends DocumentApi {
            public mixed $captured;

            public function createDocument($document_request, string $contentType = self::contentTypes['createDocument'][0])
            {
                $this->captured = $document_request;
                return new DocumentResponse(['id' => 'doc_123']);
            }

            public function createDocumentFromTemplate($document_from_template_request, string $contentType = self::contentTypes['createDocumentFromTemplate'][0])
            {
                $this->captured = $document_from_template_request;
                return new DocumentFromTemplateResponse(['id' => 'doc_from_template_123']);
            }

            public function sendDocument($id, $update_document_and_send_request, string $contentType = self::contentTypes['sendDocument'][0])
            {
                $this->captured = [$id, $update_document_and_send_request];
                return new DocumentResponse(['id' => $id]);
            }

            public function sendReminder($id, $send_reminder_request, string $contentType = self::contentTypes['sendReminder'][0])
            {
                $this->captured = [$id, $send_reminder_request];
            }

            public function getCompletedPdf($id, $url_only = false, $audit_page = true, $file_format = null, string $contentType = self::contentTypes['getCompletedPdf'][0])
            {
                $this->captured = [$id, $url_only, $audit_page, $file_format];
                return new CompletedPdfUrlResponse(['file_url' => 'https://example.com/completed.pdf']);
            }
        };
        $templates = new class () extends TemplateApi {
            public mixed $captured;

            public function createTemplate($document_template_request, string $contentType = self::contentTypes['createTemplate'][0])
            {
                $this->captured = $document_template_request;
                return new DocumentTemplateResponse(['id' => 'tpl_123']);
            }

            public function updateTemplate($id, $document_template_update_request, string $contentType = self::contentTypes['updateTemplate'][0])
            {
                $this->captured = [$id, $document_template_update_request];
                return new DocumentTemplateResponse(['id' => $id]);
            }
        };
        $bulkSends = new class () extends BulkSendApi {
            public mixed $captured;

            public function createBulkSend($create_bulk_send_request, string $contentType = self::contentTypes['createBulkSend'][0])
            {
                $this->captured = $create_bulk_send_request;
                return new BulkSendCreateResponse(['id' => 'bulk_123']);
            }

            public function validateBulkSendCsv($bulk_send_csv_request, string $contentType = self::contentTypes['validateBulkSendCsv'][0])
            {
                $this->captured = $bulk_send_csv_request;
                return new BulkSendValidateCsvResponse(['valid' => true]);
            }

            public function getBulkSendCsvTemplate($template_ids, $base64 = null, string $contentType = self::contentTypes['getBulkSendCsvTemplate'][0])
            {
                $this->captured = [$template_ids, $base64];
                return new BulkSendCsvTemplateResponse(['template_csv' => 'YQ==']);
            }
        };
        $me = new class () extends MeApi {
            public function getMe(string $contentType = self::contentTypes['getMe'][0])
            {
                return new MeResponse();
            }
        };
        $client = new class (new Configuration(), $documents, $templates, $bulkSends, $me) extends Client {
            public function __construct(
                Configuration $configuration,
                private readonly DocumentApi $documentApi,
                private readonly TemplateApi $templateApi,
                private readonly BulkSendApi $bulkSendApi,
                private readonly MeApi $meApi
            ) {
                parent::__construct($configuration);
            }

            public function documents(): DocumentApi
            {
                return $this->documentApi;
            }

            public function templates(): TemplateApi
            {
                return $this->templateApi;
            }

            public function bulkSends(): BulkSendApi
            {
                return $this->bulkSendApi;
            }

            public function me(): MeApi
            {
                return $this->meApi;
            }
        };

        self::assertInstanceOf(DocumentResponse::class, $client->createDocument(['name' => 'NDA']));
        self::assertInstanceOf(DocumentRequest::class, $documents->captured);
        self::assertInstanceOf(DocumentFromTemplateResponse::class, $client->createDocumentFromTemplate(['template_id' => 'tpl_123']));
        self::assertInstanceOf(DocumentFromTemplateRequest::class, $documents->captured);
        self::assertInstanceOf(DocumentResponse::class, $client->sendDocument('doc_123', ['subject' => 'Send']));
        self::assertInstanceOf(UpdateDocumentAndSendRequest::class, $documents->captured[1]);
        $client->sendReminder('doc_123');
        self::assertInstanceOf(SendReminderRequest::class, $documents->captured[1]);
        self::assertInstanceOf(CompletedPdfUrlResponse::class, $client->getCompletedPdf('doc_123', true, false, 'zip'));
        self::assertSame(['doc_123', true, false, 'zip'], $documents->captured);
        self::assertInstanceOf(DocumentTemplateResponse::class, $client->createTemplate(['name' => 'Template']));
        self::assertInstanceOf(DocumentTemplateRequest::class, $templates->captured);
        self::assertInstanceOf(DocumentTemplateResponse::class, $client->updateTemplate('tpl_123', ['name' => 'Updated']));
        self::assertInstanceOf(DocumentTemplateUpdateRequest::class, $templates->captured[1]);
        self::assertInstanceOf(BulkSendCreateResponse::class, $client->createBulkSend(['template_ids' => ['tpl_123'], 'bulk_send_csv' => 'YQ==']));
        self::assertInstanceOf(CreateBulkSendRequest::class, $bulkSends->captured);
        self::assertInstanceOf(BulkSendValidateCsvResponse::class, $client->validateBulkSendCsv(['template_ids' => ['tpl_123'], 'bulk_send_csv' => 'YQ==']));
        self::assertInstanceOf(BulkSendCsvRequest::class, $bulkSends->captured);
        self::assertInstanceOf(BulkSendCsvTemplateResponse::class, $client->getBulkSendCsvTemplate(['tpl_123'], true));
        self::assertSame([['tpl_123'], true], $bulkSends->captured);
        self::assertInstanceOf(MeResponse::class, $client->getMe());
    }

    public function testLaravelManagerBuildsConfiguredResources(): void
    {
        $manager = new SignWellManager((new Configuration())->setApiKey('X-Api-Key', 'test-key'));

        self::assertInstanceOf(DocumentApi::class, $manager->documents());
        self::assertSame(Embedded::class, $manager->embedded());
        self::assertFalse(method_exists($manager, 'webhook'));
    }

    public function testLaravelFacadeExposesClientConvenienceMethods(): void
    {
        $api = new class () extends DocumentApi {
            public function createDocument($document_request, string $contentType = self::contentTypes['createDocument'][0])
            {
                return new DocumentResponse(['id' => $document_request instanceof DocumentRequest ? 'doc_123' : 'invalid']);
            }
        };
        $client = new class (new Configuration(), $api) extends Client {
            public function __construct(Configuration $configuration, private readonly DocumentApi $documentApi)
            {
                parent::__construct($configuration);
            }

            public function documents(): DocumentApi
            {
                return $this->documentApi;
            }
        };
        SignWellFacade::clearResolvedInstance();
        SignWellFacade::swap($client);

        try {
            self::assertInstanceOf(DocumentApi::class, SignWellFacade::documents());
            self::assertSame('<script src="https://static.signwell.com/assets/embedded.js"></script>', SignWellFacade::scriptTag());
            self::assertInstanceOf(DocumentResponse::class, SignWellFacade::createDocument(['name' => 'NDA']));
        } finally {
            SignWellFacade::clearResolvedInstance();
        }
    }

    public function testWebhookConvenienceIsNotExposedByClientOrManager(): void
    {
        self::assertFalse(method_exists(Client::class, 'webhooksApi'));
        self::assertFalse(method_exists(Client::class, 'verifyEvent'));
        self::assertFalse(method_exists(Client::class, 'verifyEventOrThrow'));
        self::assertFalse(method_exists(Client::class, 'verifyEventOnce'));
        self::assertFalse(method_exists(Client::class, 'verifyEventOnceOrThrow'));
        self::assertFalse(method_exists(Client::class, 'replayKey'));
        self::assertFalse(method_exists(SignWellManager::class, 'webhook'));
    }
}
