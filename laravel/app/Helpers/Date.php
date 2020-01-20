<?php
namespace App\Helpers;
use Carbon\Carbon;

class Date
{
    static function W3c($value) {
        if(is_null($value))
            return $value;
        return Carbon::parse($value)->toW3cString();
    }
}