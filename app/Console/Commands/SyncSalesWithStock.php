<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Sale;
use App\Models\Stock;
use App\Models\Product;

class SyncSalesWithStock extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sales:sync-stock {--dry-run : Show what would be synced without making changes}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync existing sales with stock transactions (one-time migration for historical data)';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting sales-stock synchronization...');
        $this->newLine();

        $dryRun = $this->option('dry-run');
        
        if ($dryRun) {
            $this->warn('⚠️  DRY RUN MODE - No changes will be made');
            $this->newLine();
        }

        // Get all sales
        $sales = Sale::all();
        $totalSales = $sales->count();
        
        $this->info("Found {$totalSales} total sales records");
        $this->newLine();

        $syncedCount = 0;
        $skippedCount = 0;
        $errorCount = 0;

        foreach ($sales as $sale) {
            try {
                // Check if this sale already has a stock transaction
                $existingStock = Stock::where('reference_id', $sale->id)
                    ->where('transaction_type', 'sale')
                    ->first();

                if ($existingStock) {
                    $this->line("✓ Sale #{$sale->id} - Already synced (skipping)");
                    $skippedCount++;
                    continue;
                }

                // Get product with unit information
                $product = Product::with(['baseUnit', 'salesUnit'])->find($sale->product_id);
                
                if (!$product) {
                    $this->error("✗ Sale #{$sale->id} - Product not found (ID: {$sale->product_id})");
                    $errorCount++;
                    continue;
                }

                // Convert sales quantity to base units
                $quantityInBaseUnits = $product->convertSalesToBase($sale->quantity);

                if ($dryRun) {
                    $this->info("→ Sale #{$sale->id} - Would create stock transaction: -{$quantityInBaseUnits} {$product->baseUnit->name} ({$sale->quantity} {$product->salesUnit->name})");
                    $syncedCount++;
                    continue;
                }

                // Create stock transaction
                $stock = new Stock();
                $stock->product_id = $sale->product_id;
                $stock->Date = $sale->Date ?? now()->toDateString();
                $stock->quantity = -$quantityInBaseUnits; // Negative because it's a sale
                $stock->new_adjustment_in_stock = $quantityInBaseUnits;
                $stock->action = 'sold';
                $stock->transaction_type = 'sale';
                $stock->reference_id = $sale->id;
                
                // Create descriptive notes showing both units
                $salesUnitName = $product->salesUnit->name ?? 'units';
                $baseUnitName = $product->baseUnit->name ?? 'units';
                $stock->notes = "Sale #{$sale->id} - {$sale->quantity} {$salesUnitName} ({$quantityInBaseUnits} {$baseUnitName}) [Historical Sync]";
                $stock->save();

                $this->info("✓ Sale #{$sale->id} - Synced: -{$quantityInBaseUnits} {$baseUnitName} ({$sale->quantity} {$salesUnitName})");
                $syncedCount++;

            } catch (\Exception $e) {
                $this->error("✗ Sale #{$sale->id} - Error: {$e->getMessage()}");
                $errorCount++;
            }
        }

        $this->newLine();
        $this->info('═══════════════════════════════════════');
        $this->info('Synchronization Complete!');
        $this->info('═══════════════════════════════════════');
        $this->info("Total Sales: {$totalSales}");
        $this->info("Synced: {$syncedCount}");
        $this->info("Already Synced: {$skippedCount}");
        $this->info("Errors: {$errorCount}");
        $this->newLine();

        if ($dryRun) {
            $this->warn('This was a DRY RUN. Run without --dry-run to actually sync the data.');
        } else {
            $this->info('All historical sales are now synced with stock transactions!');
        }

        return Command::SUCCESS;
    }
}
