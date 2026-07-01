<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class HandoverPicNotification extends Notification
{
    use Queueable;

    private $mitraName;
    private $isAdminReceiver;

    public function __construct($mitraName, $isAdminReceiver = false)
    {
        $this->mitraName = $mitraName;
        $this->isAdminReceiver = $isAdminReceiver;
    }

    public function via($notifiable)
    {
        return ['database'];
    }

    public function toArray($notifiable)
    {
        // Jika notifikasi ini masuk ke server Admin
        if ($this->isAdminReceiver) {
            return [
                'title' => 'Handover PIC Mitra',
                'message' => "Mitra {$this->mitraName} melakukan handover pic, silahkan cek.",
                'icon' => 'bx-transfer-alt',
                'type' => 'primary'
            ];
        }

        // Jika notifikasi ini masuk ke server Mitra itu sendiri
        return [
            'title' => 'Handover PIC Berhasil',
            'message' => 'Proses pengalihan (handover) PIC usaha Anda telah sukses dicatat.',
            'icon' => 'bx-check-double',
            'type' => 'success'
        ];
    }
}