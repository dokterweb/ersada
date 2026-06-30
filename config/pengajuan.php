<?php 

return [

    'documents' => [

        /*
        |--------------------------------------------------------------------------
        | PAYROLL (dinas/perkebunan)
        |--------------------------------------------------------------------------
        */
        'payroll' => [

            'required' => [

                [
                    'code' => 'atm_gaji',
                    'label' => 'ATM & Buku Tabungan Gaji',
                ],

                [
                    'code' => 'bpjs',
                    'label' => 'BPJS Ketenagakerjaan',
                ],

                [
                    'code' => 'sk_kerja',
                    'label' => 'SK Perjanjian Kerja Terbaru',
                ],

                [
                    'code' => 'ktp_suami_istri',
                    'label' => 'KTP Suami Istri',
                ],

                [
                    'code' => 'kk',
                    'label' => 'Kartu Keluarga',
                ],

                [
                    'code' => 'buku_nikah',
                    'label' => 'Buku Nikah',
                ],
            ],

            /*
            salah satu wajib
            */
            'one_of' => [

                [
                    'code' => 'bpkb',
                    'label' => 'BPKB'
                ],

                [
                    'code' => 'surat_tanah',
                    'label' => 'Surat Tanah'
                ]
            ]
        ],

        /*
        |--------------------------------------------------------------------------
        | NON PAYROLL (umum)
        |--------------------------------------------------------------------------
        */
        'non_payroll' => [

            'required' => [

                [
                    'code' => 'ktp_suami_istri',
                    'label' => 'KTP Suami Istri'
                ],

                [
                    'code' => 'kk',
                    'label' => 'Kartu Keluarga'
                ],

                [
                    'code' => 'buku_nikah',
                    'label' => 'Buku Nikah'
                ]
            ],

            /*
            salah satu wajib
            */
            'one_of' => [

                [
                    'code' => 'bpkb',
                    'label' => 'BPKB'
                ],

                [
                    'code' => 'surat_tanah',
                    'label' => 'Surat Tanah'
                ]
            ]
        ],

        /*
        |--------------------------------------------------------------------------
        | OPTIONAL
        |--------------------------------------------------------------------------
        */
        'optional' => [

            [
                'code' => 'akta_anak',
                'label' => 'Akta Kelahiran Anak'
            ],

            [
                'code' => 'ijazah',
                'label' => 'Ijazah Terakhir'
            ]
        ]
    ]
];