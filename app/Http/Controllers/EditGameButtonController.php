<?php

namespace App\Http\Controllers;

use App\Models\Team;
use App\Models\Location;
use Illuminate\Http\Request;

class EditGameButtonController extends Controller
{
    public function editGameHandler(Request $request)
    {
        // Validate the request
        $request->validate([
            // New values
            'team1_id' => 'required|integer',
            'team2_id' => 'required|integer|different:team1_id',
            'time' => 'required|date_format:H:i',
            'day' => 'required',
            'location_id' => 'required|integer',
            'field' => 'required',

            // Original values
            'original_team1_id' => 'required|integer',
            'original_team2_id' => 'required|integer',
            'original_day' => 'required',
            'original_location_id' => 'required|integer',
            'original_field' => 'required',
        ]);

        try {
            // Get the current schedule from session
            $schedule = session('schedule');

            if (!$schedule) {
                return response()->json([
                    'success' => false,
                    'message' => 'No active schedule found'
                ], 404);
            }

            // Get team names from the teams array
            $teams = collect(Team::all());
            $team1 = $teams->first(function($team) use ($request) {
                return $team[0] == $request->team1_id;
            });
            $team2 = $teams->first(function($team) use ($request) {
                return $team[0] == $request->team2_id;
            });

            if (!$team1 || !$team2) {
                return response()->json([
                    'success' => false,
                    'message' => 'One or both teams not found'
                ], 404);
            }

            // Get location name from the locations array
            $locations = collect(Location::all());
            $location = $locations->first(function($loc) use ($request) {
                return $loc[0] == $request->location_id;
            });

            if (!$location) {
                return response()->json([
                    'success' => false,
                    'message' => 'Location not found'
                ], 404);
            }

            // Find and update the game in the schedule
            $updated = false;
            foreach ($schedule as $weekNumber => $games) {
                foreach ($games as $index => $game) {
                    // Check for match with original teams and location
                    if (($game['team1_id'] == $request->original_team1_id && 
                         $game['team2_id'] == $request->original_team2_id ||
                         $game['team1_id'] == $request->original_team2_id && 
                         $game['team2_id'] == $request->original_team1_id) && 
                        $game['day'] == $request->original_day &&
                        $game['location_id'] == $request->original_location_id &&
                        $game['field'] == $request->original_field) {
                        
                        // Determine division based on teams
                        // Use team1's division if they're in the same division, otherwise use 0 (cross-division)
                        $division = ($team1[2] == $team2[2]) ? $team1[2] : 0;
                        
                        // Update game details
                        $schedule[$weekNumber][$index] = [
                            'team1_id' => $request->team1_id,
                            'team2_id' => $request->team2_id,
                            'team1_name' => $team1[1],
                            'team2_name' => $team2[1],
                            'time' => $request->time,
                            'day' => $request->day,
                            'location_id' => $request->location_id,
                            'location_name' => $location[1],
                            'field' => $request->field,
                            'division' => $division
                        ];
                        
                        $updated = true;
                        break 2;
                    }
                }
            }

            if (!$updated) {
                return response()->json([
                    'success' => false,
                    'message' => 'Game not found in schedule'
                ], 404);
            }

            // Store updated schedule back in session
            session(['schedule' => $schedule]);

            // Return JSON response for AJAX
            return response()->json([
                'success' => true,
                'message' => 'Game updated successfully',
                'data' => [
                    'team1_name' => $team1[1],
                    'team2_name' => $team2[1],
                    'time' => $request->time,
                    'location_name' => $location[1],
                    'field' => $request->field,
                    'division' => $division
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update game',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}