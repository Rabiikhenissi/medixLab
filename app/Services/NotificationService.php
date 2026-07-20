<?php

namespace App\Services;

use App\Models\Notification;
use App\Models\User;
use Illuminate\Support\Facades\Mail;

class NotificationService
{
    /**
     * Create a notification for a user.
     */
    public static function send(int $userId, string $title, string $message, string $type = 'general', ?int $referenceId = null): Notification
    {
        $notification = Notification::create([
            'user_id'           => $userId,
            'title'             => $title,
            'message'           => $message,
            'notification_type' => $type,
            'reference_id'      => $referenceId,
        ]);

        // Send email notification for critical events (Task 3.3)
        if (in_array($type, ['access_request', 'exam_request', 'stock_alert'])) {
            try {
                $user = User::find($userId);
                if ($user && $user->email) {
                    Mail::raw("Bonjour {$user->first_name} {$user->last_name},\n\nVous avez reçu une notification importante sur votre espace Medix eSanté :\n\nSujet : {$title}\nMessage : {$message}\n\nConnectez-vous à la plateforme pour plus de détails.\n\nCordialement,\nL'équipe Medix eSanté.", function ($mail) use ($user, $title) {
                        $mail->to($user->email)
                             ->subject("[Medix eSanté] Notification : {$title}");
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
