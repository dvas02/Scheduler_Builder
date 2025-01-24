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
        ];

        // Validation rules for each day
        foreach ($days as $day) {
            $validationRules[$day.'_enabled'] = 'sometimes|boolean';
            $validationRules[$day.'_start'] = 'required_if:'.$day.'_enabled,1|date_format:H:i';
            $validationRules[$day.'_end'] = 'required_if:'.$day.'_enabled,1|date_format:H:i|after:'.$day.'_start';
        }

        $validated = $request->validate($validationRules);

        // Build params array with all form data
        $params = [
            'weeks' => $validated['weeks'],
            'gameLength' => $validated['gameLength'],
        ];

        // Build available days array for ScheduleBuilder
        $availableDays = [];
        foreach ($days as $day) {
            if (isset($validated[$day.'_enabled']) && $validated[$day.'_enabled']) {
                $params[$day.'_enabled'] = true;
                $params[$day.'_start'] = $validated[$day.'_start'];
                $params[$day.'_end'] = $validated[$day.'_end'];
                
                // Add to availableDays array in the format expected by ScheduleBuilder
                $availableDays[$day] = [
                    'enabled' => true,
                    'start' => $validated[$day.'_start'],
                    'end' => $validated[$day.'_end']
                ];
            }
        }
        // Get teams directly - they're already in the correct format
        $teams = Team::all();

        // Create schedule builder with new parameter structure
        $generator = new ScheduleBuilder(
            $teams,
            $validated['weeks'],
            $validated['gameLength'],
            $availableDays
        );

        $result = $generator->generateSchedule();

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