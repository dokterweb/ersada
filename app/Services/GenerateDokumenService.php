<?php

namespace App\Services;

use App\Models\Akad;
use Illuminate\Support\Facades\File;
use PhpOffice\PhpWord\TemplateProcessor;

class GenerateDokumenService
{
    protected AkadService $akadService;

    public function __construct(AkadService $akadService)
    {
        $this->akadService = $akadService;
    }

    /**
     * Generate Dokumen Akad
     */
    public function generateAkad(Akad $akad): array
    {
        /*
        |--------------------------------------------------------------------------
        | Ambil seluruh placeholder
        |--------------------------------------------------------------------------
        */

        $data = $this->akadService->templateData($akad);

        /*
        |--------------------------------------------------------------------------
        | Tentukan Template
        |--------------------------------------------------------------------------
        */

        $template = $this->getTemplate($data);

        /*
        |--------------------------------------------------------------------------
        | Nama File Output
        |--------------------------------------------------------------------------
        */

        // $filename = sprintf('%s_%s.docx',$akad->nomor_akad,now()->format('YmdHis'));
        $filename = str($akad->nomor_akad)
        ->replace('/', '-')
        ->append('_'.now()->format('YmdHis').'.docx')
        ->toString();

        /*
        |--------------------------------------------------------------------------
        | Generate
        |--------------------------------------------------------------------------
        */

        $result = $this->generateWord(
            $template,
            $data,
            $filename
        );
        
        /*
        |--------------------------------------------------------------------------
        | Simpan informasi file
        |--------------------------------------------------------------------------
        */
        
        $akad->update([
            'file_word'    => $result['filename'],
            'generated_at' => now(),
        ]);
        
        return $result;
    }

    /**
     * Tentukan template berdasarkan jenis pekerjaan
     */
    protected function getTemplate(array $data): string
    {
        if (
            strtolower($data['jenis_pekerjaan']) == 'karyawan'
        ) {

            return resource_path(
                'templates/akad_dengan_atm.docx'
            );

        }

        return resource_path(
            'templates/akad_tanpa_atm.docx'
        );
    }

    /**
     * Engine Generate Word
     */
    protected function generateWord(string $template,array $data,string $filename): array
    {

        /*
        |--------------------------------------------------------------------------
        | Folder Output
        |--------------------------------------------------------------------------
        */

        $folder = storage_path('app/public/akad');

        if (!File::exists($folder)) {
            File::makeDirectory($folder, 0755, true);
        }

        $output = $folder . DIRECTORY_SEPARATOR . $filename;

        /*
        |--------------------------------------------------------------------------
        | Load Template
        |--------------------------------------------------------------------------
        */

        $processor = new TemplateProcessor($template);

        /*
        |--------------------------------------------------------------------------
        | Isi Placeholder
        |--------------------------------------------------------------------------
        */

        foreach ($data as $key => $value) {

            /*
             * Abaikan collection / array
             */

            if (is_array($value) || is_object($value)) {

                continue;

            }

            $processor->setValue($key,$value ?? '');

        }

        /*
        |--------------------------------------------------------------------------
        | Simpan
        |--------------------------------------------------------------------------
        */

        $processor->saveAs($output);

        return [

            'success' => true,

            'filename' => $filename,

            'path' => $output,

            'public_path' => 'storage/akad/' . $filename,

        ];

    }

}