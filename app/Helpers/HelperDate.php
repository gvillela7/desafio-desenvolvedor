<?php

namespace App\Helpers;

use Carbon\Carbon;

class HelperDate
{
    public function __construct()
    {
        //
    }

    public static function convertDate($date): ?Carbon
    {
        return Carbon::createFromFormat('Y-m-d', $date);
    }
}
