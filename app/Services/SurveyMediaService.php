<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
// use Intervention\Image\Laravel\Facades\Image;
// use Intervention\Image\Encoders\JpegEncoder;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;

class SurveyMediaService
{
    protected ImageManager $image;

    public function __construct()
    {
        $this->image = new ImageManager(new Driver());
    }

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

/*     public function resizeImage(UploadedFile $file, int $surveyId): string
    {
        $image = Image::read($file);
        $image->scaleDown(width: 1600,height: 1600);
        $filename =uniqid().'.jpg';
        $path ="survey/{$surveyId}/".$filename;
        Storage::disk('public')->put($path,(string) $image->toJpeg(75)            );
        return $path;
    } */

    public function resizeImage(UploadedFile $file, int $surveyId): string
    {
        $image = $this->image->read($file);
        $image->scaleDown(width: 1600,height: 1600);
        $filename = uniqid().'.jpg';
        $path = "survey/{$surveyId}/".$filename;
        Storage::disk('public')->put($path,$image->toJpeg(75));
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

    public function storeImage(UploadedFile $file,string $folder='avatars',int $width=400,int $height=400,int $quality=80): string {
        $image = $this->image->read($file);
        $image->cover($width,$height);
        $filename = uniqid().'.jpg';
        $path = $folder.'/'.$filename;
        Storage::disk('public')->put($path,$image->toJpeg($quality));
        return $path;
    }
}