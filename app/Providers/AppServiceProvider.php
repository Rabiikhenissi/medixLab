<?php

namespace App\Providers;

use App\Models\DoctorPatientAccess;
use App\Models\ExamParameter;
use App\Models\ExamRequest;
use App\Models\ExamRequestItem;
use App\Models\Feature;
use App\Models\MachineConfiguration;
use App\Models\ResultLabo;
use App\Models\ResultLaboDetail;
use App\Models\Sample;
use App\Models\User;
use App\Observers\AuditableObserver;
use App\Services\ExamRequestService;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

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
        // Medico-legal audit trail for sensitive models
        $observer = AuditableObserver::class;
        User::observe($observer);
        ExamRequest::observe($observer);
        ExamRequestItem::observe($observer);
        Sample::observe($observer);
        ResultLabo::observe($observer);
        ResultLaboDetail::observe($observer);
        MachineConfiguration::observe($observer);
        ExamParameter::observe($observer);
        DoctorPatientAccess::observe($observer);

        Gate::before(function ($user, $ability) {
            if (method_exists($user, 'hasPermission') && $user->hasPermission($ability)) {
                return true;
            }
        });

        // Customise the branded password-reset email
        ResetPassword::toMailUsing(function ($notifiable, string $token) {
            if ($notifiable->doctor) {
                $role = 'doctor';
            } elseif ($notifiable->patient) {
                $role = 'patient';
            } elseif ($notifiable->staff) {
                $role = 'center';
            } else {
                $role = 'patient';
            }

            $url = url("/{$role}/reset-password/".$token.'?email='.urlencode($notifiable->email));

            return (new MailMessage)
                ->subject('Réinitialisation de votre mot de passe — Medix eSanté')
                ->view('emails.reset-password', ['url' => $url]);
        });

        $sidebarFeaturesComposer = function ($view) {
            $sidebarFeatures = Feature::where('is_archive', false)
                ->where('is_sidebar', true)
                ->orderBy('order', 'asc')
                ->get();
            $view->with('sidebarFeatures', $sidebarFeatures);
        };

        View::composer('layouts.admin', $sidebarFeaturesComposer);
        View::composer('components.layouts.auth', $sidebarFeaturesComposer);
        View::composer('layouts.center', $sidebarFeaturesComposer);

        // TIER 2.3 — Check access expiry once daily on any request
        $todayKey = 'access_expiry_check_'.now()->format('Y-m-d');
        if (! session()->has($todayKey)) {
            session()->put($todayKey, true);
            try {
                ExamRequestService::checkAccessExpiry();
            } catch (\Exception $e) {
                \Log::error('Access expiry check failed: '.$e->getMessage());
            }
        }
    }
}
