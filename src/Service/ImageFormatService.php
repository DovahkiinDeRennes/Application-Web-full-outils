<?php

namespace App\Service;

use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;
use Symfony\Component\HttpFoundation\File\UploadedFile;

use Intervention\Image\Encoders\JpegEncoder;
use Intervention\Image\Encoders\PngEncoder;
use Intervention\Image\Encoders\WebpEncoder;
use Intervention\Image\Encoders\PdfEncoder;

class ImageFormatService
{
    private ImageManager $imageManager;

    public function __construct()
    {
        $this->imageManager = new ImageManager(new Driver());
    }

    public function convert(
        string $name,
        UploadedFile $file,
        string $format,
        string $targetDir
    ): string {
        $format = strtolower($format);

        $image = $this->imageManager->read($file->getPathname());

        $cleanName = preg_replace('/[^a-zA-Z0-9_-]/', '_', $name);
        $filename = uniqid('img_') . '_' . $cleanName . '.' . $format;

        $path = $targetDir . '/' . $filename;

        match ($format) {
            'jpg', 'jpeg' => $image->encode(new JpegEncoder(quality: 90))->save($path),
            'png'         => $image->encode(new PngEncoder())->save($path),
            'webp'        => $image->encode(new WebpEncoder(quality: 80))->save($path),
            default       => throw new \InvalidArgumentException('Format non supporté'),
        };

        return $filename;
    }
}
