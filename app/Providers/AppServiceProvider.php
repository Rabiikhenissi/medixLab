<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Auth\Notifications\ResetPassword;

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
        \Illuminate\Support\Facades\Gate::before(function ($user, $ability) {
            if (method_exists($user, 'hasPermission') && $user->hasPermission($ability)) {
                return true;
            }
        });

        // Customise the branded password-reset email
        ResetPassword::toMailUsing(function ($notifiable, string $token) {
            $url = url('/patient/reset-password/' . $token . '?email=' . urlencode($notifiable->email));

            return (new \Illuminate\Notifications\Messages\MailMessage)
                ->subject('Réinitialisation de votre mot de passe — Medix eSanté')
                ->view('emails.reset-password', ['url' => $url]);
        \Illuminate\Support\Facades\View::composer('layouts.admin', function ($view) {
            $sidebarFeatures = \App\Models\Feature::where('is_archive', false)
                ->where('is_sidebar', true)
                ->orderBy('order', 'asc')
                ->get();
            $view->with('sidebarFeatures', $sidebarFeatures);
        });
    }
}
