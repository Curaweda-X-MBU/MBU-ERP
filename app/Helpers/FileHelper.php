<?php

namespace App\Helpers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class FileHelper
{
    protected static $disk = 's3';

    /**
     * Upload an image to a specified directory on S3
     *
     * @param  \Illuminate\Http\UploadedFile  $image  The image file from the request
     * @param  string  $directory  The directory where the image will be stored (optional)
     * @param  string  $disk  The storage disk ('public', 's3', etc.) (default: 's3')
     * @return string|false The URL of the uploaded image or false on failure
     */
    public static function upload($image, $directory = 'user-profile')
    {
        try {
            $filename = Str::random(15).'.'.$image->getClientOriginalExtension();
            $path     = $image->storeAs($directory, $filename, self::$disk);
            $url      = Storage::disk(self::$disk)->url($path);

            return [
                'status' => true,
                'url'    => $directory.'/'.$filename,
            ];
        } catch (\Exception $e) {
            return [
                'status'  => false,
                'message' => $e->getMessage(),
            ];
        }
    }

    public static function show(Request $req)
    {
        $filename = $req->filename;
        $cacheKey = 's3_cache_'.$filename;
        $file     = Cache::remember($cacheKey, 60, function() use ($filename) {
            return Storage::disk(self::$disk)->get($filename);
        });

        $extension   = pathinfo($filename, PATHINFO_EXTENSION);
        $contentType = self::getContentType($extension);

        return Response::make($file, 200, [
            'Content-Type'  => $contentType,
            'Cache-Control' => 'public, max-age=3600',
        ]);
    }

    public static function delete($filename)
    {
        if (Storage::disk(self::$disk)->exists($filename)) {
            Storage::disk(self::$disk)->delete($filename);
            Cache::forget('s3_cache_'.$filename);
        }
    }

    private static function getContentType($extension)
    {
        switch (strtolower($extension)) {
            case 'jpg':
            case 'jpeg':
                return 'image/jpeg';
            case 'png':
                return 'image/png';
            case 'pdf':
                return 'application/pdf';
            default:
                return 'application/octet-stream';
        }
    }
}
