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
            'targetGames' => 'required|integer|min:1',
        ];

        // Validation rules for each day
            // Not validating the number of fields or field name since the default is one without any name
        foreach ($days as $day) {
            $validationRules[$day.'_enabled'] = 'sometimes|boolean';
            $validationRules[$day.'_start'] = 'required_if:'.$day.'_enabled,1|date_format:H:i';
            $validationRules[$day.'_end'] = 'required_if:'.$day.'_enabled,1|date_format:H:i|after:'.$day.'_start';
            
            // Added field num and name
            // Add nullable to allow empty values which we'll handle in validation
            $validationRules[$day.'_fields'] = 'nullable|integer|min:1';
            $validationRules[$day.'_field_name'] = 'nullable|string';
        }

        $validated = $request->validate($validationRules);

        // Get teams directly - they're already in the correct format
        $teams = Team::all();

        // Build params array with all form data
        $params = [
            'weeks' => $validated['weeks'],
            'gameLength' => $validated['gameLength'],
            'targetGames' => $validated['targetGames'],
            'numTeams' => count($teams),
        ];

        // Build available days array for ScheduleBuilder
        $availableDays = [];
        foreach ($days as $day) {
            if (isset($validated[$day.'_enabled']) && $validated[$day.'_enabled']) {
                $params[$day.'_enabled'] = true;
                $params[$day.'_start'] = $validated[$day.'_start'];
                $params[$day.'_end'] = $validated[$day.'_end'];
                $params[$day.'_fields'] = $validated[$day.'_fields'];
                $params[$day.'_field_name'] = $validated[$day.'_field_name'];
        
                
                // Added for num fields and name
                // Set default value of 1 for fields if not provided or empty
                if (empty($params[$day.'_fields'])) {
                    $params[$day.'_fields'] = 1;
                }

                // Set default value of 'NONE' for field_name if not provided or empty
                if (empty($params[$day.'_field_name'])) {
                    $params[$day.'_field_name'] = 'Field';
                }
                
                // Add to availableDays array in the format expected by ScheduleBuilder
                $availableDays[$day] = [
                    'enabled' => true,
                    'start' => $validated[$day.'_start'],
                    'end' => $validated[$day.'_end'],
                    '_fields' => $params[$day.'_fields'],
                    '_field_name' => $params[$day.'_field_name'],
                    
                ];
            }
        }

        // Create schedule builder with new parameter structure
        $generator = new ScheduleBuilder(
            $teams,
            $validated['weeks'],
            $validated['gameLength'],
            $availableDays,
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
            'totalGameSlots' => $result['totalGameSlots'],
            'params' => $params
        ]);
    }
}