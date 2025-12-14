<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Config;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Custom URL Reset Password untuk Frontend React
        ResetPassword::createUrlUsing(function (object $notifiable, string $token) {
            return env('FRONTEND_URL', 'http://localhost:5173') . "/reset-password?token={$token}&email={$notifiable->getEmailForPasswordReset()}";
        });

        VerifyEmail::toMailUsing(function (object $notifiable, string $url) {
            return (new MailMessage)
                ->subject('Verifikasi Akun Alumni Anda') // Subjek Email
                ->greeting('Halo, ' . $notifiable->name . '!')
                ->line('Terima kasih telah mendaftar di Sistem Informasi Alumni.')
                ->line('Untuk mengaktifkan akun Anda, silakan klik tombol verifikasi di bawah ini:')
                ->action('Verifikasi Email Saya', $url) // Tombol & Link
                ->line('Jika Anda tidak merasa mendaftar akun ini, silakan abaikan email ini.')
                ->salutation('Salam Hangat, Admin Prodi.');
        });

        // LOGIC URL CUSTOM UNTUK FRONTEND
        VerifyEmail::createUrlUsing(function ($notifiable) {

            // 1. Ambil URL Frontend dari .env
            $frontendUrl = env('FRONTEND_URL', 'http://localhost:5173');

            // 2. Generate URL Backend yang valid dan ditandatangani
            // Ini penting agar 'signature' cocok dengan route backend nanti
            $backendUrl = URL::temporarySignedRoute(
                'verification.verify', // Nama route API (harus ada di api.php)
                now()->addMinutes(60),
                [
                    'id' => $notifiable->getKey(),
                    'hash' => sha1($notifiable->getEmailForVerification())
                ]
            );

            // 3. Ambil Query String-nya saja (expires=...&signature=...)
            $parsedUrl = parse_url($backendUrl);
            $queryString = $parsedUrl['query'] ?? '';

            // 4. Rakit URL Frontend
            // Hasil: http://localhost:5173/verify-email/{id}/{hash}?expires=...&signature=...
            return "$frontendUrl/verify-email/{$notifiable->getKey()}/" . sha1($notifiable->getEmailForVerification()) . "?$queryString";
        });
    }
}
