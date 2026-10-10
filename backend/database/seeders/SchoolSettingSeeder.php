<?php

namespace Database\Seeders;

use App\Models\SchoolSetting;
use Illuminate\Database\Seeder;

class SchoolSettingSeeder extends Seeder
{
    public function run(): void
    {
        if (SchoolSetting::query()->exists()) {
            return;
        }

        SchoolSetting::create([
            'school_name' => 'SMP Contoh Nusantara',
            'address' => 'Alamat sekolah (ubah di panel admin)',
            'timezone' => 'Asia/Jakarta',
            'late_after_minutes' => 10,
            'qr_rotation_seconds' => 30,
        ]);
    }
}