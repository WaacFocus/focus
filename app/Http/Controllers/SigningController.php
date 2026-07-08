<?php

namespace App\Http\Controllers;

use App\Models\EngagementLetter;
use App\Models\User;
use App\Services\Smtp2goService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class SigningController extends Controller
{
    public function show(string $token)
    {
        $letter = EngagementLetter::where('token', $token)->firstOrFail();
        if ($letter->status === 'signed') {
            $letter->load('client');
            return view('signing.already-signed', compact('letter'));
        }
        abort_if($letter->status !== 'sent', 404);

        $letter->load('client');

        return view('signing.sign', compact('letter', 'token'));
    }

    public function sign(Request $request, string $token)
    {
        $letter = EngagementLetter::where('token', $token)->firstOrFail();
        if ($letter->status === 'signed') {
            $letter->load('client');
            return view('signing.already-signed', compact('letter'));
        }
        abort_if($letter->status !== 'sent', 404);

        $request->validate([
            'signed_name'    => 'required|string|max:255',
            'agreed'         => 'accepted',
            'signature_data' => 'required|string|max:600000',
            'signature_type' => 'required|in:drawn,typed',
        ]);

        $letter->load('client', 'renewal', 'sentBy');

        $letter->update([
            'status'            => 'signed',
            'signed_at'         => now(),
            'signed_name'       => $request->signed_name,
            'signed_ip'         => $request->ip(),
            'transaction_id'    => (string) Str::uuid(),
            'signature_image'   => $request->signature_data,
            'signature_type'    => $request->signature_type,
            'signed_user_agent' => $request->userAgent(),
        ]);

        // Update the linked renewal
        if ($letter->renewal) {
            $letter->renewal->update([
                'status'         => 'signed',
                'completed_date' => now()->toDateString(),
                'due_date'       => now()->addYear()->toDateString(),
            ]);
        }

        // Generate PDF
        $pdf    = Pdf::loadView('pdf.engagement-letter', ['letter' => $letter])->setPaper('A4', 'portrait');
        $pdfContent = $pdf->output();
        $filename   = 'engagement-letter-' . now()->format('Y-m-d') . '.pdf';

        $smtp   = app(Smtp2goService::class);
        $client = $letter->client;

        // Email client with signed PDF
        if ($client->email) {
            $smtp->sendWithAttachment(
                $client->email,
                $client->contact_name ?: $client->company_name,
                'Your Signed Engagement Letter',
                view('emails.engagement-letter-signed-client', compact('letter'))->render(),
                $filename,
                $pdfContent,
                'application/pdf',
                'Woods Accounting & Consulting'
            );
        }

        // Notify staff member who sent it — include signed PDF attachment
        $staffUser = $letter->sentBy ?? User::where('role', 'manager')->orderBy('id')->first();
        if ($staffUser?->email) {
            $smtp->sendWithAttachment(
                $staffUser->email,
                $staffUser->name,
                'Engagement Letter Signed — ' . $client->company_name,
                view('emails.engagement-letter-signed-staff', compact('letter'))->render(),
                $filename,
                $pdfContent
            );
        }

        return view('signing.complete', compact('letter'));
    }
}
