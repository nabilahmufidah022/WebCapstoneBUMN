<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class ProfileUpdatedNotification extends Notification
{
    use Queueable;

    private $message;

    public function __construct($message)
    {
        $this->message = $message;
    }

    public function via($notifiable)
    {
        return ['database']; // Menyimpan notifikasi ke dalam database
    }

    public function toArray($notifiable)
    {
        return [
            'title' => 'Pembaruan Profil',
            'message' => $this->message,
            'icon' => 'bx-user-circle',
            'type' => 'info' // Untuk warna badge di topbar
        ];
    }
}