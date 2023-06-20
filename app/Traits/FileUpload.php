<?php
namespace App\Traits;

use Illuminate\Support\Facades\Storage;

trait FileUpload
{
    public function uploadFile($file, $folder, $filename = null)
    {
        // if (empty($filename)) {
        //     $ext = strtolower($file->getClientOriginalExtension());
        //     $filename = time();
        //     $filename = "{$filename}.{$ext}";
        // }
        // $path = Storage::disk('public')->put("{$folder}/{$filename}", $file);
        $path = Storage::disk('public')->put($folder, $file);

        // $path = Storage::putFile('public'.$folder, $file);
        // $path = str_replace('public/', '/storage/', $path);
        

        return $path;
    }

    public function getImageUrl($url)
    {
        return Storage::disk('public')->url($url);
    }
}