<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class AgendaReminderNotification extends Notification
{
    use Queueable;

    private $materi;
    private $messageCustom;

    public function __construct($materi, $messageCustom = null)
    {
        $this->materi = $materi;
        $this->messageCustom = $messageCustom;
    }

    public function via($notifiable)
    {
        return ['database'];
    }

    public function toArray($notifiable)
    {
        // Jika ada pesan custom (misal untuk alert monitoring di server Admin)
        if ($this->messageCustom) {
            return [
                'title' => 'Pengingat Pelatihan (H-1)',
                'message' => $this->messageCustom,
                'icon' => 'bx-alarm',
                'type' => 'warning'
            ];
        }

        // Pesan standar untuk server Mitra
        return [
            'title' => 'Pengingat Pelatihan (H-1)',
            'message' => "Agenda \"{$this->materi}\" akan dilaksanakan besok (24 jam lagi). Bersiaplah!",
            'icon' => 'bx-alarm',
            'type' => 'warning'
        ];
    }
}