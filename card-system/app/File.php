<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class File extends Model
{
    protected $guarded = [];
    public $timestamps = false;

    function deleteFile()
    {
        try {
            Storage::disk($this->driver)->delete($this->path);
        } catch (\Exception $spc22b6c) {
            \Log::error('File.deleteFile Error: '.$spc22b6c->getMessage(), ['exception' => $spc22b6c]);
        }
    }

    public static function getProductFolder()
    {
        return 'images/product';
    }
}