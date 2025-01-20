<?php

namespace App\Http\Controllers;

use App\Models\Team;
use App\Services\ScheduleBuilder;
use Illuminate\Http\Request;

class ScheduleController extends Controller
{
    public function index()
    {
        return view('scheduler-page');
    }

    public function generateSchedule(Request $request)
  {

    $days = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'];

    $validationRules = [
        'weeks' => 'required|integer|min:1',
        'gameLength' => 'required|integer|min:15',
        'dayStart' => 'required|date_format:H:i',
        'dayEnd' => 'required|date_format:H:i|after:dayStart',

    ];

    // Validation rules for each day
    foreach ($days as $day) {
        $validationRules[$day.'_enabled'] = 'sometimes|boolean';
        $validationRules[$day.'_start'] = 'required_if:'.$day.'_enabled,1|date_format:H:i';
        $validationRules[$day.'_end'] = 'required_if:'.$day.'_enabled,1|date_format:H:i|after:'.$day.'_start';
    }

    $validated = $request->validate($validationRules);

      $params = [
        'weeks' => $validated['weeks'],
        'gameLength' => $validated['gameLength'],
        'dayStart' => $validated['dayStart'],
        'dayEnd' => $validated['dayEnd'],
    ];

    // Add each day's data to params
    foreach ($days as $day) {
        $params[$day.'_enabled'] = $validated[$day.'_enabled'] ?? false;
        if ($params[$day.'_enabled']) {
            $params[$day.'_start'] = $validated[$day.'_start'];
            $params[$day.'_end'] = $validated[$day.'_end'];
        }
    }

    // Leave this the same for now since we aren't handling the new days/times yet
      $generator = new ScheduleBuilder(
          Team::all(),
          $request->weeks,
          $request->gameLength,
          $request->dayStart,
          $request->dayEnd
      );

      $result = $generator->generateSchedule();

      // Store schedule data in session
      /*session([
        'schedule' => $result['schedule'],
        'statistics' => $result['statistics'],
        'params' => $request->all()
      ]);

      return view('scheduler-page', [
          'schedule' => $result['schedule'],
          'statistics' => $result['statistics'],
          'params' => $request->all()
      ]);*/

      session([
        'schedule' => $result['schedule'],
        'statistics' => $result['statistics'],
        'params' => $params
      ]);

      return view('scheduler-page', [
          'schedule' => $result['schedule'],
          'statistics' => $result['statistics'],
          'params' => $params
      ]);
  }

  
}

