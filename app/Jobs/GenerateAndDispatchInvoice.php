<?php

namespace App\Jobs;

use App\Models\Society;
use App\Models\Invoice;

class GenerateAndDispatchInvoice
{
    public Society $society;
    public string $billingMonth;
    public string $source;
    public bool $force;

    public function __construct(Society $society, ?string $billingMonth = null, string $source = 'manual', bool $force = false)
    {
        $this->society = $society;
        $this->billingMonth = $billingMonth ?: now()->format('Y-m');
        $this->source = $source;
        $this->force = $force;
    }

    /**
     * Static dispatch — runs synchronously (no queue worker needed).
     */
    public static function dispatch(Society $society, ?string $billingMonth = null, string $source = 'manual', bool $force = false): void
    {
        (new self($society, $billingMonth, $source, $force))->handle();
    }

    protected function logEvent(Invoice $invoice, string $event, ?string $details = null): void
    {
        $history = $invoice->dispatch_history ?? [];
        $history[] = [
            'event' => $event,
            'timestamp' => now()->toIso8601String(),
            'source' => $this->source,
            'details' => $details,
        ];
        $updateData = ['dispatch_history' => $history];
        if ($event === 'sent') {
            $updateData['mail_sent_count'] = ($invoice->mail_sent_count ?? 0) + 1;
        }
        $invoice->update($updateData);
    }

    public function handle(): void
    {
        $invoice = null;

        try {
            // 1. Check if invoice already exists
            $existing = Invoice::where('society_id', $this->society->id)
                ->where('billing_month', $this->billingMonth)
                ->first();

            // 2. If already sent and force is not requested, skip immediately without mailing
            if (!$this->force && $existing && $existing->status === 'sent') {
                return;
            }

            // Preserve existing invoice number if generated, else create standard format
            $invoiceNumber = $existing->invoice_number ?? ('INV-' . str_replace('-', '', $this->billingMonth) . '-' . str_pad($this->society->id, 4, '0', STR_PAD_LEFT));

            // Preserve existing invoice amount if valid (> 0), else calculate from society config
            $amount = ($existing && (float)$existing->total_amount > 0)
                ? (float)$existing->total_amount
                : (float)($this->society->billing_amount ?? 0);

            if ($amount <= 0) {
                $amount = ((float) ($this->society->flats_families ?? 0)) * ((float) ($this->society->rate_per_flat ?? 0));
            }

            // 3. Skip societies with no email — mark as 'skipped' and move on
            if (empty($this->society->contact_person_email)) {
                $invoice = Invoice::updateOrCreate(
                    ['society_id' => $this->society->id, 'billing_month' => $this->billingMonth],
                    [
                        'invoice_number' => $invoiceNumber,
                        'total_amount'   => $amount,
                        'status'         => 'skipped',
                        'error_log'      => 'No email address configured in society master.'
                    ]
                );
                $this->logEvent($invoice, 'skipped', 'No email address configured in society master.');
                return;
            }

            // 4. Create / update pending record
            $invoice = Invoice::updateOrCreate(
                ['society_id' => $this->society->id, 'billing_month' => $this->billingMonth],
                [
                    'invoice_number' => $invoiceNumber,
                    'total_amount'   => $amount,
                    'status'         => 'pending',
                    'error_log'      => null
                ]
            );
            $this->logEvent($invoice, 'generated', 'Invoice record verified for dispatch.');

            $amountInWords = self::numberToWords($amount);

            // 5. Generate PDF
            $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('pdfs.invoice', [
                'invoice'       => $invoice,
                'society'       => $this->society,
                'amountInWords' => $amountInWords,
            ]);

            // 6. Send mail synchronously
            \Illuminate\Support\Facades\Mail::to($this->society->contact_person_email)
                ->send(new \App\Mail\SocietyInvoiceMail($invoice, $pdf->output()));

            $invoice->update([
                'status'    => 'sent',
                'sent_at'   => now(),
                'error_log' => null,
            ]);
            $this->logEvent($invoice, 'sent', "Successfully emailed to {$this->society->contact_person_email}");

        } catch (\Throwable $e) {
            if ($invoice) {
                $invoice->update(['status' => 'failed', 'error_log' => $e->getMessage()]);
                $this->logEvent($invoice, 'failed', $e->getMessage());
            }
            \Illuminate\Support\Facades\Log::error("Invoice dispatch failed for society {$this->society->id}: " . $e->getMessage());
        }
    }

    /**
     * Convert currency float to Indian numbering words.
     */
    public static function numberToWords($number): string
    {
        $decimal = round($number - ($no = floor($number)), 2) * 100;
        $hundred = null;
        $digits_length = strlen($no);
        $i = 0;
        $str = [];
        $words = [
            0 => '', 1 => 'One', 2 => 'Two', 3 => 'Three', 4 => 'Four',
            5 => 'Five', 6 => 'Six', 7 => 'Seven', 8 => 'Eight', 9 => 'Nine',
            10 => 'Ten', 11 => 'Eleven', 12 => 'Twelve', 13 => 'Thirteen',
            14 => 'Fourteen', 15 => 'Fifteen', 16 => 'Sixteen', 17 => 'Seventeen',
            18 => 'Eighteen', 19 => 'Nineteen', 20 => 'Twenty', 30 => 'Thirty',
            40 => 'Forty', 50 => 'Fifty', 60 => 'Sixty', 70 => 'Seventy',
            80 => 'Eighty', 90 => 'Ninety',
        ];
        $digits = ['', 'Hundred', 'Thousand', 'Lakh', 'Crore'];
        while ($i < $digits_length) {
            $divider = ($i == 2) ? 10 : 100;
            $number  = floor($no % $divider);
            $no      = floor($no / $divider);
            $i      += $divider == 10 ? 1 : 2;
            if ($number) {
                $plural  = (($counter = count($str)) && $number > 9) ? 's' : null;
                $hundred = ($counter == 1 && $str[0]) ? ' and ' : null;
                $str[]   = ($number < 21)
                    ? $words[$number] . ' ' . $digits[$counter] . $plural . ' ' . $hundred
                    : $words[floor($number / 10) * 10] . ' ' . $words[$number % 10] . ' ' . $digits[$counter] . $plural . ' ' . $hundred;
            } else {
                $str[] = null;
            }
        }
        $Rupees = implode('', array_reverse($str));
        $paise  = ($decimal > 0) ? 'and ' . ($words[$decimal / 10] . ' ' . $words[$decimal % 10]) . ' Paise' : '';
        return ucwords(trim(($Rupees ? $Rupees . ' Rupees ' : '') . $paise) . ' Only');
    }
}
