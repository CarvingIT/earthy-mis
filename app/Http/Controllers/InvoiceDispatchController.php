<?php

namespace App\Http\Controllers;

use App\Models\Society;
use App\Models\Invoice;
use App\Jobs\GenerateAndDispatchInvoice;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class InvoiceDispatchController extends Controller
{
    public function index(Request $request)
    {
        $month = $request->query('month', now()->format('Y-m'));
        
        $totalSocieties = Society::count();
        $sentCount    = Invoice::where('billing_month', $month)->where('status', 'sent')->count();
        $failedCount  = Invoice::where('billing_month', $month)->where('status', 'failed')->count();
        $pendingCount = Invoice::where('billing_month', $month)->where('status', 'pending')->count();
        $skippedCount = Invoice::where('billing_month', $month)->where('status', 'skipped')->count();

        $allSocieties = Society::with(['invoices' => function ($query) use ($month) {
            $query->where('billing_month', $month);
        }])->orderBy('name')->paginate(15);

        if ($request->ajax() || $request->query('ajax') == 1) {
            $html = view('invoices.partials.rows', compact('allSocieties', 'month'))->render();
            return response()->json([
                'html'        => $html,
                'hasMore'     => $allSocieties->hasMorePages(),
                'nextPageUrl' => $allSocieties->nextPageUrl() ? $allSocieties->nextPageUrl() . '&month=' . $month . '&ajax=1' : null,
                'total'       => $allSocieties->total(),
                'count'       => $allSocieties->count(),
            ]);
        }

        return view('invoices.dashboard', compact(
            'month',
            'totalSocieties',
            'sentCount',
            'failedCount',
            'pendingCount',
            'skippedCount',
            'allSocieties'
        ));
    }

    /**
     * Trigger invoice generation and dispatch for all active societies (legacy form-submit fallback).
     */
    public function triggerGlobalDispatch(Request $request)
    {
        $month = $request->input('month', now()->format('Y-m'));
        $societies = Society::all();

        if ($societies->isEmpty()) {
            return redirect()->back()->with('error', 'No societies found to process.');
        }

        $sent = 0; $failed = 0; $skipped = 0;
        foreach ($societies as $society) {
            GenerateAndDispatchInvoice::dispatch($society, $month);
            $inv = Invoice::where('society_id', $society->id)->where('billing_month', $month)->first();
            if ($inv) {
                if ($inv->status === 'sent')         $sent++;
                elseif ($inv->status === 'skipped')  $skipped++;
                else $failed++;
            }
        }

        return redirect()->back()->with('success', "Dispatch complete — Sent: {$sent}, Skipped (no email): {$skipped}, Failed: {$failed}.");
    }

    /**
     * AJAX: Return list of all society IDs + names for the live dispatch UI.
     */
    public function getSocietiesForDispatch(Request $request)
    {
        $societies = Society::select('id', 'name', 'contact_person_email')->orderBy('name')->get();
        return response()->json($societies);
    }

    /**
     * AJAX: Dispatch invoice for a single society and return JSON result.
     */
    public function dispatchOne(Request $request, Society $society)
    {
        $month = $request->input('month', now()->format('Y-m'));
        GenerateAndDispatchInvoice::dispatch($society, $month);
        $inv = Invoice::where('society_id', $society->id)->where('billing_month', $month)->first();
        return response()->json([
            'society_id' => $society->id,
            'name'       => $society->name,
            'status'     => $inv ? $inv->status : 'failed',
            'error'      => ($inv && $inv->status === 'failed') ? $inv->error_log : null,
        ]);
    }


    /**
     * Retry dispatch for all failed invoices in the given month.
     */
    public function retryFailed(Request $request)
    {
        $month = $request->input('month', now()->format('Y-m'));
        
        $failedInvoices = Invoice::where('billing_month', $month)
            ->where('status', 'failed')
            ->with('society')
            ->get();

        if ($failedInvoices->isEmpty()) {
            return redirect()->back()->with('info', 'No failed invoices found to retry.');
        }

        foreach ($failedInvoices as $invoice) {
            $invoice->update(['status' => 'pending', 'error_log' => null]);
            GenerateAndDispatchInvoice::dispatch($invoice->society, $month);
        }

        return redirect()->back()->with('success', "Dispatched retry jobs for {$failedInvoices->count()} failed invoices.");
    }

    /**
     * Retry/trigger dispatch for a single society.
     */
    public function retrySingle(Request $request, Society $society)
    {
        $month = $request->input('month', now()->format('Y-m'));

        if ($request->filled('email')) {
            $request->validate([
                'email' => ['required', 'email', 'max:255'],
            ]);
            $society->update([
                'contact_person_email' => $request->input('email')
            ]);
        }

        // Reset or create pending invoice record
        Invoice::updateOrCreate(
            [
                'society_id' => $society->id,
                'billing_month' => $month,
            ],
            [
                'status' => 'pending',
                'error_log' => null,
            ]
        );

        GenerateAndDispatchInvoice::dispatch($society, $month);

        return redirect()->back()->with('success', "Invoice dispatch job queued for {$society->name}.");
    }

    /**
     * View generated PDF inline.
     */
    public function viewPdf(Invoice $invoice)
    {
        $amountInWords = GenerateAndDispatchInvoice::numberToWords($invoice->total_amount);

        $pdf = Pdf::loadView('pdfs.invoice', [
            'invoice' => $invoice,
            'society' => $invoice->society,
            'amountInWords' => $amountInWords,
        ]);

        return $pdf->stream("Invoice_{$invoice->invoice_number}.pdf");
    }

    /**
     * Download a ZIP of all generated invoices for the month.
     */
    public function downloadZip(Request $request)
    {
        $month = $request->query('month', now()->format('Y-m'));
        $invoices = Invoice::where('billing_month', $month)->where('status', 'sent')->with('society')->get();

        if ($invoices->isEmpty()) {
            return redirect()->back()->with('error', 'No sent invoices found to package for this month.');
        }

        $zip = new \ZipArchive();
        $zipFileName = "Invoices_{$month}.zip";
        $zipFilePath = storage_path($zipFileName);

        if ($zip->open($zipFilePath, \ZipArchive::CREATE | \ZipArchive::OVERWRITE) === TRUE) {
            foreach ($invoices as $invoice) {
                $amountInWords = GenerateAndDispatchInvoice::numberToWords($invoice->total_amount);
                $pdf = Pdf::loadView('pdfs.invoice', [
                    'invoice' => $invoice,
                    'society' => $invoice->society,
                    'amountInWords' => $amountInWords,
                ]);
                $pdfContent = $pdf->output();
                $fileName = "Invoice_{$invoice->invoice_number}.pdf";
                $zip->addFromString($fileName, $pdfContent);
            }
            $zip->close();

            return response()->download($zipFilePath)->deleteFileAfterSend(true);
        }

        return redirect()->back()->with('error', 'Failed to generate ZIP archive.');
    }

    /**
     * Get details of active billing stats for modal display.
     */
    public function getStatsDetails(Request $request)
    {
        $status = $request->query('status'); // 'total', 'sent', 'pending', 'failed'
        $month = $request->query('month', now()->format('Y-m'));

        $data = [];
        if ($status === 'total') {
            $societies = Society::orderBy('name')->get();
            foreach ($societies as $s) {
                $data[] = [
                    'name' => $s->name,
                    'info' => "{$s->flats_families} Flats / " . ($s->city ?: 'No city'),
                    'detail' => $s->contact_person_email ?: 'No email set',
                    'amount' => '₹' . number_format($s->billing_amount ?: ($s->flats_families * $s->rate_per_flat), 2)
                ];
            }
            $title = "All Societies Listing";
        } elseif ($status === 'sent') {
            $invoices = Invoice::where('billing_month', $month)->where('status', 'sent')->with('society')->get();
            foreach ($invoices as $inv) {
                $data[] = [
                    'name' => $inv->society->name,
                    'info' => $inv->invoice_number,
                    'detail' => 'Sent At: ' . ($inv->sent_at ? $inv->sent_at->format('d-M-y H:i') : '-'),
                    'amount' => '₹' . number_format($inv->total_amount, 2)
                ];
            }
            $title = "Sent Invoices (" . count($data) . ")";
        } elseif ($status === 'pending') {
            $invoices = Invoice::where('billing_month', $month)->where('status', 'pending')->with('society')->get();
            foreach ($invoices as $inv) {
                $data[] = [
                    'name' => $inv->society->name,
                    'info' => $inv->invoice_number,
                    'detail' => $inv->society->contact_person_email ?: 'No email set',
                    'amount' => '₹' . number_format($inv->total_amount, 2)
                ];
            }
            $title = "Pending Queue (" . count($data) . ")";
        } elseif ($status === 'failed') {
            $invoices = Invoice::where('billing_month', $month)->where('status', 'failed')->with('society')->get();
            foreach ($invoices as $inv) {
                $data[] = [
                    'name' => $inv->society->name,
                    'info' => $inv->invoice_number,
                    'detail' => 'Error: ' . substr($inv->error_log, 0, 80) . '...',
                    'amount' => '₹' . number_format($inv->total_amount, 2)
                ];
            }
            $title = "Failed Invoices (" . count($data) . ")";
        } else {
            return response()->json(['error' => 'Invalid status'], 400);
        }

        return response()->json([
            'title' => $title,
            'items' => $data
        ]);
    }

    /**
     * View dynamic PDF preview for a specific society (even if not yet generated).
     */
    public function viewPdfBySociety(Request $request, Society $society)
    {
        $month = $request->query('month', now()->format('Y-m'));

        $invoice = Invoice::where('society_id', $society->id)
            ->where('billing_month', $month)
            ->first();

        if (!$invoice) {
            $amount = (float) ($society->billing_amount ?? 0);
            if ($amount <= 0) {
                $amount = ((float) ($society->flats_families ?? 0)) * ((float) ($society->rate_per_flat ?? 0));
            }
            $invoiceNumber = 'PREVIEW-' . str_replace('-', '', $month) . '-' . str_pad($society->id, 4, '0', STR_PAD_LEFT);

            $invoice = new Invoice([
                'society_id' => $society->id,
                'billing_month' => $month,
                'invoice_number' => $invoiceNumber,
                'total_amount' => $amount,
                'status' => 'pending'
            ]);
        }

        $amountInWords = GenerateAndDispatchInvoice::numberToWords($invoice->total_amount);

        $pdf = Pdf::loadView('pdfs.invoice', [
            'invoice' => $invoice,
            'society' => $society,
            'amountInWords' => $amountInWords,
        ]);

        return $pdf->stream("Invoice_{$invoice->invoice_number}.pdf");
    }

    /**
     * Clear all invoice records for the selected month to start fresh.
     */
    public function clearQueue(Request $request)
    {
        $month = $request->input('month', now()->format('Y-m'));

        Invoice::where('billing_month', $month)->delete();

        return redirect()->back()->with('success', "All invoice records for {$month} have been cleared. You can now generate fresh invoices.");
    }

    /**
     * Clear only pending/failed invoice records for the selected month.
     */
    public function clearPending(Request $request)
    {
        $month = $request->input('month', now()->format('Y-m'));

        Invoice::where('billing_month', $month)->whereIn('status', ['pending', 'failed', 'skipped'])->delete();

        return redirect()->back()->with('success', "All pending/failed/skipped invoice records for {$month} have been cleared.");
    }

    /**
     * Generate invoice records for all societies for the selected month without dispatching mail.
     */
    public function generateGlobal(Request $request)
    {
        $month = $request->input('month', now()->format('Y-m'));
        $societies = Society::all();

        if ($societies->isEmpty()) {
            return redirect()->back()->with('error', 'No societies found to process.');
        }

        foreach ($societies as $society) {
            $amount = (float) ($society->billing_amount ?? 0);
            if ($amount <= 0) {
                $amount = ((float) ($society->flats_families ?? 0)) * ((float) ($society->rate_per_flat ?? 0));
            }
            $invoiceNumber = 'INV-' . str_replace('-', '', $month) . '-' . str_pad($society->id, 4, '0', STR_PAD_LEFT);

            $invoice = Invoice::updateOrCreate(
                [
                    'society_id' => $society->id,
                    'billing_month' => $month,
                ],
                [
                    'invoice_number' => $invoiceNumber,
                    'total_amount' => $amount,
                    'status' => 'pending',
                    'error_log' => null,
                ]
            );

            $history = $invoice->dispatch_history ?? [];
            $history[] = [
                'event' => 'generated',
                'timestamp' => now()->toIso8601String(),
                'source' => 'manual',
                'details' => 'Generated globally without mailing',
            ];
            $invoice->update(['dispatch_history' => $history]);
        }

        return redirect()->back()->with('success', "Successfully generated invoice records for {$societies->count()} societies for {$month}.");
    }

    /**
     * Get JSON report data for invoices dispatched/generated in a month.
     */
    public function getReportData(Request $request)
    {
        $month = $request->query('month', now()->format('Y-m'));

        $invoices = Invoice::where('billing_month', $month)
            ->with('society')
            ->get();

        $data = $invoices->map(function ($invoice) {
            return [
                'id' => $invoice->id,
                'society_name' => $invoice->society->name ?? 'Unknown',
                'invoice_number' => $invoice->invoice_number,
                'status' => $invoice->status,
                'mail_sent_count' => $invoice->mail_sent_count ?? 0,
                'created_at' => $invoice->created_at ? $invoice->created_at->toIso8601String() : null,
                'sent_at' => $invoice->sent_at ? $invoice->sent_at->toIso8601String() : null,
                'dispatch_history' => $invoice->dispatch_history ?? [],
            ];
        });

        return response()->json([
            'month_label' => \Carbon\Carbon::parse($month . '-01')->format('F Y'),
            'invoices' => $data
        ]);
    }
}
