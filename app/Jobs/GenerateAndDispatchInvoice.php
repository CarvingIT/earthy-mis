<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

use App\Models\Society;
use App\Models\Invoice;

class GenerateAndDispatchInvoice implements ShouldQueue
{
    use Queueable;

    public Society $society;
    public string $billingMonth;

    /**
     * Create a new job instance.
     */
    public function __construct(Society $society, ?string $billingMonth = null)
    {
        $this->society = $society;
        $this->billingMonth = $billingMonth ?: now()->format('Y-m');
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $invoice = null;

        try {
            $amount = (float) ($this->society->billing_amount ?? 0);
            if ($amount <= 0) {
                $amount = ((float) ($this->society->flats_families ?? 0)) * ((float) ($this->society->rate_per_flat ?? 0));
            }

            // Generate unique invoice number: e.g. INV-202606-0012
            $invoiceNumber = 'INV-' . str_replace('-', '', $this->billingMonth) . '-' . str_pad($this->society->id, 4, '0', STR_PAD_LEFT);

            // Create or update the invoice
            $invoice = Invoice::updateOrCreate(
                [
                    'society_id' => $this->society->id,
                    'billing_month' => $this->billingMonth,
                ],
                [
                    'invoice_number' => $invoiceNumber,
                    'total_amount' => $amount,
                    'status' => 'pending',
                    'error_log' => null,
                ]
            );

            if (empty($this->society->contact_person_email)) {
                throw new \Exception("Society lacks contact person email address.");
            }

            $amountInWords = self::numberToWords($amount);

            // Generate PDF
            $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('pdfs.invoice', [
                'invoice' => $invoice,
                'society' => $this->society,
                'amountInWords' => $amountInWords,
            ]);

            // Dispatch Mail
            \Illuminate\Support\Facades\Mail::to($this->society->contact_person_email)
                ->send(new \App\Mail\SocietyInvoiceMail($invoice, $pdf->output()));

            // Update status
            $invoice->update([
                'status' => 'sent',
                'sent_at' => now(),
            ]);

        } catch (\Throwable $e) {
            if ($invoice) {
                $invoice->update([
                    'status' => 'failed',
                    'error_log' => $e->getMessage() . "\n" . $e->getTraceAsString(),
                ]);
            } else {
                // If invoice creation failed, write to log
                \Illuminate\Support\Facades\Log::error("Failed generating invoice for society {$this->society->id}: " . $e->getMessage());
            }
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
        $str = array();
        $words = array(
            0 => '', 1 => 'One', 2 => 'Two',
            3 => 'Three', 4 => 'Four', 5 => 'Five', 6 => 'Six',
            7 => 'Seven', 8 => 'Eight', 9 => 'Nine',
            10 => 'Ten', 11 => 'Eleven', 12 => 'Twelve',
            13 => 'Thirteen', 14 => 'Fourteen', 15 => 'Fifteen',
            16 => 'Sixteen', 17 => 'Seventeen', 18 => 'Eighteen',
            19 => 'Nineteen', 20 => 'Twenty', 30 => 'Thirty',
            40 => 'Forty', 50 => 'Fifty', 60 => 'Sixty',
            70 => 'Seventy', 80 => 'Eighty', 90 => 'Ninety'
        );
        $digits = array('', 'Hundred','Thousand','Lakh', 'Crore');
        while( $i < $digits_length ) {
            $divider = ($i == 2) ? 10 : 100;
            $number = floor($no % $divider);
            $no = floor($no / $divider);
            $i += $divider == 10 ? 1 : 2;
            if ($number) {
                $plural = (($counter = count($str)) && $number > 9) ? 's' : null;
                $hundred = ($counter == 1 && $str[0]) ? ' and ' : null;
                $str [] = ($number < 21) ? $words[$number].' '. $digits[$counter]. $plural.' '.$hundred:$words[floor($number / 10) * 10].' '.$words[$number % 10]. ' '.$digits[$counter].$plural.' '.$hundred;
            } else $str[] = null;
        }
        $Rupees = implode('', array_reverse($str));
        $paise = ($decimal > 0) ? "and " . ($words[$decimal / 10] . " " . $words[$decimal % 10]) . ' Paise' : '';
        return ucwords(trim(($Rupees ? $Rupees . ' Rupees ' : '') . $paise) . ' Only');
    }
}
