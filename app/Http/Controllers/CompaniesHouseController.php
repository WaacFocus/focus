<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class CompaniesHouseController extends Controller
{
    private function ch(): \Illuminate\Http\Client\PendingRequest
    {
        return Http::withBasicAuth(config('services.companies_house.key', ''), '')
            ->baseUrl('https://api.company-information.service.gov.uk')
            ->timeout(8);
    }

    public function search(Request $request)
    {
        $request->validate(['q' => 'required|string|min:2|max:200']);

        $q = trim($request->q);

        // If the query looks like a company number (up to 2 optional letters + 5–8 digits,
        // e.g. 15117984, SC123456, NI012345), look it up directly by number.
        if (preg_match('/^[A-Za-z]{0,2}\d{5,8}$/', $q)) {
            $number  = strtoupper(str_pad(preg_replace('/[^a-zA-Z0-9]/', '', $q), 8, '0', STR_PAD_LEFT));
            $profile = $this->ch()->get("/company/{$number}");

            if ($profile->successful()) {
                $d = $profile->json();
                return response()->json(['items' => [[
                    'company_number'  => $d['company_number'] ?? $number,
                    'title'           => $d['company_name'] ?? '',
                    'company_status'  => $d['company_status'] ?? '',
                    'company_type'    => $d['type'] ?? '',
                    'address_snippet' => implode(', ', array_filter([
                        $d['registered_office_address']['address_line_1'] ?? '',
                        $d['registered_office_address']['locality'] ?? '',
                        $d['registered_office_address']['postal_code'] ?? '',
                    ])),
                ]]]);
            }
        }

        $res = $this->ch()->get('/search/companies', [
            'q'              => $q,
            'items_per_page' => 10,
        ]);

        if (! $res->successful()) {
            return response()->json(['items' => [], 'error' => 'Companies House search failed.']);
        }

        $items = collect($res->json('items', []))->map(fn ($item) => [
            'company_number'  => $item['company_number'] ?? '',
            'title'           => $item['title'] ?? '',
            'company_status'  => $item['company_status'] ?? '',
            'company_type'    => $item['company_type'] ?? '',
            'address_snippet' => $item['address_snippet'] ?? '',
        ])->values();

        return response()->json(['items' => $items]);
    }

    public function officers(string $number)
    {
        $number = strtoupper(preg_replace('/[^a-zA-Z0-9]/', '', $number));

        $res = $this->ch()->get("/company/{$number}/officers", ['items_per_page' => 50]);

        if (! $res->successful()) {
            return response()->json(['officers' => []]);
        }

        $officers = collect($res->json('items', []))
            ->filter(fn ($o) => empty($o['resigned_on']))
            ->map(fn ($o) => [
                'name'                 => $this->formatOfficerName($o['name'] ?? ''),
                'role'                 => $o['officer_role'] ?? 'director',
                'appointed_on'         => $o['appointed_on'] ?? null,
                'dob_month'            => $o['date_of_birth']['month'] ?? null,
                'dob_year'             => $o['date_of_birth']['year'] ?? null,
                'nationality'          => $o['nationality'] ?? null,
                'occupation'           => $o['occupation'] ?? null,
                'country_of_residence' => $o['country_of_residence'] ?? null,
            ])
            ->values();

        return response()->json(['officers' => $officers]);
    }

    private function formatOfficerName(string $raw): string
    {
        // CH format: "SURNAME, Firstname" or "SURNAME SURNAME, Firstname Middlename"
        $commaPos = strpos($raw, ',');
        if ($commaPos === false) {
            return ucwords(strtolower($raw));
        }
        $surname   = trim(substr($raw, 0, $commaPos));
        $forenames = trim(substr($raw, $commaPos + 1));
        $surname   = implode(' ', array_map(
            fn ($w) => strtoupper($w[0] ?? '') . strtolower(substr($w, 1)),
            explode(' ', $surname)
        ));
        return $forenames . ' ' . $surname;
    }

    public function profile(string $number)
    {
        $number = strtoupper(preg_replace('/[^a-zA-Z0-9]/', '', $number));

        $res = $this->ch()->get("/company/{$number}");

        if (! $res->successful()) {
            return response()->json(['error' => 'Company not found.'], 404);
        }

        $data    = $res->json();
        $address = $data['registered_office_address'] ?? [];

        // Build a single address line from the two CH address lines
        $addressLine = implode(', ', array_filter([
            $address['address_line_1'] ?? '',
            $address['address_line_2'] ?? '',
        ]));

        $accounts = $data['accounts'] ?? [];

        return response()->json([
            'company_name'                       => $data['company_name'] ?? '',
            'company_number'                     => $data['company_number'] ?? '',
            'company_type'                       => $data['type'] ?? '',
            'company_status'                     => $data['company_status'] ?? '',
            // Main address fields (for client record)
            'address'                            => $addressLine,
            'town'                               => $address['locality'] ?? '',
            'county'                             => $address['region'] ?? '',
            'postcode'                           => $address['postal_code'] ?? '',
            // CH data fields
            'ch_status'                          => $data['company_status'] ?? '',
            'ch_incorporated_on'                 => $data['date_of_creation'] ?? '',
            'ch_jurisdiction'                    => $data['jurisdiction'] ?? '',
            'ch_sic_codes'                       => implode(', ', $data['sic_codes'] ?? []),
            'ch_reg_address_line_1'              => $address['address_line_1'] ?? '',
            'ch_reg_address_line_2'              => $address['address_line_2'] ?? '',
            'ch_reg_locality'                    => $address['locality'] ?? '',
            'ch_reg_region'                      => $address['region'] ?? '',
            'ch_reg_postcode'                    => $address['postal_code'] ?? '',
            'ch_reg_country'                     => $address['country'] ?? '',
            'ch_accounts_year_end'               => $accounts['next_made_up_to'] ?? '',
            'ch_accounts_next_due'               => $accounts['next_due'] ?? '',
            'ch_confirmation_statement_next_due' => ($data['confirmation_statement']['next_due'] ?? ''),
        ]);
    }
}
