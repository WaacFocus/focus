<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class Smtp2goService
{
    public function send(string $toAddress, string $toName, string $subject, string $htmlBody, ?string $fromName = null): bool
    {
        $senderName = $fromName ?? config('services.smtp2go.from_name');
        $response = Http::asJson()->post('https://api.smtp2go.com/v3/email/send', [
            'api_key' => config('services.smtp2go.key'),
            'to'      => ["{$toName} <{$toAddress}>"],
            'sender'  => $senderName . ' <' . config('services.smtp2go.from_address') . '>',
            'subject' => $subject,
            'html_body' => $htmlBody,
        ]);

        if (!$response->successful() || ($response->json('data.succeeded') ?? 0) < 1) {
            Log::warning('SMTP2GO send failed', [
                'to'     => $toAddress,
                'status' => $response->status(),
                'body'   => $response->json(),
            ]);
            return false;
        }

        return true;
    }

    public function sendWithAttachment(
        string $toAddress, string $toName, string $subject, string $htmlBody,
        string $filename, string $fileContent, string $mimeType = 'application/pdf',
        ?string $fromName = null
    ): bool {
        $senderName = $fromName ?? config('services.smtp2go.from_name');
        $response = Http::asJson()->post('https://api.smtp2go.com/v3/email/send', [
            'api_key'   => config('services.smtp2go.key'),
            'to'        => ["{$toName} <{$toAddress}>"],
            'sender'    => $senderName . ' <' . config('services.smtp2go.from_address') . '>',
            'subject'   => $subject,
            'html_body' => $htmlBody,
            'attachments' => [
                [
                    'filename' => $filename,
                    'fileblob' => base64_encode($fileContent),
                    'mimetype' => $mimeType,
                ],
            ],
        ]);

        if (!$response->successful() || ($response->json('data.succeeded') ?? 0) < 1) {
            Log::warning('SMTP2GO sendWithAttachment failed', [
                'to'     => $toAddress,
                'status' => $response->status(),
                'body'   => $response->json(),
            ]);
            return false;
        }

        return true;
    }
}
