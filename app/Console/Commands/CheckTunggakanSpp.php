<?php

namespace App\Console\Commands;

use App\Models\Transaction;
use App\Notifications\TunggakanSppNotification;
use Carbon\Carbon;
use Illuminate\Console\Command;

class CheckTunggakanSpp extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:check-tunggakan-spp';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Cek tunggakan spp';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $today = Carbon::now();

        if ($today->day !== 10) {
            $this->info('Hari ini bukan tanggal 10, tidak ada yang perlu dilakukan.');
            return Command::SUCCESS;
        }

        $tunggakan = Transaction::with('student')
            ->where('status', 'Belum Lunas')
            ->whereDate('date', '<=', $today->toDateString())
            ->get();

        if ($tunggakan->isEmpty()) {
            $this->info('Tidak ada tunggakan spp.');
        } else {
            foreach ($tunggakan as $transaction) {
                if ($transaction->student && $transaction->student->email) {
                    $transaction->student->notify(new TunggakanSppNotification($transaction));
                    $this->info("Email dikirim ke : {$transaction->student->email}");
                }
            }

            $this->info('Notifikasi tunggakan spp telah dikirim.');
        }

        return Command::SUCCESS;
    }
}
