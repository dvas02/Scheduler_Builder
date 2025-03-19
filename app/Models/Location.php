<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Location
{
    private static $locations = [
        [1, 'Lachine'],
        [2, 'VSL'],
        [3, 'CSL'],
        [4, 'Brossard'],
        [5, 'ST-Leonard'],
    ];

    public static function all()
    {
        return self::$locations;
    }
}