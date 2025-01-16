<?php

namespace App\Services;

class ScheduleBuilder
{
    private $teams;
    private $weeks;
    private $gameLength;
    private $dayStart;
    private $dayEnd;
    private $gamesPerDay;
    private $targetGamesPerTeam;

    public function __construct(array $teams, int $weeks, int $gameLength, string $dayStart, string $dayEnd)
    {
        $this->teams = $teams;
        $this->weeks = $weeks;
        $this->gameLength = $gameLength;
        $this->dayStart = $dayStart;
        $this->dayEnd = $dayEnd;
        
        // Calculate how many games can fit in a day
        $dayStartMinutes = $this->timeToMinutes($dayStart);
        $dayEndMinutes = $this->timeToMinutes($dayEnd);
        $availableMinutes = $dayEndMinutes - $dayStartMinutes;
        $this->gamesPerDay = floor($availableMinutes / $gameLength);
        
        // Calculate target games per team based on available slots
        $totalGameSlots = $this->weeks * $this->gamesPerDay;
        $teamCount = count($teams);
        // Each game involves 2 teams, so multiply by 2
        $this->targetGamesPerTeam = floor(($totalGameSlots * 2) / $teamCount);
        // Ensure it's even since teams must play in pairs
        $this->targetGamesPerTeam = floor($this->targetGamesPerTeam / 2) * 2;
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
        
        // Shuffle matchups to ensure randomness while maintaining balance
        shuffle($matchups);

        for ($week = 0; $week < $this->weeks; $week++) {
            $weekSchedule = [];
            $gamesThisWeek = 0;
            $teamsPlayingThisWeek = [];
            $availableMatchups = $matchups; // Create a copy for this week
            
            // Try to fill all available time slots in this week
            while ($gamesThisWeek < $this->gamesPerDay && !empty($availableMatchups)) {
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
                        unset($matchups[$matchupIndex]); // Remove from overall matchups too
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

                    $gameTime = $this->getGameTime($gamesThisWeek);
                    if ($gameTime === false) {
                        break 2; // Break out of both loops if we can't schedule any more games today
                    }

                    $weekSchedule[] = [
                        'team1_id' => $team1[0],
                        'team1_name' => $team1[1],
                        'team2_id' => $team2[0],
                        'team2_name' => $team2[1],
                        'time' => $gameTime
                    ];

                    // Update tracking variables
                    $gamesScheduled[$team1Index]++;
                    $gamesScheduled[$team2Index]++;
                    $gamesThisWeek++;
                    $teamsPlayingThisWeek[] = $team1Index;
                    $teamsPlayingThisWeek[] = $team2Index;

                    // Remove used matchup
                    unset($availableMatchups[$matchupIndex]);
                    unset($matchups[$matchupIndex]);
                    $matchupFound = true;
                    break;
                }

                // If no valid matchup was found, break the loop
                if (!$matchupFound) {
                    break;
                }
            }

            if (!empty($weekSchedule)) {
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
            'statistics' => $statistics
        ];
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

    private function getGameTime($gameIndex)
    {
        $startTime = $this->timeToMinutes($this->dayStart);
        $endTime = $this->timeToMinutes($this->dayEnd);
        
        $gameStartMinutes = $startTime + ($gameIndex * $this->gameLength);
        
        if ($gameStartMinutes + $this->gameLength > $endTime) {
            return false;
        }
        
        return $this->minutesToTime($gameStartMinutes);
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