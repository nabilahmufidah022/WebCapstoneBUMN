<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;
use App\Models\MitraEventParticipation;
use App\Notifications\AgendaReminderNotification;
use App\Models\User;
use Carbon\Carbon;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

/*
|--------------------------------------------------------------------------
| Console Routes / Task Scheduling - Rumah BUMN Jakarta
|--------------------------------------------------------------------------
*/

Schedule::call(function () {
    // 1. Ambil format tanggal besok hari (H-1 dari pelatihan)
    $tanggalBesok = Carbon::tomorrow()->toDateString();
    
    // 2. Cari agenda pelatihan yang jatuh tempo besok dan statusnya masih 'Akan Datang'
    $agendas = MitraEventParticipation::whereDate('tanggal_pelatihan', $tanggalBesok)
                ->where('status', 'Akan Datang')
                ->get();

    foreach ($agendas as $agenda) {
        // Ambil data jam pelaksanaan (Format Jam:Menit)
        $waktu = Carbon::parse($agenda->tanggal_pelatihan)->format('H:i');
        
        // --- AKSI 1: KIRIM KE SERVER MITRA ---
        if ($agenda->mitra && $agenda->mitra->user) {
            $userMitra = $agenda->mitra->user;
            $userMitra->notify(new AgendaReminderNotification($agenda->judul_pelatihan));
        }
        
        // --- AKSI 2: KIRIM KE SERVER ADMIN ---
        $admins = User::where('usertype', 'admin')->get();
        foreach ($admins as $admin) {
            $namaPT = $agenda->mitra->nama_perusahaan ?? 'Mitra Terkait';
            $pesanAdmin = "Monitoring Pelatihan: Kelas \"{$agenda->judul_pelatihan}\" (Mitra: {$namaPT}) akan berlangsung besok jam {$waktu} WIB.";
            
            $admin->notify(new AgendaReminderNotification($agenda->judul_pelatihan, $pesanAdmin));
        }
    }
})->dailyAt('09:00'); 