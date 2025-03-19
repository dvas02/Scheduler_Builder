<?php

namespace App\Services;

use App\Models\Location;

class ScheduleBuilder
{
    private $teams;
    private $weeks;
    private $gameLength;
    private $availableDays;
    private $gamesPerWeek;
    private $targetGamesPerTeam;
    private $totalGameSlots;
    private $locations;

    public function __construct(array $teams, int $weeks, int $gameLength, array $availableDays, array $locations)
    {
        $this->teams = $teams;
        $this->weeks = $weeks;
        $this->gameLength = $gameLength;
        $this->availableDays = $this->processDays($availableDays);
        $this->locations = $locations;
        
        // Calculate total games per week across all available days and locations
        $this->gamesPerWeek = $this->calculateGamesPerWeek();
        
        // Calculate total game slots
        $this->totalGameSlots = $this->weeks * $this->gamesPerWeek;
        
        $teamCount = count($teams);
        // Each game involves 2 teams, so multiply by 2
        $this->targetGamesPerTeam = floor(($this->totalGameSlots * 2) / $teamCount);
        // Ensure it's even since teams must play in pairs
        $this->targetGamesPerTeam = floor($this->targetGamesPerTeam / 2) * 2;
    }

    private function processDays(array $availableDays)
    {
        $processedDays = [];
        
        foreach ($availableDays as $day => $dayInfo) {
            if (isset($dayInfo['enabled']) && $dayInfo['enabled']) {
                // Process locations for this day
                $dayLocations = [];
                if (isset($dayInfo['locations']) && is_array($dayInfo['locations'])) {
                    foreach ($dayInfo['locations'] as $locationId => $locationInfo) {
                        if (isset($locationInfo['enabled']) && $locationInfo['enabled']) {
                            $dayLocations[$locationId] = [
                                'start' => $locationInfo['start'] ?? $dayInfo['start'],
                                'end' => $locationInfo['end'] ?? $dayInfo['end'],
                                'num_fields' => $locationInfo['num_fields'] ?? 1,
                                'division' => $locationInfo['division'] ?? 0,
                                'games_per_location' => $this->calculateGamesPerDay(
                                    $locationInfo['start'] ?? $dayInfo['start'],
                                    $locationInfo['end'] ?? $dayInfo['end'],
                                    $locationInfo['num_fields'] ?? 1
                                ),
                                'name' => $locationInfo['name'] ?? 'Unknown Location'
                            ];
                        }
                    }
                }
                
                $processedDays[$day] = [
                    'start' => $dayInfo['start'],
                    'end' => $dayInfo['end'],
                    'locations' => $dayLocations,
                    'games_per_day' => $this->calculateTotalGamesForDay($dayLocations)
                ];
            }
        }
        
        return $processedDays;
    }

    private function calculateTotalGamesForDay($locations)
    {
        $total = 0;
        foreach ($locations as $location) {
            $total += $location['games_per_location'];
        }
        return $total;
    }

    private function calculateGamesPerDay($startTime, $endTime, $numFields)
    {
        $startMinutes = $this->timeToMinutes($startTime);
        $endMinutes = $this->timeToMinutes($endTime);
        $availableMinutes = $endMinutes - $startMinutes;
        $gamesPerField = floor($availableMinutes / $this->gameLength);
        return $gamesPerField * $numFields;
    }

    private function calculateGamesPerWeek()
    {
        $total = 0;
        foreach ($this->availableDays as $day) {
            $total += $day['games_per_day'];
        }
        return $total;
    }

    public function generateSchedule()
    {
        $schedule = [];
        $teamCount = count($this->teams);
        $gamesScheduled = array_fill(0, $teamCount, 0);
        
        // Group teams by division
        $teamsByDivision = [];
        foreach ($this->teams as $index => $team) {
            $division = $team[2] ?? 0; // Default to division 0 if not specified
            if (!isset($teamsByDivision[$division])) {
                $teamsByDivision[$division] = [];
            }
            $teamsByDivision[$division][] = $index;
        }

        // If odd number of teams in any division, add a "BYE" team to that division
        foreach ($teamsByDivision as $division => $teamIndices) {
            if (count($teamIndices) % 2 !== 0) {
                $this->teams[] = [-1 * $division, "BYE (Div $division)", $division];
                $teamCount++;
                $gamesScheduled[] = 0;
                $teamsByDivision[$division][] = $teamCount - 1;
            }
        }

        // Create all possible matchups, prioritizing within-division games
        $matchups = $this->generateAllPossibleMatchups($teamsByDivision);
        shuffle($matchups);

        for ($week = 0; $week < $this->weeks; $week++) {
            $weekSchedule = [];
            $teamsPlayingThisWeek = [];
            $availableMatchups = $matchups; // Create a copy for this week
            
            // Schedule games for each available day
            foreach ($this->availableDays as $dayName => $dayInfo) {
                // Schedule games for each location on this day
                foreach ($dayInfo['locations'] as $locationId => $locationInfo) {
                    $gamesThisLocation = 0;
                    $locationDivision = $locationInfo['division'] ?? 0; // Get division for this location
                    
                    while ($gamesThisLocation < $locationInfo['games_per_location'] && !empty($availableMatchups)) {
                        $matchupFound = false;
                        
                        foreach ($availableMatchups as $matchupIndex => $matchup) {
                            $team1Index = $matchup[0];
                            $team2Index = $matchup[1];
                            $team1 = $this->teams[$team1Index];
                            $team2 = $this->teams[$team2Index];
                            $matchupDivision = $matchup[2]; // The division this matchup belongs to

                            // Skip if either team has reached target games
                            if ($gamesScheduled[$team1Index] >= $this->targetGamesPerTeam ||
                                $gamesScheduled[$team2Index] >= $this->targetGamesPerTeam) {
                                unset($availableMatchups[$matchupIndex]);
                                unset($matchups[$matchupIndex]);
                                continue;
                            }

                            // Skip if either team is already playing this week
                            if (in_array($team1Index, $teamsPlayingThisWeek) ||
                                in_array($team2Index, $teamsPlayingThisWeek)) {
                                continue;
                            }

                            // Skip games involving "BYE" team
                            if (strpos($team1[1], "BYE") !== false || strpos($team2[1], "BYE") !== false) {
                                unset($availableMatchups[$matchupIndex]);
                                unset($matchups[$matchupIndex]);
                                continue;
                            }
                            
                            // Skip if location has a specific division requirement and this matchup doesn't match
                            if ($locationDivision > 0 && $matchupDivision != $locationDivision) {
                                continue;
                            }

                            $gameTime = $this->getGameTime($locationInfo['start'], $gamesThisLocation, $locationInfo['num_fields']);
                            $fieldNumber = ($gamesThisLocation % $locationInfo['num_fields']) + 1;
                            
                            // Find location name
                            $locationName = "Unknown Location";
                            foreach ($this->locations as $loc) {
                                if ($loc[0] == $locationId) {
                                    $locationName = $loc[1];
                                    break;
                                }
                            }
                            
                            $weekSchedule[] = [
                                'team1_id' => $team1[0],
                                'team1_name' => $team1[1],
                                'team2_id' => $team2[0],
                                'team2_name' => $team2[1],
                                'day' => $dayName,
                                'time' => $gameTime,
                                'location_id' => $locationId,
                                'location_name' => $locationName,
                                'field' => "Field " . $fieldNumber,
                                'division' => $matchupDivision
                            ];

                            // Update tracking variables
                            $gamesScheduled[$team1Index]++;
                            $gamesScheduled[$team2Index]++;
                            $gamesThisLocation++;
                            $teamsPlayingThisWeek[] = $team1Index;
                            $teamsPlayingThisWeek[] = $team2Index;

                            // Remove used matchup
                            unset($availableMatchups[$matchupIndex]);
                            unset($matchups[$matchupIndex]);
                            $matchupFound = true;
                            break;
                        }

                        if (!$matchupFound) {
                            break;
                        }
                    }
                }
            }

            if (!empty($weekSchedule)) {
                // Sort games by day, location, and time before adding to schedule
                usort($weekSchedule, function($a, $b) {
                    $dayOrder = $this->getDayOrder($a['day']) - $this->getDayOrder($b['day']);
                    if ($dayOrder === 0) {
                        $locationOrder = $a['location_id'] - $b['location_id'];
                        if ($locationOrder === 0) {
                            return strcmp($a['time'], $b['time']);
                        }
                        return $locationOrder;
                    }
                    return $dayOrder;
                });
                
                $schedule[$week + 1] = $weekSchedule;
            }
        }

        // Prepare statistics for return
        $statistics = [];
        foreach ($this->teams as $index => $team) {
            if (strpos($team[1], "BYE") === false) {
                $statistics[] = [
                    'id' => $team[0],
                    'name' => $team[1],
                    'division' => $team[2] ?? 0,
                    'games' => $gamesScheduled[$index],
                    'target_games' => $this->targetGamesPerTeam
                ];
            }
        }

        return [
            'schedule' => $schedule,
            'statistics' => $statistics,
            'totalGameSlots' => $this->totalGameSlots,
        ];
    }

    private function getDayOrder($day)
    {
        $dayOrder = [
            'monday' => 1,
            'tuesday' => 2,
            'wednesday' => 3,
            'thursday' => 4,
            'friday' => 5,
            'saturday' => 6,
            'sunday' => 7
        ];
        return $dayOrder[$day] ?? 0;
    }

    private function generateAllPossibleMatchups($teamsByDivision)
    {
        $matchups = [];
        
        // First generate within-division matchups (higher priority)
        foreach ($teamsByDivision as $division => $teamIndices) {
            for ($i = 0; $i < count($teamIndices); $i++) {
                for ($j = $i + 1; $j < count($teamIndices); $j++) {
                    $matchups[] = [$teamIndices[$i], $teamIndices[$j], $division];
                }
            }
        }
        
        // Then generate cross-division matchups if needed
        $divisionKeys = array_keys($teamsByDivision);
        for ($d1 = 0; $d1 < count($divisionKeys); $d1++) {
            for ($d2 = $d1 + 1; $d2 < count($divisionKeys); $d2++) {
                $division1 = $divisionKeys[$d1];
                $division2 = $divisionKeys[$d2];
                
                foreach ($teamsByDivision[$division1] as $team1) {
                    foreach ($teamsByDivision[$division2] as $team2) {
                        // Use a non-existent division to mark cross-division games
                        $matchups[] = [$team1, $team2, 0]; // 0 means cross-division
                    }
                }
            }
        }
        
        return $matchups;
    }

    private function getGameTime($dayStart, $gameIndex, $numFields)
    {
        // Calculate actual game index for a single field by dividing by number of fields
        $gameIndexPerField = floor($gameIndex / $numFields);
        $startMinutes = $this->timeToMinutes($dayStart);
        return $this->minutesToTime($startMinutes + ($gameIndexPerField * $this->gameLength));
    }

    private function timeToMinutes($time)
    {
        list($hours, $minutes) = explode(':', $time);
        return ($hours * 60) + $minutes;
    }

    private function minutesToTime($minutes)
    {
        $hours = floor($minutes / 60);
        $mins = $minutes % 60;
        return sprintf('%02d:%02d', $hours, $mins);
    }
}