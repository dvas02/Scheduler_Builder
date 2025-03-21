<!DOCTYPE html>
<html lang="en">
<head>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LeagueSuite Scheduler</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 40px;
            line-height: 1.6;
            background-color: #f5f5f5;
        }
        .container {
            max-width: 1000px;
            margin: 0 auto;
        }
        h1 {
            color: #333;
            text-align: center;
            margin-bottom: 30px;
        }
        .form-container {
            background: white;
            padding: 20px;
            margin-bottom: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .form-group {
            margin-bottom: 15px;
        }
        .form-group label {
            display: block;
            margin-bottom: 5px;
            color: #2c3e50;
            font-weight: 500;
        }
        .form-group input {
            width: 100%;
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 16px;
        }
        .submit-btn {
            background-color: #3498db;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
            width: 100%;
        }
        .submit-btn:hover {
            background-color: #2980b9;
        }
        .error {
            color: #e74c3c;
            font-size: 0.9em;
            margin-top: 5px;
        }
        .week-container {
            background: white;
            padding: 20px;
            margin-bottom: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .week-title {
            color: #2c3e50;
            margin-bottom: 15px;
            border-bottom: 2px solid #eee;
            padding-bottom: 10px;
        }
        .game {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 12px;
            border-bottom: 1px solid #eee;
            gap: 15px;
        }
        .game:last-child {
            border-bottom: none;
        }
        .teams {
            flex: 1;
            display: flex;
            justify-content: flex-start;
            align-items: center;
            gap: 20px;
        }
        .team {
            display: flex;
            align-items: center;
            gap: 5px;
        }
        .team-id {
            font-size: 0.8em;
            color: #666;
            background: #eee;
            padding: 2px 6px;
            border-radius: 3px;
        }
        .team-name {
            font-weight: 500;
        }
        .time {
            width: 100px;
            text-align: right;
            color: #666;
        }
        .vs {
            color: #999;
            font-weight: 300;
        }
        .days-times-section {
            margin-top: 30px;
            border-top: 1px solid #eee;
            padding-top: 20px;
        }

        .params-subheader {
            color: #2c3e50;
            font-size: 1.1rem;
            font-weight: 600;
            margin-bottom: 20px;
        }

        .days-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            gap: 20px;
        }

        .day-param-group {
            background: #f8f9fa;
            border-radius: 8px;
            padding: 15px;
            transition: all 0.2s ease;
        }

        .day-param-group:hover {
            background: #f1f3f5;
        }

        .day-header {
            margin-bottom: 10px;
        }

        .day-name {
            font-weight: 600;
            color: #2c3e50;
            text-transform: capitalize;
        }

        .day-details {
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

        .time-slot {
            color: #666;
            font-size: 0.95rem;
        }

        .field-list {
            display: flex;
            flex-wrap: wrap;
            gap: 6px;
        }

        .field-item {
            background: #e9ecef;
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 0.9rem;
            color: #495057;
        }

        /* Update existing schedule-params class to ensure proper spacing */
        .schedule-params {
            background: white;
            padding: 25px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            margin-bottom: 30px;
        }
        .params-title {
            font-weight: 600;
            margin-bottom: 10px;
            color: #2c3e50;
        }
        .modal-overlay {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: rgba(0, 0, 0, 0.5);
            display: none;
        }  
        .modal-overlay.show {
            display: block;
        }
        .modal-content {
            position: relative;
            top: 10%;
            margin: 0 auto;
            padding: 20px;
            background: white;
            width: 90%;
            max-width: 500px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }
        .notification {
            animation: slideIn 0.3s ease-out;
            box-shadow: 0 2px 5px rgba(0,0,0,0.2);
        }
        .days-picker {
            margin-top: 10px;
        }

        .day-circles {
            display: flex;
            gap: 10px;
            justify-content: center;
            margin-bottom: 20px;
        }

        .day-circle {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            border: 2px solid #3498db;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            font-weight: 500;
            color: #3498db;
            background-color: white;
            transition: all 0.2s ease;
        }

        .day-circle.selected {
            background-color: #3498db;
            color: white;
        }

        .time-input-group {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 15px;
            padding: 10px;
            background-color: #f8f9fa;
            border-radius: 4px;
        }

        .time-input-group label {
            min-width: 100px;
            font-weight: normal;
        }

        .time-inputs {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .time-inputs input[type="time"] {
            padding: 5px;
            border: 1px solid #ddd;
            border-radius: 4px;
            width: 130px;
        }

        .time-inputs span {
            color: #666;
        }
        .day-section {
            margin-bottom: 20px;
            padding: 15px;
            background-color: #f8f9fa;
            border-radius: 6px;
        }
        
        .day-section:last-child {
            margin-bottom: 0;
        }
        
        .day-title {
            color: #2c3e50;
            font-size: 1.1em;
            font-weight: 500;
            padding-bottom: 10px;
            margin-bottom: 10px;
            border-bottom: 1px solid #dee2e6;
        }
        
        .edit-btn {
            padding: 6px 12px;
            background-color: #3498db;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 14px;
            transition: background-color 0.2s;
        }
        
        .edit-btn:hover {
            background-color: #2980b9;
        }

        .schedule-params {
            background: white;
            padding: 25px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            margin-bottom: 30px;
        }

        .params-header {
            color: #2c3e50;
            font-size: 1.25rem;
            font-weight: 600;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 2px solid #eee;
        }

        .params-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 25px;
        }

        .param-group {
            display: flex;
            flex-direction: column;
            gap: 5px;
        }

        .param-label {
            color: #666;
            font-size: 0.9rem;
            font-weight: 500;
        }

        .param-value {
            color: #2c3e50;
            font-size: 1.1rem;
            font-weight: 600;
        }

        .slots-status {
            padding: 20px;
            border-radius: 6px;
            margin-top: 20px;
        }

        .status-success {
            background-color: #ebfbee;
            border: 1px solid #d1fadf;
        }

        .status-error {
            background-color: #fef2f2;
            border: 1px solid #fee2e2;
        }

        .status-content {
            text-align: center;
        }

        .status-header {
            font-weight: 600;
            margin-bottom: 10px;
            color: #374151;
        }

        .status-details {
            font-size: 1.1rem;
            font-weight: 500;
        }

        .status-success .status-details {
            color: #059669;
        }

        .status-error .status-details {
            color: #dc2626;
        }

        .slots-divider {
            margin: 0 8px;
            color: #666;
        }

        .extra-slots {
            margin-top: 8px;
            font-size: 0.9rem;
            color: #059669;
        }
        .fields-container {
            margin-top: 15px;
            border-top: 1px solid #eee;
            padding-top: 15px;
        }

        .fields-input {
            display: flex;
            align-items: center;
            gap: 15px;
            margin-top: 5px;
            margin-bottom: 10px;
        }

        .fields-input label {
            min-width: 100px;
            color: #666;
        }

        .fields-input input[type="number"] {
            width: 45px;
            padding: 5px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }

        .fields-input input[type="text"] {
            width: 150px;
            padding: 5px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }

        .fields-number::-webkit-inner-spin-button,
        .fields-number::-webkit-outer-spin-button {
            opacity: 1;
        }

        .fields-section {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .game-details {
            display: flex;
            align-items: center;
            gap: 15px;
            min-width: 200px;
            justify-content: flex-end;
        }

        .time, .field-name {
            color: #666;
        }

        .field-name {
            background: #f0f0f0;
            padding: 2px 8px;
            border-radius: 4px;
            font-size: 0.9em;
        }

        .open-slot {
            background-color: #e7ffe7;
            padding: 12px;
            border-bottom: 1px solid #eee;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .open-slot .slot-info {
            color: #666;
        }


        @keyframes slideIn {
            from {
                transform: translateX(100%);
                opacity: 0;
            }
            to {
                transform: translateX(0);
                opacity: 1;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>LeagueSuite Scheduler</h1>
        
        <div class="form-container">
            <form method="POST" action="/scheduler"> 
                <!-- @ csrf generates token for form request -->
                @csrf
                
                {{-- Form options (eg number of weeks, game length, etc) --}}
                <div class="form-group">
                    <label for="weeks">Number of Weeks:</label>
                    <input type="number" id="weeks" name="weeks" min="1" value="{{ old('weeks', $params['weeks'] ?? '') }}" required>
                    @error('weeks')
                        <div class="error">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="gameLength">Game Length (minutes):</label>
                    <input type="number" id="gameLength" name="gameLength" min="15" step="15" value="{{ old('gameLength', $params['gameLength'] ?? '') }}" required>
                    @error('gameLength')
                        <div class="error">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="targetGames">Target Number of Games Per Team:</label>
                    <input type="number" id="targetGames" name="targetGames" min="1" step="1" value="{{ old('targetGames', $params['targetGames'] ?? '') }}" required>
                    @error('targetGames')
                        <div class="error">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label>Select Days and Times:</label>
                    <div class="days-picker">
                        <div class="day-circles">
                            @php
                                $days = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'];
                                $dayLetters = ['M', 'T', 'W', 'T', 'F', 'S', 'S'];
                            @endphp
                            
                            @foreach($days as $index => $day)
                                <div class="day-circle {{ old($day.'_enabled', $params[$day.'_enabled'] ?? false) ? 'selected' : '' }}"
                                     data-day="{{ $day }}">
                                    <input type="checkbox" 
                                           name="{{ $day }}_enabled" 
                                           id="{{ $day }}_enabled"
                                           value="1"
                                           style="display: none;"
                                           {{ old($day.'_enabled', $params[$day.'_enabled'] ?? false) ? 'checked' : '' }}>
                                    {{ $dayLetters[$index] }}
                                </div>
                            @endforeach
                        </div>
                        
                        <div id="timeInputsContainer">
                            @foreach($days as $day)
                                <div class="time-input-group" id="time-{{ $day }}" 
                                     style="{{ old($day.'_enabled', $params[$day.'_enabled'] ?? false) ? '' : 'display: none;' }}">
                                    <label>{{ ucfirst($day) }}:</label>
                                    <div class="time-inputs">
                                        <input type="time" 
                                               name="{{ $day }}_start" 
                                               value="{{ old($day.'_start', $params[$day.'_start'] ?? '09:00') }}"
                                               {{ old($day.'_enabled', $params[$day.'_enabled'] ?? false) ? 'required' : '' }}>
                                        <span>to</span>
                                        <input type="time" 
                                               name="{{ $day }}_end" 
                                               value="{{ old($day.'_end', $params[$day.'_end'] ?? '17:00') }}"
                                               {{ old($day.'_enabled', $params[$day.'_enabled'] ?? false) ? 'required' : '' }}>
                                    </div>
                                    <div class="fields-input">
                                        <div class="fields-section">
                                            <label>Number of Fields:</label>
                                            <input type="number" 
                                                   name="{{ $day }}_fields"
                                                   min="1"
                                                   value="{{ old($day.'_fields', $params[$day.'_fields'] ?? '1') }}"
                                                   {{ old($day.'_enabled', $params[$day.'_enabled'] ?? false) ? 'required' : '' }}
                                                   class="fields-number">
                                        </div>
                                        <div class="fields-section">
                                            <label>Field Name:</label>
                                            <input type="text"
                                                   name="{{ $day }}_field_name"
                                                   value="{{ old($day.'_field_name', $params[$day.'_field_name'] ?? '') }}"
                                                   placeholder="Enter field name"
                                                   {{ old($day.'_enabled', $params[$day.'_enabled'] ?? false) ? 'required' : '' }}>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                <button type="submit" class="submit-btn">Generate Schedule</button>
            </form>
        </div>

            @if(isset($schedule))
            <!-- Parameters Section -->
            <div class="schedule-params">
                <h3 class="params-header">Schedule Parameters</h3>
                
                <div class="params-grid">
                    <div class="param-group">
                        <div class="param-label">Weeks</div>
                        <div class="param-value">{{ $params['weeks'] }}</div>
                    </div>
                    <div class="param-group">
                        <div class="param-label">Game Length</div>
                        <div class="param-value">{{ $params['gameLength'] }} minutes</div>
                    </div>
                    <div class="param-group">
                        <div class="param-label">Number of Teams</div>
                        <div class="param-value">{{ $params['numTeams'] }}</div>
                    </div>
                    <div class="param-group">
                        <div class="param-label">Games Per Team</div>
                        <div class="param-value">{{ $params['targetGames'] }}</div>
                    </div>
                </div>
            
                @php
                    $slotsNeeded = $params['targetGames'] * $params['numTeams'];
                    $slotsNeeded = $slotsNeeded / 2;
                    $extraSlots = $totalGameSlots - $slotsNeeded;
                    $statusClass = $slotsNeeded <= $totalGameSlots ? 'status-success' : 'status-error';
                @endphp
            
                <div class="slots-status {{ $statusClass }}">
                    <div class="status-content">
                        <div class="status-header">Game Slots Status</div>
                        <div class="status-details">
                            <span class="slots-needed">{{ $slotsNeeded }} needed</span>
                            <span class="slots-divider">/</span>
                            <span class="slots-available">{{ $totalGameSlots }} available</span>
                        </div>
                        @if($extraSlots > 0)
                            <div class="extra-slots">{{ $extraSlots }} extra slots available</div>
                        @endif
                    </div>
                </div>
            
                <div class="days-times-section">
                    <h4 class="params-subheader">Available Days and Times</h4>
                    <div class="days-grid">
                        @php
                            $enabledDays = [];
                            $days = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'];
                            foreach ($days as $day) {
                                if (isset($params[$day.'_enabled']) && $params[$day.'_enabled']) {
                                    $enabledDays[] = $day;
                                }
                            }
                        @endphp
                        
                        @foreach($days as $day)
                            @if(isset($params[$day.'_enabled']) && $params[$day.'_enabled'])
                                <div class="day-param-group">
                                    <div class="day-header">
                                        <span class="day-name">{{ ucfirst($day) }}</span>
                                    </div>
                                    <div class="day-details">
                                        <div class="time-slot">{{ $params[$day.'_start'] }} to {{ $params[$day.'_end'] }}</div>
                                        <div class="field-list">
                                            @for($i = 1; $i <= $params[$day.'_fields']; $i++)
                                                <span class="field-item">
                                                    {{ $params[$day.'_field_name'] }} {{ $i }}
                                                </span>
                                            @endfor
                                        </div>
                                    </div>
                                </div>
                            @endif
                        @endforeach
                    </div>
                </div>

                {{-- Removed for demo to rob
                <div class="stats-container">
                    <div class="params-title" style="margin-top: 20px;">Games Per Team:</div>
                    <div class="team-stats">
                        @foreach($statistics as $stat)
                            <div class="team-stat">
                                <span class="team-id">#{{ $stat['id'] }}</span>
                                <span class="team-name">{{ $stat['name'] }}</span>
                                <span class="game-count">{{ $stat['games'] }} games</span>
                            </div>
                        @endforeach
                    </div>
                </div>
                --}}


            </div>
            @foreach($schedule as $weekNumber => $games)
                <div class="week-container">
                    <h2 class="week-title">Week {{ $weekNumber }}</h2>
                    @php
                        $groupedGames = collect($games)->groupBy('day');
                    @endphp
                    
                    @foreach($enabledDays as $dayName)
                        @php
                            $dayNameLower = strtolower($dayName);
                            $dayGames = $groupedGames->has($dayNameLower) ? $groupedGames->get($dayNameLower) : collect();
                            $dayStart = $params[$dayNameLower.'_start'];
                            $dayEnd = $params[$dayNameLower.'_end'];
                            $gameLength = $params['gameLength'];
                            $currentTime = strtotime($dayStart);
                            $endTime = strtotime($dayEnd);
                            $timeSlots = [];
                            while ($currentTime + ($gameLength * 60) <= $endTime) {
                                $timeSlots[] = date('H:i', $currentTime);
                                $currentTime += $gameLength * 60;
                            }
                            $fieldsCount = $params[$dayNameLower.'_fields'];
                            $fieldName = $params[$dayNameLower.'_field_name'];
                            $fieldGroupedGames = $dayGames->groupBy('field');
                        @endphp
                        
                        <div class="day-section">
                            <h3 class="day-title">{{ ucfirst($dayName) }}</h3>
                            @for($i = 1; $i <= $fieldsCount; $i++)
                                @php
                                    $currentField = $fieldName . ' ' . $i;
                                    $gamesInField = $fieldGroupedGames->get($currentField, collect());
                                    $occupiedSlots = $gamesInField->pluck('time')->toArray();
                                @endphp
                                <div class="field-section">
                                    <h4 class="field-title">{{ $currentField }}</h4>
                                    @foreach($timeSlots as $slot)
                                        @if(in_array($slot, $occupiedSlots))
                                            @php $game = $gamesInField->firstWhere('time', $slot) @endphp
                                            <div class="game">
                                                <div class="teams">
                                                    <div class="team">
                                                        <span class="team-id">#{{ $game['team1_id'] }}</span>
                                                        <span class="team-name">{{ $game['team1_name'] }}</span>
                                                    </div>
                                                    <span class="vs">vs</span>
                                                    <div class="team">
                                                        <span class="team-id">#{{ $game['team2_id'] }}</span>
                                                        <span class="team-name">{{ $game['team2_name'] }}</span>
                                                    </div>
                                                </div>
                                                <div class="game-details">
                                                    <span class="time">{{ $game['time'] }}</span>
                                                    <span class="field-name">{{ $game['field'] }}</span>
                                                </div>
                                                <button 
                                                    onclick="openEditModal('{{ $game['team1_id'] }}', '{{ $game['team2_id'] }}', '{{ $game['day'] }}', '{{ $game['field'] }}', '{{ $game['time'] }}')" 
                                                    class="edit-btn">
                                                    Edit Game
                                                </button>
                                            </div>
                                        @else
                                            <div class="open-slot">
                                                <div class="slot-info">
                                                    Open Spot • {{ $currentField }} • {{ $slot }}
                                                </div>
                                            </div>
                                        @endif
                                    @endforeach
                                </div>
                            @endfor
                        </div>
                    @endforeach
                </div>
            @endforeach
        @endif
    </div>
    <!-- MODAL -->
    <div id="editGameModal" class="modal-overlay">
        <div class="modal-content">
            <div class="mt-3">
                <h3 style="font-size: 1.125rem; font-weight: 500; color: #1a202c; margin-bottom: 1rem;">Edit Game</h3>
                <div class="mt-2 px-7 py-3">
                    <form id="editGameForm" action="{{ route('editGame') }}" method="POST">
                        @csrf
                        
                        <div style="margin-bottom: 1rem;">
                            <label style="display: block; font-weight: 500; margin-bottom: 0.5rem;" for="team1">
                                Team 1
                            </label>
                            <select id="modalTeam1" 
                                name="team1_id" 
                                style="width: 100%; padding: 0.5rem; border: 1px solid #e2e8f0; border-radius: 0.375rem; background-color: white;">
                            </select>
                        </div>
    
                        <div style="margin-bottom: 1rem;">
                            <label style="display: block; font-weight: 500; margin-bottom: 0.5rem;" for="team2">
                                Team 2
                            </label>
                            <select id="modalTeam2" 
                                name="team2_id" 
                                style="width: 100%; padding: 0.5rem; border: 1px solid #e2e8f0; border-radius: 0.375rem; background-color: white;">
                            </select>
                        </div>
                        
                        <div style="margin-bottom: 1rem;">
                            <label style="display: block; font-weight: 500; margin-bottom: 0.5rem;" for="gameDay">
                                Game Day
                            </label>
                            <select id="modalGameDay" 
                                name="day" 
                                style="width: 100%; padding: 0.5rem; border: 1px solid #e2e8f0; border-radius: 0.375rem; background-color: white;">
                                @foreach(['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'] as $day)
                                    @if(isset($params[$day.'_enabled']) && $params[$day.'_enabled'])
                                        <option value="{{ $day }}">{{ ucfirst($day) }}</option>
                                    @endif
                                @endforeach
                            </select>
                        </div>

                        <div style="margin-bottom: 1rem;">
                            <label style="display: block; font-weight: 500; margin-bottom: 0.5rem;" for="field">
                                Field
                            </label>
                            <select id="modalField" 
                                name="field" 
                                style="width: 100%; padding: 0.5rem; border: 1px solid #e2e8f0; border-radius: 0.375rem; background-color: white;">
                            </select>
                        </div>
                        
                        <div style="margin-bottom: 1rem;">
                            <label style="display: block; font-weight: 500; margin-bottom: 0.5rem;" for="gameTime">
                                Game Time
                            </label>
                            <input type="time" 
                                id="modalGameTime" 
                                name="time" 
                                style="width: 100%; min-width: 200px; padding: 0.5rem; border: 1px solid #e2e8f0; border-radius: 0.375rem;">
                        </div>
                        
                        <div style="display: flex; justify-content: flex-end; gap: 1rem; margin-top: 1.5rem;">
                            <button type="button" 
                                    onclick="closeEditModal()" 
                                    style="padding: 0.5rem 1rem; background-color: #e2e8f0; border-radius: 0.375rem; cursor: pointer;">
                                Cancel
                            </button>
                            <button type="submit"
                                    style="padding: 0.5rem 1rem; background-color: #3498db; color: white; border-radius: 0.375rem; cursor: pointer;">
                                Save Changes
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Must remain top of script
        let originalTeam1Id = null;
        let originalTeam2Id = null;
        let currentGameElement = null;
        let originalDay = null;
        let originalField = null;
        let originalTime = null;

        // Extract teams from the schedule
        function extractTeamsFromSchedule() {
            const teams = new Map();
            const games = document.querySelectorAll('.game');
            
            games.forEach(game => {
                const team1Id = game.querySelector('.team:first-child .team-id').textContent.replace('#', '');
                const team1Name = game.querySelector('.team:first-child .team-name').textContent;
                const team2Id = game.querySelector('.team:last-child .team-id').textContent.replace('#', '');
                const team2Name = game.querySelector('.team:last-child .team-name').textContent;
                
                teams.set(team1Id, team1Name);
                teams.set(team2Id, team2Name);
            });
            
            return Array.from(teams, ([id, name]) => ({ id, name }));
        }

        function populateTeamDropdowns() {
            const teams = extractTeamsFromSchedule();
            const team1Select = document.getElementById('modalTeam1');
            const team2Select = document.getElementById('modalTeam2');
            
            team1Select.innerHTML = '';
            team2Select.innerHTML = '';
            
            teams.forEach(team => {
                const option1 = new Option(team.name, team.id);
                const option2 = new Option(team.name, team.id);
                team1Select.add(option1);
                team2Select.add(option2);
            });
        }

        function populateFieldDropdown() {
            const selectedDay = document.getElementById('modalGameDay').value;
            const fieldSelect = document.getElementById('modalField');
            
            // Clear existing options
            fieldSelect.innerHTML = '';
            
            // Get the field information from the day's parameters
            const fieldNameInput = document.querySelector(`input[name="${selectedDay}_field_name"]`);
            const numFieldsInput = document.querySelector(`input[name="${selectedDay}_fields"]`);
            
            if (fieldNameInput && numFieldsInput) {
                const fieldName = fieldNameInput.value;
                const numFields = parseInt(numFieldsInput.value);
                
                // Create options for each field
                for (let i = 1; i <= numFields; i++) {
                    const option = new Option(`${fieldName} ${i}`, `${fieldName} ${i}`);
                    fieldSelect.add(option);
                }
                
                // If there's a current game being edited, try to select its field
                if (currentGameElement) {
                    const currentField = currentGameElement.querySelector('.field-name').textContent.trim();
                    // Find and select the matching option if it exists
                    const matchingOption = Array.from(fieldSelect.options).find(option => option.value === currentField);
                    if (matchingOption) {
                        fieldSelect.value = currentField;
                    }
                }
            }
        }


        function openEditModal(team1Id, team2Id, day, field, gameTime) {
            originalTeam1Id = team1Id;
            originalTeam2Id = team2Id;
            originalDay = day;
            originalField = field;
            originalTime = gameTime;
            currentGameElement = event.target.closest('.game');
            
            populateTeamDropdowns();
            
            document.getElementById('modalTeam1').value = team1Id;
            document.getElementById('modalTeam2').value = team2Id;
            document.getElementById('modalGameDay').value = day;
            document.getElementById('modalGameTime').value = gameTime;
            document.getElementById('editGameModal').classList.add('show');

            // Trigger change event to populate fields based on the selected day
            document.getElementById('modalGameDay').dispatchEvent(new Event('change'));
            
            document.getElementById('modalTeam1').dispatchEvent(new Event('change'));
            document.getElementById('modalTeam2').dispatchEvent(new Event('change'));
        }

        function closeEditModal() {
            document.getElementById('editGameModal').classList.remove('show');
        }

        
        function updateGameDisplay(team1Id, team1Name, team2Id, team2Name, gameTime, day, field) {
            if (!currentGameElement) return;

            // Helper functions
            const createOpenSlot = (time, fieldName) => {
                const openSlot = document.createElement('div');
                openSlot.className = 'open-slot';
                openSlot.innerHTML = `
                    <div class="slot-info">
                        Open Spot • ${fieldName} • ${time}
                    </div>
                `;
                return openSlot;
            };

            const findOrCreateDaySection = (dayName, weekContainer) => {
                const dayOrder = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];
                const formattedDay = dayName.charAt(0).toUpperCase() + dayName.slice(1);
                
                // Try to find existing day section
                let daySection = Array.from(weekContainer.querySelectorAll('.day-section')).find(section => {
                    return section.querySelector('.day-title').textContent.trim() === formattedDay;
                });

                if (!daySection) {
                    // Create new day section
                    daySection = document.createElement('div');
                    daySection.className = 'day-section';
                    
                    const title = document.createElement('h3');
                    title.className = 'day-title';
                    title.textContent = formattedDay;
                    daySection.appendChild(title);

                    // Find correct position to insert
                    const sections = weekContainer.querySelectorAll('.day-section');
                    const newIndex = dayOrder.indexOf(formattedDay);
                    
                    let inserted = false;
                    for (const existingSection of sections) {
                        const existingDay = existingSection.querySelector('.day-title').textContent.trim();
                        const existingIndex = dayOrder.indexOf(existingDay);
                        
                        if (existingIndex > newIndex) {
                            existingSection.parentNode.insertBefore(daySection, existingSection);
                            inserted = true;
                            break;
                        }
                    }
                    if (!inserted) weekContainer.appendChild(daySection);
                }
                
                return daySection;
            };

            const findOrCreateFieldSection = (fieldName, daySection) => {
                let fieldSection = Array.from(daySection.querySelectorAll('.field-section')).find(section => {
                    return section.querySelector('.field-title').textContent.trim() === fieldName;
                });

                if (!fieldSection) {
                    fieldSection = document.createElement('div');
                    fieldSection.className = 'field-section';
                    
                    const title = document.createElement('h4');
                    title.className = 'field-title';
                    title.textContent = fieldName;
                    fieldSection.appendChild(title);

                    // Insert in alphabetical order
                    const existingSections = daySection.querySelectorAll('.field-section');
                    let inserted = false;
                    
                    for (const section of existingSections) {
                        if (section.querySelector('.field-title').textContent.localeCompare(fieldName) > 0) {
                            section.parentNode.insertBefore(fieldSection, section);
                            inserted = true;
                            break;
                        }
                    }
                    if (!inserted) daySection.appendChild(fieldSection);
                }
                
                return fieldSection;
            };

            // Capture original state
            const originalWeekContainer = currentGameElement.closest('.week-container');
            const originalDaySection = currentGameElement.closest('.day-section');
            const originalFieldSection = currentGameElement.closest('.field-section');
            const originalTimeValue = originalTime;
            const originalFieldValue = originalFieldSection ? 
                originalFieldSection.querySelector('.field-title').textContent.trim() : '';   
            const originalIndex = Array.from(originalFieldSection.children).indexOf(currentGameElement);


            // Update game details
            currentGameElement.querySelector('.team:first-child .team-id').textContent = `#${team1Id}`;
            currentGameElement.querySelector('.team:first-child .team-name').textContent = team1Name;
            currentGameElement.querySelector('.team:last-child .team-id').textContent = `#${team2Id}`;
            currentGameElement.querySelector('.team:last-child .team-name').textContent = team2Name;
            currentGameElement.querySelector('.time').textContent = gameTime;
            currentGameElement.querySelector('.field-name').textContent = field;

            // Update edit button
            const editButton = currentGameElement.querySelector('button');
            editButton.setAttribute('onclick', 
                `openEditModal('${team1Id}', '${team2Id}', '${day}', '${field}', '${gameTime}')`);

            // Handle day/field changes
            const newDaySection = findOrCreateDaySection(day, originalWeekContainer);
            const newFieldSection = findOrCreateFieldSection(field, newDaySection);

            if (originalDay !== day || originalField !== field) {
                // Move to new field section
                newFieldSection.appendChild(currentGameElement);
            }

            // Handle time changes
            if (originalTime !== gameTime || originalDay !== day || originalField !== field) {
                if (originalFieldSection) {
                    const newOpenSlot = createOpenSlot(originalTime, originalField);
                    const allSlots = Array.from(originalFieldSection.children);
                    
                    // Insert the open slot at the exact same position
                    if (originalIndex >= 0 && originalIndex < allSlots.length) {
                        originalFieldSection.insertBefore(newOpenSlot, allSlots[originalIndex]);
                    } else {
                        originalFieldSection.appendChild(newOpenSlot);
                    }
                }
                
                // Remove open slot at new position and insert game
                const newSlots = Array.from(newFieldSection.querySelectorAll('.game, .open-slot'));
                let inserted = false;

                // First try to replace an open slot with matching time
                for (let i = 0; i < newSlots.length; i++) {
                    const slot = newSlots[i];
                    const slotTime = slot.classList.contains('game') ? 
                        slot.querySelector('.time').textContent.trim() : 
                        slot.querySelector('.slot-info').textContent.split('•')[2].trim();
                    
                    if (slotTime === gameTime && slot.classList.contains('open-slot')) {
                        slot.parentNode.replaceChild(currentGameElement, slot);
                        inserted = true;
                        break;
                    }
                }

                // If no open slot was replaced, insert in correct position
                if (!inserted) {
                    inserted = false;
                    for (let i = 0; i < newSlots.length; i++) {
                        const slot = newSlots[i];
                        const slotTime = slot.classList.contains('game') ? 
                            slot.querySelector('.time').textContent.trim() : 
                            slot.querySelector('.slot-info').textContent.split('•')[2].trim();
                        
                        // If we find a slot with the same time, keep looking until we find
                        // either a later time or the end of slots with this time
                        if (slotTime === gameTime) {
                            continue;
                        }
                        
                        if (slotTime.localeCompare(gameTime) > 0) {
                            slot.parentNode.insertBefore(currentGameElement, slot);
                            inserted = true;
                            break;
                        }
                    }
                    
                    if (!inserted) {
                        newFieldSection.appendChild(currentGameElement);
                    }
                }
            }

        }

        function showNotification(message, type) {
            const notification = document.createElement('div');
            notification.className = `notification ${type}`;
            notification.style.position = 'fixed';
            notification.style.top = '20px';
            notification.style.right = '20px';
            notification.style.padding = '10px 20px';
            notification.style.borderRadius = '4px';
            notification.style.zIndex = '1000';
            notification.style.backgroundColor = type === 'success' ? '#4CAF50' : '#f44336';
            notification.style.color = 'white';
            notification.textContent = message;
            
            document.body.appendChild(notification);
            
            setTimeout(() => {
                notification.remove();
            }, 4000);
        }

        // Event Listeners
        document.getElementById('modalTeam1').addEventListener('change', function() {
            const team2Select = document.getElementById('modalTeam2');
            const selectedTeam = this.value;
            
            Array.from(team2Select.options).forEach(option => {
                option.disabled = option.value === selectedTeam;
            });
        });

        // NEW LISTENER
        document.getElementById('modalGameDay').addEventListener('change', function() {
            populateFieldDropdown();
        });

        document.getElementById('modalTeam2').addEventListener('change', function() {
            const team1Select = document.getElementById('modalTeam1');
            const selectedTeam = this.value;
            
            Array.from(team1Select.options).forEach(option => {
                option.disabled = option.value === selectedTeam;
            });
        });

        document.getElementById('editGameModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeEditModal();
            }
        });

        document.getElementById('editGameForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            const team1Id = formData.get('team1_id');
            const team2Id = formData.get('team2_id');
            const gameTime = formData.get('time');
            const day = formData.get('day');
            const field = formData.get('field');
            
            const token = document.querySelector('input[name="_token"]').value;

            //console.log(field);
            //console.log(originalField);
            
            fetch('/edit-game', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': token,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    team1_id: team1Id,
                    team2_id: team2Id,
                    time: gameTime,
                    day: day,
                    field: field,
                    original_team1_id: originalTeam1Id,
                    original_team2_id: originalTeam2Id,
                    original_day: originalDay,
                    original_field: originalField,
                })
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    updateGameDisplay(
                        team1Id,
                        data.data.team1_name,
                        team2Id,
                        data.data.team2_name,
                        gameTime,
                        day,
                        field,
                    );
                    closeEditModal();
                    showNotification('Game updated successfully!', 'success');
                } else {
                    showNotification(data.message || 'Failed to update game.', 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                //showNotification('An error occurred while updating the game', 'error');
                showNotification(error, 'error'); //Error is TypeError: Cannot read properties of null (reading 'charAt')
            });
        });


    // New version of handling day/time picker
    document.addEventListener('DOMContentLoaded', function() {
        const dayCircles = document.querySelectorAll('.day-circle');
        
        dayCircles.forEach(circle => {
            circle.addEventListener('click', function() {
                const day = this.dataset.day;
                const checkbox = this.querySelector('input[type="checkbox"]');
                const timeInputs = document.getElementById(`time-${day}`);
                
                this.classList.toggle('selected');
                checkbox.checked = !checkbox.checked;
                
                if (timeInputs) {
                    timeInputs.style.display = checkbox.checked ? '' : 'none';
                    const inputs = timeInputs.querySelectorAll('input[type="time"]');
                    inputs.forEach(input => {
                        input.required = checkbox.checked;
                    });
                }
            });
        });
    });

    </script>
</body>
</html>