<?php

namespace Database\Seeders;

use App\Enums\AttendanceStatus;
use App\Enums\Role;
use App\Enums\SessionStatus;
use App\Models\AcademicYear;
use App\Models\Attendance;
use App\Models\AttendanceSession;
use App\Models\Classroom;
use App\Models\Guardian;
use App\Models\Schedule;
use App\Models\Student;
use App\Models\Subject;
use App\Models\Teacher;
use App\Models\User;
use App\Models\UserNotificationPreference;
use App\Notifications\AppNotification;
use Illuminate\Database\Seeder;

class DemoSeeder extends Seeder
{
    public function run(): void
    {
        if (app()->environment('production')) {
            $this->command?->error('DemoSeeder tidak boleh dijalankan di production.');
            return;
        }

        mt_srand(42); // data demo deterministik

        $makeUser = function (string $name, string $email, string $username, Role $role, ?string $phone = null): User {
            $user = User::factory()->withRole($role)->create([
                'name' => $name, 'email' => $email, 'username' => $username, 'phone' => $phone,
            ]);
            UserNotificationPreference::create(['user_id' => $user->id]);
            return $user;
        };

        // Admin
        $makeUser('Administrator Sekolah', 'admin@absensiswa.test', 'admin', Role::Admin);

        // Tahun ajaran
        $year = AcademicYear::create([
            'name' => '2026/2027', 'start_date' => '2026-07-13', 'end_date' => '2027-06-26', 'is_active' => true,
        ]);

        // Mata pelajaran + guru pengampu
        $subjectData = [
            ['MAT', 'Mathematics', '#1765E8', 'Dedi Kurniawan'],
            ['IND', 'Indonesian Language', '#E7A52A', 'Ratna Puspita'],
            ['IPA', 'Science', '#16A36A', 'Yoga Prasetyo'],
            ['ENG', 'English', '#4D8DF5', 'Nia Anggraini'],
            ['PKN', 'Civic Education', '#DC4545', 'Agung Setiawan'],
        ];

        $subjects = [];
        $teacherBySubject = [];
        $teacherUserIds = [];
        foreach ($subjectData as $i => [$code, $name, $color, $teacherName]) {
            $subjects[$i] = Subject::create(['code' => $code, 'name' => $name, 'color' => $color, 'default_duration_minutes' => 90]);
            $user = $makeUser($teacherName, 'guru'.($i + 1).'@absensiswa.test', 'guru'.($i + 1), Role::Teacher);
            $teacher = Teacher::create(['user_id' => $user->id]);
            $teacherBySubject[$i] = $teacher;
            $teacherUserIds[$teacher->id] = $user->id;
        }

        // Kelas
        $classroom = Classroom::create([
            'academic_year_id' => $year->id, 'name' => '8A', 'grade_level' => 8,
            'homeroom_teacher_id' => $teacherBySubject[0]->id,
        ]);

        // Siswa
        $studentData = [
            ['Raka Mahendra', '2024001', '2011-03-14', 'male'],
            ['Dinda Safitri', '2024002', '2011-07-02', 'female'],
            ['Fajar Nugroho', '2024003', '2010-11-21', 'male'],
            ['Salsa Amelia', '2024004', '2011-01-30', 'female'],
            ['Bagas Pratama', '2024005', '2011-09-09', 'male'],
        ];

        $students = [];
        foreach ($studentData as $i => [$name, $number, $birth, $gender]) {
            $user = $makeUser($name, 'siswa'.($i + 1).'@absensiswa.test', 'siswa'.($i + 1), Role::Student, '0812000000'.($i + 1));
            $students[$i] = Student::create([
                'user_id' => $user->id, 'classroom_id' => $classroom->id,
                'student_number' => $number, 'birth_date' => $birth, 'gender' => $gender,
            ]);
        }

        // Orang tua
        $parent1 = Guardian::create(['user_id' => $makeUser('Bambang Mahendra', 'ortu1@absensiswa.test', 'ortu1', Role::Guardian)->id]);
        $parent2 = Guardian::create(['user_id' => $makeUser('Wulan Safitri', 'ortu2@absensiswa.test', 'ortu2', Role::Guardian)->id]);

        $pivot = ['relationship' => 'parent', 'is_verified' => true, 'verified_at' => now()];
        $parent1->students()->attach($students[0]->id, $pivot);
        $parent2->students()->attach([$students[1]->id => $pivot, $students[2]->id => $pivot]);

        // Jadwal Senin-Jumat: 5 slot waktu, mata pelajaran dirotasi tiap hari
        $slots = [
            ['07:00', '08:30', 'Room 101'],
            ['08:45', '10:15', 'Room 102'],
            ['10:30', '12:00', 'Lab 1'],
            ['12:30', '14:00', 'Room 103'],
            ['14:15', '15:45', 'Room 104'],
        ];

        for ($day = 1; $day <= 5; $day++) {
            foreach ($slots as $s => [$start, $end, $room]) {
                $idx = ($s + $day - 1) % 5;
                Schedule::create([
                    'classroom_id' => $classroom->id,
                    'subject_id' => $subjects[$idx]->id,
                    'teacher_id' => $teacherBySubject[$idx]->id,
                    'day_of_week' => $day,
                    'start_time' => $start.':00',
                    'end_time' => $end.':00',
                    'room' => $room,
                ]);
            }
        }

        // Riwayat presensi 30 hari terakhir (hari kerja)
        $today = now()->startOfDay();
        for ($d = 30; $d >= 1; $d--) {
            $date = $today->copy()->subDays($d);
            if ($date->isWeekend()) {
                continue;
            }

            foreach (Schedule::where('day_of_week', $date->dayOfWeekIso)->orderBy('start_time')->get() as $schedule) {
                $start = $date->copy()->setTimeFromTimeString($schedule->start_time);
                $end = $date->copy()->setTimeFromTimeString($schedule->end_time);

                $session = AttendanceSession::create([
                    'schedule_id' => $schedule->id,
                    'created_by' => $teacherUserIds[$schedule->teacher_id],
                    'session_date' => $date->toDateString(),
                    'status' => SessionStatus::Closed,
                    'started_at' => $start,
                    'late_after' => $start->copy()->addMinutes(10),
                    'closed_at' => $end,
                ]);

                foreach ($students as $student) {
                    $roll = mt_rand(1, 100);
                    [$status, $checkIn] = match (true) {
                        $roll <= 88 => [AttendanceStatus::Present, $start->copy()->addMinutes(mt_rand(0, 5))],
                        $roll <= 93 => [AttendanceStatus::Late, $start->copy()->addMinutes(mt_rand(11, 30))],
                        $roll <= 96 => [AttendanceStatus::Sick, null],
                        $roll <= 98 => [AttendanceStatus::Permitted, null],
                        default => [AttendanceStatus::Absent, null],
                    };

                    Attendance::create([
                        'attendance_session_id' => $session->id,
                        'student_id' => $student->id,
                        'attendance_date' => $date->toDateString(),
                        'status' => $status,
                        'check_in_at' => $checkIn,
                        'source' => $checkIn ? 'qr' : 'manual',
                    ]);
                }
            }
        }

        // Notifikasi demo untuk siswa pertama
        $u = $students[0]->user;
        $u->notify(new AppNotification('attendance_confirmed', 'Attendance Confirmed', 'Presensi Anda hari ini sudah tercatat.'));
        $u->notify(new AppNotification('schedule_reminder', 'Schedule Reminder', 'Pelajaran berikutnya dimulai 15 menit lagi.'));
        $u->notify(new AppNotification('announcement', 'School Announcement', 'Akan ada kegiatan sekolah pekan depan.'));
        $u->notify(new AppNotification('attendance_update', 'Attendance Update', 'Status presensi Anda diperbarui oleh guru.'));
    }
}