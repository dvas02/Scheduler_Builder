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
      $request->validate([
          'weeks' => 'required|integer|min:1',
          'gameLength' => 'required|integer|min:15',
          'dayStart' => 'required|date_format:H:i',
          'dayEnd' => 'required|date_format:H:i|after:dayStart',
      ]);

      $generator = new ScheduleBuilder(
          Team::all(),
          $request->weeks,
          $request->gameLength,
          $request->dayStart,
          $request->dayEnd
      );

      $result = $generator->generateSchedule();

      // Store schedule data in session
      session([
        'schedule' => $result['schedule'],
        'statistics' => $result['statistics'],
        'params' => $request->all()
      ]);

      return view('scheduler-page', [
          'schedule' => $result['schedule'],
          'statistics' => $result['statistics'],
          'params' => $request->all()
      ]);
  }

  
}

