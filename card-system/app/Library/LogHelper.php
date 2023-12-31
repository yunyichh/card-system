<?php

namespace App\Library;

use Illuminate\Support\Facades\Log;

class LogHelper
{
    public static function setLogFile($spbef6b3)
    {
        Log::getMonolog()->setHandlers([]);
        Log::useDailyFiles(storage_path(DIRECTORY_SEPARATOR.'logs'.DIRECTORY_SEPARATOR.$spbef6b3.'_'.php_sapi_name()
                                        .'.log'), 0, config('app.log_level'));
    }
}