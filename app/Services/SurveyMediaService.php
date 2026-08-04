<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Laravel\Facades\Image;

class SurveyMediaService
{
    /**
     * Simpan file sesuai jenisnya.
     */
    public function store(UploadedFile $file, int $surveyId): string
    {
        $mime = $file->getMimeType();

        if (str_starts_with($mime, 'image/')) {
            return $this->resizeImage($file, $surveyId);
        }

        if (str_starts_with($mime, 'video/')) {
            return $this->compressVideo($file, $surveyId);
        }

        return $this->storePdf($file, $surveyId);
    }

    public function resizeImage(UploadedFile $file, int $surveyId): string
    {
        $image = Image::read($file);
        $image->scaleDown(width: 1600,height: 1600);
        $filename =uniqid().'.jpg';
        $path ="survey/{$surveyId}/".$filename;
        Storage::disk('public')->put($path,(string) $image->toJpeg(75)            );
        return $path;
    }

    public function compressVideo(UploadedFile $file,int $surveyId): string {
        return $file->store("survey/{$surveyId}","public");
    }

    public function storePdf(UploadedFile $file,int $surveyId): string {
        return $file->store("survey/{$surveyId}","public");
    }

    /**
     * Thumbnail video.
     */
    public function generateThumbnailVideo(string $videoPath): ?string
    {
        return null;
    }

    public function deleteMedia(string $path): bool {
        if(Storage::disk('public')->exists($path)){
            return Storage::disk('public')->delete($path);
        }
        return false;
    }
}