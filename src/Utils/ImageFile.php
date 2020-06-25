<?php

namespace App\Utils;

use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class ImageFile
{
    /**
     * @param UploadedFile $file
     * @param string $path
     *
     * @return string
     */
    public static function upload(UploadedFile $file, string $path): string
    {
        $fileName = md5(uniqid()) . '.' . $file->guessExtension();
        try {
            $file->move($path, $fileName);
        } catch (FileException $e) {
            var_dump($e->getMessage()); die;
        }
        return $fileName;
    }

    /**
     * @param string $filename
     *
     */
    public static function deleteFile(string $filename)
    {
        if (file_exists($filename)) {
            $fileInfo = pathinfo($filename);
            unlink($fileInfo['dirname'] . '/thumbs/' . $fileInfo['basename']);
            unlink($filename);
        }
    }

    /**
     * @param string $imageName
     * @param int $width
     * @param int $height
     *
     * @return bool
     */
    public static function createThumb(string $imageName, int $width, int $height): bool
    {
        $result = false;
        list($srcWidth, $srcHeight, $srcType) = getimagesize($imageName);
        $ratio = $srcWidth / $srcHeight;
        if( $ratio > 1) {
            $height = $height / $ratio;
        }
        else {
            $width = $width * $ratio;
        }
        $thumb = imagecreatetruecolor($width, $height);
        if ($srcType == IMAGETYPE_JPEG) {
            $source = imagecreatefromjpeg($imageName);
        } elseif($srcType == IMAGETYPE_PNG) {
            $source = imagecreatefrompng($imageName);
        } elseif($srcType == IMAGETYPE_GIF) {
            $source = imagecreatefromgif($imageName);
        }

        imagecopyresized($thumb, $source, 0, 0, 0, 0, $width, $height, $srcWidth, $srcHeight);
        $pathInfo = pathinfo($imageName);
        $thumbsDir = $pathInfo['dirname'] . '/thumbs';
        if (!is_dir($thumbsDir)) {
            mkdir($thumbsDir);
        }

        if ($srcType == IMAGETYPE_JPEG) {
            $result = imagejpeg($thumb, $thumbsDir . '/' . $pathInfo['basename']);
        } elseif($srcType == IMAGETYPE_PNG) {
            $result = imagepng($thumb, $thumbsDir . '/' . $pathInfo['basename']);
        } elseif($srcType == IMAGETYPE_GIF) {
            $result = imagegif($thumb, $thumbsDir . '/' . $pathInfo['basename']);
        }
        return $result;
    }
}