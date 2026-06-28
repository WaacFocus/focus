<?php

namespace App\Http\Controllers;

use App\Models\EngagementLetter;
use App\Models\User;
use App\Services\Smtp2goService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class SigningController extends Controller
{
    public function show(string $token)
    {
        $letter = EngagementLetter::where('token', $token)->firstOrFail();
        abort_if($letter->status === 'signed', 410, 'This engagement letter has already been signed.');
        abort_if($letter->status !== 'sent', 404);

        $letter->load('client');

        return view('signing.sign', compact('letter', 'token'));
    }

    public function sign(Request $request, string $token)
    {
        $letter = EngagementLetter::where('token', $token)->firstOrFail();
        abort_if($letter->status === 'signed', 410, 'This engagement letter has already been signed.');
        abort_if($letter->status !== 'sent', 404);

        $request->validate([
            'signed_name' => 'required|string|max:255',
            'agreed'      => 'accepted',
        ]);

        $letter->load('client', 'renewal', 'sentBy');

        $ip = $request->ip();

        $letter->update([
            'status'      => 'signed',
            'signed_at'   => now(),
            'signed_name' => $request->signed_name,
            'signed_ip'   => $ip,
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
                $pdfContent
            );
        }

        // Notify staff member who sent it
        $staffUser = $letter->sentBy ?? User::where('role', 'manager')->orderBy('id')->first();
        if ($staffUser?->email) {
            $smtp->send(
                $staffUser->email,
                $staffUser->name,
                'Engagement Letter Signed — ' . $client->company_name,
                view('emails.engagement-letter-signed-staff', compact('letter'))->render()
            );
        }

        return view('signing.complete', compact('letter'));
    }
}
