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
    
    // Add a method to get divisions
    public static function divisions()
    {
        return [
            0 => 'None',
            1 => 'Division 1',
            2 => 'Division 2'
        ];
    }
}