<?php

namespace App\Http\Controllers;

use App\Models\Team;
use App\Models\Location;
use App\Services\ScheduleBuilder;
use Illuminate\Http\Request;

class ScheduleController extends Controller
{
    public function index()
    {
        return view('scheduler-page', [
            'locations' => Location::all(),
            'divisions' => Location::divisions()
        ]);
    }

    public function generateSchedule(Request $request)
    {
        $days = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'];
        $locations = Location::all();
        $locationIds = array_column($locations, 0);

        $validationRules = [
            'weeks' => 'required|integer|min:1',
            'gameLength' => 'required|integer|min:15',
            'targetGames' => 'required|integer|min:1',
        ];

        // Validation rules for each day
        foreach ($days as $day) {
            $validationRules[$day.'_enabled'] = 'sometimes|boolean';
            $validationRules[$day.'_start'] = 'required_if:'.$day.'_enabled,1|date_format:H:i';
            $validationRules[$day.'_end'] = 'required_if:'.$day.'_enabled,1|date_format:H:i|after:'.$day.'_start';
            
            // For each location on each day
            foreach ($locationIds as $locationId) {
                $validationRules[$day.'_loc_'.$locationId.'_enabled'] = 'sometimes|boolean';
                $validationRules[$day.'_loc_'.$locationId.'_start'] = 'nullable|date_format:H:i';
                $validationRules[$day.'_loc_'.$locationId.'_end'] = 'nullable|date_format:H:i';
                $validationRules[$day.'_loc_'.$locationId.'_fields'] = 'nullable|integer|min:1';
                $validationRules[$day.'_loc_'.$locationId.'_division'] = 'nullable|integer|min:0|max:2';
            }
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
                
                // Handle locations for this day
                $dayLocations = [];
                foreach ($locationIds as $locationId) {
                    $locationEnabled = $request->input($day.'_loc_'.$locationId.'_enabled', false);
                    
                    if ($locationEnabled) {
                        $locationName = '';
                        foreach ($locations as $loc) {
                            if ($loc[0] == $locationId) {
                                $locationName = $loc[1];
                                break;
                            }
                        }
                        
                        // Get location specific data or use day defaults
                        $locationStart = $request->input($day.'_loc_'.$locationId.'_start') ?? $validated[$day.'_start'];
                        $locationEnd = $request->input($day.'_loc_'.$locationId.'_end') ?? $validated[$day.'_end'];
                        $locationFields = $request->input($day.'_loc_'.$locationId.'_fields') ?? 1;
                        $locationDivision = $request->input($day.'_loc_'.$locationId.'_division', 0);
                        
                        $dayLocations[$locationId] = [
                            'enabled' => true,
                            'name' => $locationName,
                            'start' => $locationStart,
                            'end' => $locationEnd,
                            'num_fields' => $locationFields,
                            'division' => $locationDivision,
                        ];
                        
                        // Store in params for view
                        $params[$day.'_loc_'.$locationId.'_enabled'] = true;
                        $params[$day.'_loc_'.$locationId.'_start'] = $locationStart;
                        $params[$day.'_loc_'.$locationId.'_end'] = $locationEnd;
                        $params[$day.'_loc_'.$locationId.'_fields'] = $locationFields;
                        $params[$day.'_loc_'.$locationId.'_division'] = $locationDivision;
                    }
                }
                
                // Add to availableDays array with locations
                $availableDays[$day] = [
                    'enabled' => true,
                    'start' => $validated[$day.'_start'],
                    'end' => $validated[$day.'_end'],
                    'locations' => $dayLocations
                ];
            }
        }

        // Create schedule builder with new parameter structure
        $generator = new ScheduleBuilder(
            $teams,
            $validated['weeks'],
            $validated['gameLength'],
            $availableDays,
            $locations
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
            'params' => $params,
            'locations' => $locations,
            'divisions' => Location::divisions()
        ]);
    }
}