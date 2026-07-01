<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class PasswordUpdatedNotification extends Notification
{
    use Queueable;

    private $message;

    public function __construct($message = 'Password akun berhasil diperbarui.')
    {
        $this->message = $message;
    }

    public function via($notifiable)
    {
        return ['database'];
    }

    public function toArray($notifiable)
    {
        return [
            'title' => 'Keamanan Akun',
            'message' => $this->message,
            'icon' => 'bx-key',
            'type' => 'danger'
        ];
    }
}