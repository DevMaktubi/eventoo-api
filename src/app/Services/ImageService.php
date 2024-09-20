<?php

namespace App\Services;

use Illuminate\Support\Facades\Storage;

class ImageService
{
    public function upload($image, $folder)
    {
        if ($image) {
            // Armazena a imagem no S3
            $path = $image->store($folder, 's3');
            // Retorna a URL pública da imagem
            return Storage::disk('s3')->url($path);
        }

        return null;
    }
}
