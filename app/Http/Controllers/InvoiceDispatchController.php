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
        $sentCount = Invoice::where('billing_month', $month)->where('status', 'sent')->count();
        $failedCount = Invoice::where('billing_month', $month)->where('status', 'failed')->count();
        $pendingCount = Invoice::where('billing_month', $month)->where('status', 'pending')->count();
        
        // Societies that don't have an invoice record yet for this month are also pending/not generated
        $allSocieties = Society::with(['invoices' => function ($query) use ($month) {
            $query->where('billing_month', $month);
        }])->orderBy('name')->paginate(15);

        if ($request->ajax() || $request->query('ajax') == 1) {
            $html = view('invoices.partials.rows', compact('allSocieties', 'month'))->render();
            return response()->json([
                'html' => $html,
                'hasMore' => $allSocieties->hasMorePages(),
                'nextPageUrl' => $allSocieties->nextPageUrl() ? $allSocieties->nextPageUrl() . '&month=' . $month . '&ajax=1' : null,
                'total' => $allSocieties->total(),
                'count' => $allSocieties->count(),
            ]);
        }

        return view('invoices.dashboard', compact(
            'month',
            'totalSocieties',
            'sentCount',
            'failedCount',
            'pendingCount',
            'allSocieties'
        ));
    }

    /**
     * Trigger invoice generation and dispatch for all active societies.
     */
    public function triggerGlobalDispatch(Request $request)
    {
        $month = $request->input('month', now()->format('Y-m'));
        $societies = Society::all();

        if ($societies->isEmpty()) {
            return redirect()->back()->with('error', 'No societies found to process.');
        }

        foreach ($societies as $society) {
            GenerateAndDispatchInvoice::dispatch($society, $month);
        }

        return redirect()->back()->with('success', "Global dispatch queue jobs generated for {$societies->count()} societies for month {$month}.");
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
}
