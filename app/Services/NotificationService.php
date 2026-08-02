<?php

namespace App\Services;

use App\Models\Notification;
use App\Models\User;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\View;

class NotificationService
{
    /**
     * Persist a notification for a user and optionally email it.
     *
     * @param  int  $userId  recipient user id
     * @param  string  $title  notification title
     * @param  string  $message  notification body
     * @param  string  $type  notification type (general, exam_request, access_request, stock_alert)
     * @param  int|null  $referenceId  optional id of the referenced resource
     * @return Notification the created notification
     */
    public static function send(int $userId, string $title, string $message, string $type = 'general', ?int $referenceId = null): Notification
    {
        // Persist the notification in the database
        $notification = Notification::create([
            'user_id' => $userId,
            'title' => $title,
            'message' => $message,
            'notification_type' => $type,
            'reference_id' => $referenceId,
        ]);

        // Send email only for actionable notification types
        if (in_array($type, ['access_request', 'exam_request', 'stock_alert'])) {
            try {
                // Resolve the recipient user and their role
                $user = User::select('id', 'email', 'doctor_id', 'staff_id', 'patient_id')->find($userId);
                if ($user && $user->email) {
                    $role = 'patient';
                    if ($user->doctor) {
                        $role = 'doctor';
                    } elseif ($user->staff) {
                        $role = 'center';
                    }

                    // Build a link pointing to the recipient's dashboard
                    $actionUrl = config('app.url')."/{$role}/dashboard";
                    $actionLabel = 'Ouvrir mon espace';

                    // Pick a role- and type-specific action label
                    if ($type === 'access_request' && $role === 'patient') {
                        $actionLabel = 'Gérer les accès';
                    } elseif ($type === 'exam_request' && $role === 'doctor') {
                        $actionLabel = 'Voir la demande';
                    } elseif ($type === 'stock_alert') {
                        $actionLabel = 'Gérer le stock';
                    }

                    // Render the HTML email and send it
                    $html = View::make('emails.notification', [
                        'title' => $title,
                        'message' => $message,
                        'type' => $type,
                        'actionUrl' => $actionUrl,
                        'actionLabel' => $actionLabel,
                    ])->render();

                    Mail::raw("Medix eSanté — {$title}\n\n{$message}", function ($mail) use ($user, $title, $html) {
                        $mail->to($user->email)
                            ->subject("[Medix eSanté] {$title}")
                            ->html($html);
                    });
                }
            } catch (\Exception $e) {
                // Log email failures without breaking the notification flow
                \Log::error("Failed to send email notification to user ID {$userId}: ".$e->getMessage());
            }
        }

        return $notification;
    }

    /**
     * Notify a user about a new exam request.
     *
     * @param  int  $userId  recipient user id
     * @param  string  $message  notification body
     * @param  int  $examRequestId  id of the related exam request
     * @return Notification the created notification
     */
    public static function examRequest(int $userId, string $message, int $examRequestId): Notification
    {
        return self::send($userId, 'Demande d\'analyses', $message, 'exam_request', $examRequestId);
    }

    /**
     * Notify a user about a new access request.
     *
     * @param  int  $userId  recipient user id
     * @param  string  $message  notification body
     * @param  int  $accessId  id of the related access request
     * @return Notification the created notification
     */
    public static function accessRequest(int $userId, string $message, int $accessId): Notification
    {
        return self::send($userId, 'Demande d\'accès', $message, 'access_request', $accessId);
    }

    /**
     * Notify a user that their exam results are ready.
     *
     * @param  int  $userId  recipient user id
     * @param  string  $message  notification body
     * @param  int  $examRequestId  id of the related exam request
     * @return Notification the created notification
     */
    public static function resultsReady(int $userId, string $message, int $examRequestId): Notification
    {
        return self::send($userId, 'Analyses complétées', $message, 'exam_request', $examRequestId);
    }

    /**
     * Alert lab staff that a consumable's stock is below the minimum.
     *
     * @param  int  $userId  recipient staff user id
     * @param  string  $consumableName  name of the low-stock consumable
     * @param  string  $labName  name of the affected laboratory
     * @return Notification the created notification
     */
    public static function stockAlert(int $userId, string $consumableName, string $labName): Notification
    {
        return self::send(
            $userId,
            'Stock bas — '.$consumableName,
            'Le stock de «'.$consumableName.'» au laboratoire «'.$labName.'» est sous le seuil minimum. Veuillez réapprovisionner.',
            'stock_alert'
        );
    }
}
