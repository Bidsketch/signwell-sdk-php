<?php

declare(strict_types=1);

function signwellExampleDocumentPayload(): array
{
    $recipientName = getenv('SIGNWELL_EXAMPLE_RECIPIENT_NAME') ?: 'Example Signer';
    $recipientEmail = getenv('SIGNWELL_EXAMPLE_RECIPIENT_EMAIL') ?: 'signer@example.com';

    return [
        'test_mode' => true,
        'name' => 'SignWell PHP SDK test-mode example ' . gmdate('c'),
        'subject' => 'SignWell PHP SDK test-mode signing request',
        'message' => 'This test-mode request was created by a SignWell PHP SDK example.',
        'files' => [
            [
                'name' => 'signwell-sdk-validation.pdf',
                'file_base64' => signwellExamplePdfBase64(),
            ],
        ],
        'recipients' => [
            [
                'id' => '1',
                'name' => $recipientName,
                'email' => $recipientEmail,
            ],
        ],
        'fields' => [
            [
                [
                    'page' => 1,
                    'x' => 120,
                    'y' => 260,
                    'type' => 'signature',
                    'recipient_id' => '1',
                    'required' => true,
                ],
            ],
        ],
    ];
}

function signwellExamplePdfBase64(): string
{
    $content = "BT\n/F1 18 Tf\n72 720 Td\n(SignWell PHP SDK validation) Tj\nET\n";
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

    return base64_encode($pdf);
}
