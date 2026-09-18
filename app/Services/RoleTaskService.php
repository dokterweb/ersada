<?php

namespace App\Services;

use App\Models\Pengajuan;
use App\Models\Survey;
use Illuminate\Support\Facades\Auth;

class RoleTaskService
{
    /**
     * Mapping role Spatie ke group tugas.
     */
    protected array $roleGroups = [

        // Pimpinan
        'kacab'     => 'pimpinan',
        'direktur'  => 'pimpinan',
        'komisaris' => 'pimpinan',

        // Marketing
        'marketing'    => 'marketing',
        'spvmarketing' => 'spvmarketing',

        // Survey
        'spvsurveyor' => 'spvsurveyor',
        'surveyor'    => 'surveyor',

        // Operasional
        'admincabang' => 'admincabang',
        'staff' => 'staff',

        // System
        'superadmin' => 'superadmin',
    ];


    /**
     * Informasi dasar masing-masing group.
     */
    protected array $tasks = [

        'pimpinan' => [
            'title' => 'Monitoring Pimpinan',
            'icon' => 'bell',
            'color' => 'indigo',

            'description' =>
                'Memantau kondisi dan kinerja operasional pembiayaan.',

            'tasks' => [],

            'flow' =>
                'Dashboard → Monitoring → Reports',
        ],

        'marketing' => [
            'title' => 'Tugas Marketing',
            'icon' => 'briefcase',
            'color' => 'blue',

            'description' =>
                'Mengelola proses pengajuan dan pembiayaan nasabah.',

            'tasks' => [],

            'flow' =>
                'Pengajuan → Survey → Approval → Pembiayaan',
        ],

        'spvmarketing' => [
            'title' => 'Tugas SPV Marketing',
            'icon' => 'users',
            'color' => 'azure',

            'description' =>
                'Memantau proses pengajuan dan pembiayaan marketing.',

            'tasks' => [],

            'flow' =>
                'Pengajuan → Survey → Approval → Pembiayaan',
        ],

        'spvsurveyor' => [
            'title' => 'Tugas SPV Surveyor',
            'icon' => 'clipboard',
            'color' => 'purple',

            'description' =>
                'Mengatur dan memantau proses survey nasabah.',

            'tasks' => [],

            'flow' =>
                'Disposisi → Survey → Review Survey',
        ],

        'surveyor' => [
            'title' => 'Tugas Surveyor',
            'icon' => 'map-pin',
            'color' => 'orange',

            'description' =>
                'Melakukan survey dan melengkapi hasil survey.',

            'tasks' => [],

            'flow' =>
                'Tugas Survey → Survey → Submit Hasil',
        ],

        'admincabang' => [
            'title' => 'Tugas Admin Cabang',
            'icon' => 'building',
            'color' => 'green',

            'description' =>
                'Memproses administrasi pembiayaan pada cabang.',

            'tasks' => [],

            'flow' =>
                'Approval → Pembiayaan → Akad → Pencairan',
        ],

        'staff' => [
            'title' => 'Tugas Staff',
            'icon' => 'briefcase',
            'color' => 'cyan',

            'description' =>
                'Membantu proses administrasi dan operasional.',

            'tasks' => [],

            'flow' =>
                'Pembiayaan → Pencairan → Angsuran → Pelunasan',
        ],

        'superadmin' => [
            'title' => 'Administrator Sistem',
            'icon' => 'settings',
            'color' => 'red',

            'description' =>
                'Mengelola sistem dan seluruh data aplikasi.',

            'tasks' => [],

            'flow' =>
                'System → Master Data → User → Monitoring',
        ],
    ];


    /**
     * Ambil informasi tugas user.
     */
    public function getForUser($user = null): array
    {
        $user = $user ?: Auth::user();

        if (!$user) {
            return $this->defaultTask();
        }

        $roles = $user->getRoleNames()
            ->map(fn ($role) => strtolower($role));

        foreach ($roles as $role) {

            if (!isset($this->roleGroups[$role])) {
                continue;
            }

            $group = $this->roleGroups[$role];

            $info = $this->tasks[$group] ?? $this->defaultTask();

            $info['tasks'] = [];

            switch ($group) {

                case 'pimpinan':

                    $info['tasks'] = $this->getPimpinanTasks($user);

                    break;

                case 'marketing':
                case 'spvmarketing':
                case 'admincabang':

                    $info['tasks'] = $this->getPembiayaanTasks($user);

                    break;
            }

            return $info;
        }

        return $this->defaultTask();
    }


    /**
     * Tugas dinamis untuk Pimpinan.
     */
    protected function getPimpinanTasks($user): array
    {
        $tasks = [];

        /*
        |--------------------------------------------------------------------------
        | PENGAJUAN MENUNGGU TINDAKAN PIMPINAN
        |--------------------------------------------------------------------------
        |
        | Status yang menjadi pekerjaan Pimpinan:
        |
        | - menunggu_pimpinan
        | - menunggu_keputusan
        |
        */


        $query = Pengajuan::query()
            ->whereIn('status', [
                'menunggu_pimpinan',
                'menunggu_keputusan',
            ]);


        /*
        |--------------------------------------------------------------------------
        | SCOPE KACAB
        |--------------------------------------------------------------------------
        |
        | Kacab hanya melihat pengajuan dari cabangnya sendiri.
        |
        | Direktur dan Komisaris melihat seluruh cabang.
        |
        */

        if ($user->hasRole('kacab')) {

            $cabangId = $user->karyawan?->cabang_id;

            if ($cabangId) {

                $query->where(
                    'cabang_id',
                    $cabangId
                );

            } else {

                // Jika Kacab tidak memiliki cabang,
                // jangan tampilkan data.
                $query->whereRaw('1 = 0');

            }
        }


        /*
        |--------------------------------------------------------------------------
        | HITUNG JUMLAH
        |--------------------------------------------------------------------------
        */

        $jumlahPengajuan = $query->count();


        /*
        |--------------------------------------------------------------------------
        | MASUKKAN KE TASK CENTER
        |--------------------------------------------------------------------------
        */

        if ($jumlahPengajuan > 0) {

            $tasks[] = [
                'title' => 'Pengajuan menunggu keputusan',
                'count' => $jumlahPengajuan,
                'icon' => 'file-text',
                'url' => route('pimpinan.index'),
            ];
        }


        return $tasks;
    }

    protected function getPembiayaanTasks($user): array
    {
        $tasks = [];

        /*
        |--------------------------------------------------------------------------
        | PENGAJUAN DISETUJUI
        |--------------------------------------------------------------------------
        |
        | Semua role dalam group ini hanya dapat melihat
        | pengajuan dari cabangnya sendiri.
        |
        */

        $query = Pengajuan::query()
            ->where('status', 'disetujui');


        /*
        |--------------------------------------------------------------------------
        | DATA KARYAWAN
        |--------------------------------------------------------------------------
        */

        $karyawan = $user->karyawan;

        if (!$karyawan) {
            return $tasks;
        }


        /*
        |--------------------------------------------------------------------------
        | FILTER CABANG
        |--------------------------------------------------------------------------
        */

        $query->where(
            'cabang_id',
            $karyawan->cabang_id
        );


        /*
        |--------------------------------------------------------------------------
        | FILTER MARKETING
        |--------------------------------------------------------------------------
        |
        | Admin Cabang:
        |   Semua marketing di cabang tersebut.
        |
        | Marketing:
        |   Hanya marketing_id miliknya.
        |
        | SPV Marketing:
        |   Hanya marketing_id miliknya.
        |
        */

        if ($user->hasAnyRole([
            'marketing',
            'spvmarketing',
        ])) {

            $query->where(
                'marketing_id',
                $karyawan->id
            );
        }


        /*
        |--------------------------------------------------------------------------
        | HITUNG
        |--------------------------------------------------------------------------
        */

        $jumlah = $query->count();


        /*
        |--------------------------------------------------------------------------
        | TASK
        |--------------------------------------------------------------------------
        */

        if ($jumlah > 0) {

            $tasks[] = [
                'title' => 'Pengajuan disetujui',
                'count' => $jumlah,
                'icon' => 'file-text',
                'url' => route('pembiayaan.index'),
            ];
        }


        return $tasks;
    }
    /**
     * Default.
     */
    protected function defaultTask(): array
    {
        return [
            'title' => 'Informasi Pekerjaan',
            'icon' => 'info',
            'color' => 'blue',

            'description' =>
                'Informasi pekerjaan untuk akun Anda.',

            'tasks' => [],

            'flow' =>
                'Pengajuan → Survey → Approval → Pembiayaan',
        ];
    }
}
