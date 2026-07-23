<?php

namespace App\Services;

use App\Models\Notification;
use App\Models\User;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\View;

class NotificationService
{
    public static function send(int $userId, string $title, string $message, string $type = 'general', ?int $referenceId = null): Notification
    {
        $notification = Notification::create([
            'user_id'           => $userId,
            'title'             => $title,
            'message'           => $message,
            'notification_type' => $type,
            'reference_id'      => $referenceId,
        ]);

        if (in_array($type, ['access_request', 'exam_request', 'stock_alert'])) {
            try {
                $user = User::find($userId);
                if ($user && $user->email) {
                    $role = 'patient';
                    if ($user->doctor) {
                        $role = 'doctor';
                    } elseif ($user->staff) {
                        $role = 'center';
                    }

                    $actionUrl = config('app.url') . "/{$role}/dashboard";
                    $actionLabel = 'Ouvrir mon espace';

                    if ($type === 'access_request' && $role === 'patient') {
                        $actionLabel = 'Gérer les accès';
                    } elseif ($type === 'exam_request' && $role === 'doctor') {
                        $actionLabel = 'Voir la demande';
                    } elseif ($type === 'stock_alert') {
                        $actionLabel = 'Gérer le stock';
                    }

                    $html = View::make('emails.notification', [
                        'title'       => $title,
                        'message'     => $message,
                        'type'        => $type,
                        'actionUrl'   => $actionUrl,
                        'actionLabel' => $actionLabel,
                    ])->render();

                    Mail::raw("Medix eSanté — {$title}\n\n{$message}", function ($mail) use ($user, $title, $html) {
                        $mail->to($user->email)
                             ->subject("[Medix eSanté] {$title}")
                             ->html($html);
                    });
                }
            } catch (\Exception $e) {
                \Log::error("Failed to send email notification to user ID {$userId}: " . $e->getMessage());
            }
        }

        return $notification;
    }

    public static function examRequest(int $userId, string $message, int $examRequestId): Notification
    {
        return self::send($userId, 'Demande d\'analyses', $message, 'exam_request', $examRequestId);
    }

    public static function accessRequest(int $userId, string $message, int $accessId): Notification
    {
        return self::send($userId, 'Demande d\'accès', $message, 'access_request', $accessId);
    }

    public static function resultsReady(int $userId, string $message, int $examRequestId): Notification
    {
        return self::send($userId, 'Analyses complétées', $message, 'exam_request', $examRequestId);
    }

    public static function stockAlert(int $userId, string $consumableName, string $labName): Notification
    {
        return self::send(
            $userId,
            'Stock bas — ' . $consumableName,
            'Le stock de «' . $consumableName . '» au laboratoire «' . $labName . '» est sous le seuil minimum. Veuillez réapprovisionner.',
            'stock_alert'
        );
    }
}
