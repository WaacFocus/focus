<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('engagement_letter_templates')) {
            return;
        }

        $now = now();

        $sections = [
            [
                'title'            => 'Introduction',
                'service_type'     => 'general',
                'sort_order'       => 1,
                'is_mandatory'     => true,
                'default_included' => true,
                'body'             => "We are pleased to confirm the terms of our engagement with you. This letter sets out the basis on which we will act on your behalf, together with our respective responsibilities.\n\nPlease read this letter carefully. If there is anything you do not understand or with which you disagree, please contact us before signing.",
            ],
            [
                'title'            => 'Our Responsibilities',
                'service_type'     => 'general',
                'sort_order'       => 2,
                'is_mandatory'     => true,
                'default_included' => true,
                'body'             => "We will conduct our work with reasonable skill and care in accordance with applicable professional and ethical standards.\n\nWe will keep you informed of material matters affecting your affairs and advise you of any action required. Our advice will be based on the information you provide; we will not independently verify such information.",
            ],
            [
                'title'            => 'Annual Accounts',
                'service_type'     => 'accounts',
                'sort_order'       => 3,
                'is_mandatory'     => false,
                'default_included' => false,
                'body'             => "We will prepare your annual financial statements from the accounting records and information you provide to us.\n\nWe will send a draft for your review and approval before finalising. Once approved, we will submit the accounts to Companies House and/or HMRC as required.\n\nWe will not be carrying out an audit of the accounts. Our work is conducted on the assumption that the information provided is complete and accurate.",
            ],
            [
                'title'            => 'Corporation Tax',
                'service_type'     => 'tax',
                'sort_order'       => 4,
                'is_mandatory'     => false,
                'default_included' => false,
                'body'             => "We will prepare your company corporation tax return and computations from your finalised accounts and any additional information you provide.\n\nWe will calculate your corporation tax liability, advise you of the amounts payable and payment due dates, and submit the return to HMRC on your behalf.",
            ],
            [
                'title'            => 'Self Assessment',
                'service_type'     => 'tax',
                'sort_order'       => 5,
                'is_mandatory'     => false,
                'default_included' => false,
                'body'             => "We will prepare your self assessment tax return for the relevant tax year and calculate your income tax and National Insurance liability.\n\nWe will advise you of the amounts due and the payment dates. We will submit your return to HMRC electronically once you have reviewed and approved it.",
            ],
            [
                'title'            => 'VAT Returns',
                'service_type'     => 'vat',
                'sort_order'       => 6,
                'is_mandatory'     => false,
                'default_included' => false,
                'body'             => "We will prepare your VAT returns for each VAT period from the records you provide and submit them to HMRC via Making Tax Digital (MTD) where applicable.\n\nYou are responsible for ensuring all VAT transactions are correctly coded and for notifying us of any unusual transactions.",
            ],
            [
                'title'            => 'Payroll',
                'service_type'     => 'payroll',
                'sort_order'       => 7,
                'is_mandatory'     => false,
                'default_included' => false,
                'body'             => "We will operate your payroll, calculate employee pay, deduct PAYE and National Insurance contributions, and submit Real Time Information (RTI) returns to HMRC.\n\nWe will advise you of amounts payable to employees and to HMRC. You are responsible for notifying us promptly of any changes to employee details, new starters, and leavers.",
            ],
            [
                'title'            => 'Bookkeeping',
                'service_type'     => 'bookkeeping',
                'sort_order'       => 8,
                'is_mandatory'     => false,
                'default_included' => false,
                'body'             => "We will maintain your accounting records from the documents and information you provide to us and reconcile your bank accounts on a regular basis.\n\nIt is your responsibility to preserve all relevant financial documents and to bring all transactions to our attention promptly.",
            ],
            [
                'title'            => 'Company Secretarial',
                'service_type'     => 'secretarial',
                'sort_order'       => 9,
                'is_mandatory'     => false,
                'default_included' => false,
                'body'             => "We will maintain your statutory registers, prepare and file confirmation statements with Companies House, and advise you of your ongoing statutory obligations.\n\nYou are responsible for notifying us promptly of any changes to directors, shareholders, registered office, or other statutory details.",
            ],
            [
                'title'            => 'Professional Fees',
                'service_type'     => 'general',
                'sort_order'       => 10,
                'is_mandatory'     => false,
                'default_included' => true,
                'body'             => "Our fees are based on the time and level of expertise required to carry out the agreed work. Where possible we will agree a fixed fee with you in advance.\n\nAll fees are payable within 30 days of invoice. We reserve the right to suspend services and charge interest on overdue accounts.",
            ],
            [
                'title'            => 'Client Responsibilities',
                'service_type'     => 'general',
                'sort_order'       => 11,
                'is_mandatory'     => true,
                'default_included' => true,
                'body'             => "You are responsible for ensuring that all information provided to us is complete, accurate, and provided in a timely manner. You should notify us of any changes to your circumstances that may affect your tax or financial affairs.\n\nYou are responsible for maintaining adequate accounting records and retaining documentation in accordance with statutory requirements.",
            ],
            [
                'title'            => 'Confidentiality & Data Protection',
                'service_type'     => 'general',
                'sort_order'       => 12,
                'is_mandatory'     => true,
                'default_included' => true,
                'body'             => "All information provided to us will be treated as confidential. We will not disclose your information to any third party without your consent, except where required by law or our professional obligations.\n\nWe are registered as a data controller under the UK GDPR. Our Privacy Notice explains how we collect, use, and protect your personal data and is available on request.",
            ],
            [
                'title'            => 'Acceptance of Terms',
                'service_type'     => 'general',
                'sort_order'       => 13,
                'is_mandatory'     => true,
                'default_included' => true,
                'body'             => "This letter supersedes any previous engagement letter between us. These terms will apply to all future work we undertake on your behalf until a new engagement letter is agreed.\n\nBy signing this letter you confirm that you have read, understood, and agree to be bound by its terms.",
            ],
        ];

        foreach ($sections as $section) {
            $exists = DB::table('engagement_letter_templates')
                ->where('title', $section['title'])
                ->exists();

            if (!$exists) {
                DB::table('engagement_letter_templates')->insert(array_merge($section, [
                    'is_active'  => true,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]));
            }
        }
    }

    public function down(): void
    {
        // Not reversible
    }
};
