<?php

namespace App\Http\Controllers;

use App\Models\Team;
use Illuminate\Http\Request;

class EditGameButtonController extends Controller
{
    public function editGameHandler(Request $request)
    {
        // Validate the request
        $request->validate([
            'team1_id' => 'required|integer',
            'team2_id' => 'required|integer|different:team1_id',
            'time' => 'required|date_format:H:i',
            'original_team1_id' => 'required|integer',
            'original_team2_id' => 'required|integer'
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

            // Get team names from the static array
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

            // Find and update the game in the schedule
            $updated = false;
            foreach ($schedule as $weekNumber => $games) {
                foreach ($games as $index => $game) {
                    // Check for match with original teams (in either order)
                    if (($game['team1_id'] == $request->original_team1_id && 
                         $game['team2_id'] == $request->original_team2_id) ||
                        ($game['team1_id'] == $request->original_team2_id && 
                         $game['team2_id'] == $request->original_team1_id)) {
                        
                        // Update game details
                        $schedule[$weekNumber][$index] = [
                            'team1_id' => $request->team1_id,
                            'team2_id' => $request->team2_id,
                            'team1_name' => $team1[1],  // Index 1 contains the team name
                            'team2_name' => $team2[1],  // Index 1 contains the team name
                            'time' => $request->time
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
                    'time' => $request->time
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