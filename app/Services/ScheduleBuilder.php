<?php

namespace App\Services;

class ScheduleBuilder
{
    private $teams;
    private $weeks;
    private $gameLength;
    private $availableDays;
    private $gamesPerWeek;
    private $targetGamesPerTeam;
    private $totalGameSlots;

    public function __construct(array $teams, int $weeks, int $gameLength, array $availableDays)
    {
        $this->teams = $teams;
        $this->weeks = $weeks;
        $this->gameLength = $gameLength;
        $this->availableDays = $this->processDays($availableDays);
        
        
        // Calculate total games per week across all available days
        $this->gamesPerWeek = $this->calculateGamesPerWeek();
        
        // Calculate target games per team based on available slots
        $this->totalGameSlots = $this->weeks * $this->gamesPerWeek;
        
        // TESTING TO SEE IF AVAIL SLOTS WORK
        //$this->availSlots = $totalGameSlots;
        // Ends here

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
                $processedDays[$day] = [
                    'start' => $dayInfo['start'],
                    'end' => $dayInfo['end'],
                    'games_per_day' => $this->calculateGamesPerDay(
                        $dayInfo['start'],
                        $dayInfo['end']
                    )
                ];
            }
        }
        
        return $processedDays;
    }


    private function calculateGamesPerDay($startTime, $endTime)
    {
        $startMinutes = $this->timeToMinutes($startTime);
        $endMinutes = $this->timeToMinutes($endTime);
        $availableMinutes = $endMinutes - $startMinutes;
        return floor($availableMinutes / $this->gameLength);
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

        // If odd number of teams, add a "BYE" team
        if ($teamCount % 2 !== 0) {
            $this->teams[] = [-1, "BYE"];
            $teamCount++;
            $gamesScheduled[] = 0;
        }

        // Create all possible matchups
        $matchups = $this->generateAllPossibleMatchups($teamCount);
        shuffle($matchups);

        for ($week = 0; $week < $this->weeks; $week++) {
            $weekSchedule = [];
            $teamsPlayingThisWeek = [];
            $gamesScheduledThisWeek = 0;
            $availableMatchups = $matchups; // Create a copy for this week
            
            // Schedule games for each available day
            foreach ($this->availableDays as $dayName => $dayInfo) {
                $gamesThisDay = 0;
                
                while ($gamesThisDay < $dayInfo['games_per_day'] && !empty($availableMatchups)) {
                    $matchupFound = false;
                    
                    foreach ($availableMatchups as $matchupIndex => $matchup) {
                        $team1Index = $matchup[0];
                        $team2Index = $matchup[1];
                        $team1 = $this->teams[$team1Index];
                        $team2 = $this->teams[$team2Index];

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
                        if ($team1[1] === "BYE" || $team2[1] === "BYE") {
                            unset($availableMatchups[$matchupIndex]);
                            unset($matchups[$matchupIndex]);
                            continue;
                        }

                        $gameTime = $this->getGameTime($dayInfo['start'], $gamesThisDay);
                        
                        $weekSchedule[] = [
                            'team1_id' => $team1[0],
                            'team1_name' => $team1[1],
                            'team2_id' => $team2[0],
                            'team2_name' => $team2[1],
                            'day' => $dayName,
                            'time' => $gameTime
                        ];

                        // Update tracking variables
                        $gamesScheduled[$team1Index]++;
                        $gamesScheduled[$team2Index]++;
                        $gamesThisDay++;
                        $gamesScheduledThisWeek++;
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

            if (!empty($weekSchedule)) {
                // Sort games by day and time before adding to schedule
                usort($weekSchedule, function($a, $b) {
                    $dayOrder = $this->getDayOrder($a['day']) - $this->getDayOrder($b['day']);
                    if ($dayOrder === 0) {
                        return strcmp($a['time'], $b['time']);
                    }
                    return $dayOrder;
                });
                
                $schedule[$week + 1] = $weekSchedule;
            }
        }

        // Prepare statistics for return
        $statistics = [];
        foreach ($this->teams as $index => $team) {
            if ($team[1] !== "BYE") {
                $statistics[] = [
                    'id' => $team[0],
                    'name' => $team[1],
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

    private function generateAllPossibleMatchups($teamCount)
    {
        $matchups = [];
        for ($i = 0; $i < $teamCount; $i++) {
            for ($j = $i + 1; $j < $teamCount; $j++) {
                $matchups[] = [$i, $j];
            }
        }
        return $matchups;
    }

    private function getGameTime($dayStart, $gameIndex)
    {
        $startMinutes = $this->timeToMinutes($dayStart);
        return $this->minutesToTime($startMinutes + ($gameIndex * $this->gameLength));
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