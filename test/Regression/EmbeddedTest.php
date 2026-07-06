<?php

declare(strict_types=1);

namespace SignWell\Sdk\Test\Regression;

use PHPUnit\Framework\TestCase;
use SignWell\Sdk\Embedded;
use SignWell\Sdk\Models\DocumentRequest;
use SignWell\Sdk\Models\DocumentResponse;
use SignWell\Sdk\ObjectSerializer;
use SignWell\Sdk\Resources\DocumentApi;

final class EmbeddedTest extends TestCase
{
    public function testCreateSigningDocumentBuildsEmbeddedRequest(): void
    {
        $api = new class () extends DocumentApi {
            public DocumentRequest $captured;

            public function createDocument($document_request, string $contentType = self::contentTypes['createDocument'][0])
            {
                $this->captured = $document_request;

                return new DocumentResponse(['id' => 'doc_123']);
            }
        };

        $result = Embedded::createSigningDocument([
            'name' => 'NDA',
            'files' => [['name' => 'nda.pdf', 'file_url' => 'https://example.com/nda.pdf']],
            'recipients' => [['name' => 'Jane Doe', 'email' => 'jane@example.com']],
            'fields' => [[['page' => 1, 'x' => 20, 'y' => 60, 'type' => 'signature']]],
        ], $api);

        self::assertInstanceOf(DocumentResponse::class, $result);
        self::assertTrue($api->captured->getEmbeddedSigning());
        self::assertFalse($api->captured->getEmbeddedSigningNotifications());
        self::assertSame('1', $api->captured->getRecipients()[0]->getId());
        self::assertSame('1', $api->captured->getFields()[0][0]->getRecipientId());
        self::assertTrue($api->captured->getFields()[0][0]->getRequired());
    }

    public function testRejectsFieldlessEmbeddedSigningUnlessSignaturePageOrTextTagsAreUsed(): void
    {
        $api = new class () extends DocumentApi {
            public function createDocument($document_request, string $contentType = self::contentTypes['createDocument'][0])
            {
                return new DocumentResponse(['id' => 'doc_123']);
            }
        };

        $params = [
            'name' => 'NDA',
            'files' => [['name' => 'nda.pdf', 'file_url' => 'https://example.com/nda.pdf']],
            'recipients' => [['name' => 'Jane Doe', 'email' => 'jane@example.com']],
        ];

        try {
            Embedded::createSigningDocument($params, $api);
            self::fail('Expected field placement validation error');
        } catch (\InvalidArgumentException $error) {
            self::assertStringContainsString('must include fields', $error->getMessage());
        }

        self::assertInstanceOf(DocumentResponse::class, Embedded::createSigningDocument($params + ['with_signature_page' => true], $api));
        self::assertInstanceOf(DocumentResponse::class, Embedded::createSigningDocument($params + ['text_tags' => true], $api));
    }

    public function testNormalizesBase64UploadsBeforeSerialization(): void
    {
        $api = new class () extends DocumentApi {
            public DocumentRequest $captured;

            public function createDocument($document_request, string $contentType = self::contentTypes['createDocument'][0])
            {
                $this->captured = $document_request;

                return new DocumentResponse(['id' => 'doc_123']);
            }
        };
        $pdf = self::minimalPdf();
        $base64WithWhitespace = chunk_split(base64_encode($pdf), 16, "\n");

        Embedded::createSigningDocument([
            'name' => 'NDA',
            'files' => [['name' => 'nda.pdf', 'file_base64' => $base64WithWhitespace]],
            'recipients' => [['name' => 'Jane Doe', 'email' => 'jane@example.com']],
            'fields' => [[['page' => 1, 'x' => 20, 'y' => 60, 'type' => 'signature']]],
        ], $api);

        $payload = json_decode(json_encode(ObjectSerializer::sanitizeForSerialization($api->captured), JSON_THROW_ON_ERROR), true, flags: JSON_THROW_ON_ERROR);

        self::assertSame(base64_encode($pdf), $payload['files'][0]['file_base64']);
    }

    public function testRejectsMalformedPdfBase64Uploads(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('complete PDF');

        Embedded::createSigningDocument([
            'name' => 'NDA',
            'files' => [['name' => 'nda.pdf', 'file_base64' => base64_encode("%PDF-1.4\n% not complete\n")]],
            'recipients' => [['name' => 'Jane Doe', 'email' => 'jane@example.com']],
            'fields' => [[['page' => 1, 'x' => 20, 'y' => 60, 'type' => 'signature']]],
        ]);
    }

    public function testExtractsSigningUrlsAndRendersSafeIframe(): void
    {
        $document = [
            'recipients' => [
                ['email' => 'jane@example.com', 'embedded_signing_url' => 'https://www.signwell.com/docs/abc'],
                ['email' => 'missing@example.com', 'embedded_signing_url' => null],
            ],
        ];

        self::assertSame('https://www.signwell.com/docs/abc', Embedded::embeddedSigningUrl($document));
        self::assertSame(['jane@example.com' => 'https://www.signwell.com/docs/abc'], Embedded::embeddedSigningUrls($document));
        self::assertSame('<script src="https://static.signwell.com/assets/embedded.js"></script>', Embedded::scriptTag());
        self::assertSame(
            '<script nonce="abc&#039;&quot;&amp;&lt;&gt;" src="https://static.signwell.com/assets/embedded.js"></script>',
            Embedded::scriptTag('abc\'"&<>')
        );

        $html = Embedded::signingIframe([
            'url' => 'https://www.signwell.com/docs/abc',
            'nonce' => 'abc123',
            'events' => ['completed' => 'SignWellHandlers.completed'],
        ]);

        self::assertStringContainsString('<script nonce="abc123">', $html);
        self::assertStringContainsString('new SignWellEmbed', $html);
        self::assertStringContainsString('SignWellHandlers.completed', $html);

        $requestingHtml = Embedded::requestingIframe([
            'url' => 'https://www.signwell.com/embedded/request/abc',
            'nonce' => 'abc\'"&<>',
        ]);
        self::assertStringContainsString('<script nonce="abc&#039;&quot;&amp;&lt;&gt;">', $requestingHtml);
    }

    public function testIframeRejectsUnsafeUrlsAndHandlerPaths(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        Embedded::signingIframe(['url' => 'http://www.signwell.com/docs/abc']);
    }

    public function testIframeRejectsPrototypeHandlerPath(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        Embedded::signingIframe([
            'url' => 'https://www.signwell.com/docs/abc',
            'events' => ['completed' => 'SignWellHandlers.__proto__.completed'],
        ]);
    }

    private static function minimalPdf(): string
    {
        $content = "BT\n/F1 18 Tf\n72 720 Td\n(SignWell PHP SDK upload regression) Tj\nET\n";
        $objects = [
            "1 0 obj\n<< /Type /Catalog /Pages 2 0 R >>\nendobj\n",
            "2 0 obj\n<< /Type /Pages /Kids [3 0 R] /Count 1 >>\nendobj\n",
            "3 0 obj\n<< /Type /Page /Parent 2 0 R /MediaBox [0 0 612 792] /Resources << /Font << /F1 4 0 R >> >> /Contents 5 0 R >>\nendobj\n",
            "4 0 obj\n<< /Type /Font /Subtype /Type1 /BaseFont /Helvetica >>\nendobj\n",
            "5 0 obj\n<< /Length " . strlen($content) . " >>\nstream\n" . $content . "endstream\nendobj\n",
        ];

        $pdf = "%PDF-1.4\n";
        $offsets = [];
        foreach ($objects as $object) {
            $offsets[] = strlen($pdf);
            $pdf .= $object;
        }

        $xrefOffset = strlen($pdf);
        $pdf .= "xref\n0 " . (count($objects) + 1) . "\n0000000000 65535 f \n";
        foreach ($offsets as $offset) {
            $pdf .= sprintf("%010d 00000 n \n", $offset);
        }
        $pdf .= "trailer\n<< /Size " . (count($objects) + 1) . " /Root 1 0 R >>\nstartxref\n" . $xrefOffset . "\n%%EOF\n";

        return $pdf;
    }
}
