<?php

namespace App\Console\Commands;

use App\Models\Society;
use App\Jobs\GenerateAndDispatchInvoice;
use Illuminate\Console\Command;

class DispatchMonthlyInvoices extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'invoices:dispatch-monthly {month? : Billing month in YYYY-MM format}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate and dispatch monthly invoices for all active societies.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $month = $this->argument('month') ?: now()->format('Y-m');

        $this->info("Dispatching monthly invoices for: {$month}");

        $societies = Society::all();

        if ($societies->isEmpty()) {
            $this->warn("No active societies found.");
            return 0;
        }

        foreach ($societies as $society) {
            GenerateAndDispatchInvoice::dispatch($society, $month, 'cron');
        }

        $this->info("Dispatched generation jobs for {$societies->count()} societies.");
        return 0;
    }
}
