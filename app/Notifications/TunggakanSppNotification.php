<?php

namespace App\Notifications;

use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class TunggakanSppNotification extends Notification
{
    use Queueable;

    public $transaction;

    // php artisan vendor:publish --tag=laravel-mail

    /**
     * Create a new notification instance.
     */
    public function __construct($transaction)
    {
        $this->transaction = $transaction;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        Carbon::setLocale('id');
        $date = Carbon::parse($this->transaction->date)->translatedFormat('d F Y');

        return (new MailMessage)
            ->subject("Tunggakan SPP")
            ->greeting("Halo {$this->transaction->student->first_name} {$this->transaction->student->last_name},")
            ->line("Kami mendeteksi bahwa Anda belum melakukan pembayaran SPP untuk tanggal {$date}.")
            ->line("Jumlah tagihan: Rp " . number_format($this->transaction->price, 0, ',', '.'))
            ->line('Mohon segera melakukan pembayaran.')
            ->action('Bayar Sekarang', url("/dashboard/transaksi-spp/show/{$this->transaction->student->id}"))
            ->salutation("Hormat kami,\n\nAdmin SMAN 1 Lumbanjulu");
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            //
        ];
    }
}
