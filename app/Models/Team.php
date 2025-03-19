<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Team
{
    private static $teams = [
        // team = [team_id, team_name, team_div]
        [1, 'Lions', 1],
        [2, 'Tigers', 2],
        [3, 'Bears', 1],
        [4, 'Hawks', 2],
        [5, 'Eagles', 1],
        [6, 'Wolves', 2],
        [7, 'Alouettes', 1],
        [8, 'Patriots', 2],
        [9, 'Chiefs', 1],
        [10, 'Titans', 2],
        [11, 'Heat', 1],
        [12, 'Twelve', 2],
        [13, 'Thirteen', 1],
        [14, 'Fourteen', 2],
        [15, 'Fifteen', 1],
        [16, 'Sixteen', 2],
        [17, 'Seventeen', 1],
        [18, 'Eighteen', 2],
        [19, 'Nineteen', 1],
        [20, 'Twenty', 2],
        [21, 'Twenty One', 1],
        [22, 'Twenty Two', 2]
    ];

    public static function all()
    {
        return self::$teams;
    }
}