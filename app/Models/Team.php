<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Team
{
    private static $teams = [
        [1, 'Lions'],
        [2, 'Tigers'],
        [3, 'Bears'],
        [4, 'Hawks'],
        [5, 'Eagles'],
        [6, 'Wolves'],
        [7, 'Alouettes'],
        [8, 'Patriots'],
        [9, 'Chiefs'],
        [10, 'Titans'],
        [11, 'Heat'],
        [12, 'Twelve'],
        [13, 'Thirteen'],
        [14, 'Fourteen'],
        [15, 'Fifteen'],
        [16, 'Sixteen'],
        [17, 'Seventeen'],
        [18, 'Eighteen'],
        [19, 'Nineteen'],
        [20, 'Twenty'],
        [21, 'Twenty One'],
        [22, 'Twenty Two']
    ];

    public static function all()
    {
        return self::$teams;
    }
}