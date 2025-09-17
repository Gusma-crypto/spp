<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Transaction;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class ExpireTransactions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'transactions:expire';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Set Pending transactions to Expired when expired_at < now';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        try {
            $now = Carbon::now();

            $transactions = Transaction::where('status', 'Pending')
                ->whereNotNull('expired_at')
                ->where('expired_at', '<', $now)
                ->get();

            if ($transactions->isEmpty()) {
                $this->info("Tidak ada transaksi yang expired.");
                return 0;
            }

            foreach ($transactions as $trx) {
                $trx->update([
                    'status' => 'Expired'
                ]);

                Log::info("Transaksi expired otomatis", [
                    'transaction_id' => $trx->id,
                    'student_id' => $trx->student_id,
                    'expired_at' => $trx->expired_at,
                ]);
            }

            $this->info("Expired transactions updated: {$transactions->count()}");
            return 0;

        } catch (\Throwable $th) {
            Log::error("Gagal menjalankan transaksi expire", [
                'error' => $th->getMessage(),
                'trace' => $th->getTraceAsString()
            ]);
            $this->error("Error: " . $th->getMessage());
            return 1;
        }
    }
}