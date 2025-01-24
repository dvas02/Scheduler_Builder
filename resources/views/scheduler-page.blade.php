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
            padding: 10px;
            border-bottom: 1px solid #eee;
        }
        .game:last-child {
            border-bottom: none;
        }
        .teams {
            flex: 1;
            display: flex;
            justify-content: center;
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
        .schedule-params {
            background: #f8f9fa;
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 8px;
            font-size: 0.9em;
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
            top: 20%;
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
            <form method="POST" action="/scheduler"> <!-- Replaced generate-schedule -->
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
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>



                <button type="submit" class="submit-btn">Generate Schedule</button>
            </form>
        </div>

            @if(isset($schedule))
            <div class="schedule-params">
                <div class="params-title">Current Schedule Parameters:</div>
                <div>Weeks: {{ $params['weeks'] }}</div>
                <div>Game Length: {{ $params['gameLength'] }} minutes</div>
                
                {{-- Days and Times Section --}}
                <div class="days-times-section" style="margin-top: 15px;">
                    <div class="params-subtitle" style="font-weight: 500; margin-bottom: 10px;">Available Days and Times:</div>
                    @php
                        $days = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'];
                        $enabledDays = [];
                    @endphp
                    
                    @foreach($days as $day)
                        @if(isset($params[$day.'_enabled']) && $params[$day.'_enabled'])
                            <div class="day-time-entry" style="margin-bottom: 8px;">
                                <span style="text-transform: capitalize; font-weight: 500;">{{ $day }}:</span>
                                <span>{{ $params[$day.'_start'] }} to {{ $params[$day.'_end'] }}</span>
                            </div>
                            @php
                                $enabledDays[] = ucfirst($day);
                            @endphp
                        @endif
                    @endforeach
                    
                    @if(empty($enabledDays))
                        <div style="color: #666;">No days selected</div>
                    @endif
                </div>
                
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
            </div>


            @foreach($schedule as $weekNumber => $games)
                <div class="week-container">
                    <h2 class="week-title">Week {{ $weekNumber }}</h2>
                    
                    {{-- Group games by day --}}
                    @php
                        $groupedGames = collect($games)->groupBy('day');
                    @endphp
                    
                    @foreach($groupedGames as $day => $dayGames)
                        <div class="day-section">
                            <h3 class="day-title">
                                {{ ucfirst($day) }}
                            </h3>
                            
                            @foreach($dayGames as $game)
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
                                    <div class="time">{{ $game['time'] }}</div>
                                    <button 
                                        onclick="openEditModal('{{ $game['team1_id'] }}', '{{ $game['team2_id'] }}', '{{ $game['day'] }}', '{{ $game['time'] }}')" 
                                        class="edit-btn">
                                        Edit Game
                                    </button>
                                </div>
                            @endforeach
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

        /*function openEditModal(team1Id, team2Id, gameTime) {
            originalTeam1Id = team1Id;
            originalTeam2Id = team2Id;
            currentGameElement = event.target.closest('.game');
            
            populateTeamDropdowns();
            
            document.getElementById('modalTeam1').value = team1Id;
            document.getElementById('modalTeam2').value = team2Id;
            document.getElementById('modalGameTime').value = gameTime;
            document.getElementById('editGameModal').classList.add('show');
            
            document.getElementById('modalTeam1').dispatchEvent(new Event('change'));
            document.getElementById('modalTeam2').dispatchEvent(new Event('change'));
        }*/
        function openEditModal(team1Id, team2Id, day, gameTime) {
            originalTeam1Id = team1Id;
            originalTeam2Id = team2Id;
            originalDay = day;
            currentGameElement = event.target.closest('.game');
            
            populateTeamDropdowns();
            
            document.getElementById('modalTeam1').value = team1Id;
            document.getElementById('modalTeam2').value = team2Id;
            document.getElementById('modalGameDay').value = day;
            document.getElementById('modalGameTime').value = gameTime;
            document.getElementById('editGameModal').classList.add('show');
            
            document.getElementById('modalTeam1').dispatchEvent(new Event('change'));
            document.getElementById('modalTeam2').dispatchEvent(new Event('change'));
        }

        function closeEditModal() {
            document.getElementById('editGameModal').classList.remove('show');
        }

        /*function updateGameDisplay(team1Id, team1Name, team2Id, team2Name, gameTime) {
            if (!currentGameElement) return;
            
            const team1Element = currentGameElement.querySelector('.team:first-child');
            team1Element.querySelector('.team-id').textContent = '#' + team1Id;
            team1Element.querySelector('.team-name').textContent = team1Name;
            
            const team2Element = currentGameElement.querySelector('.team:last-child');
            team2Element.querySelector('.team-id').textContent = '#' + team2Id;
            team2Element.querySelector('.team-name').textContent = team2Name;
            
            currentGameElement.querySelector('.time').textContent = gameTime;
            
            // Update the Edit Game button's onclick handler with new values
            const editButton = currentGameElement.querySelector('button');
            editButton.setAttribute('onclick', `openEditModal('${team1Id}', '${team2Id}', '${gameTime}')`);
        }*/
        function updateGameDisplay(team1Id, team1Name, team2Id, team2Name, gameTime, day) {
            if (!currentGameElement) return;
            
            // Find the current day section
            const currentDaySection = currentGameElement.closest('.day-section');
            const newDayTitle = day.charAt(0).toUpperCase() + day.slice(1);
            
            // Update the game details
            const team1Element = currentGameElement.querySelector('.team:first-child');
            team1Element.querySelector('.team-id').textContent = '#' + team1Id;
            team1Element.querySelector('.team-name').textContent = team1Name;
            
            const team2Element = currentGameElement.querySelector('.team:last-child');
            team2Element.querySelector('.team-id').textContent = '#' + team2Id;
            team2Element.querySelector('.team-name').textContent = team2Name;
            
            currentGameElement.querySelector('.time').textContent = gameTime;
            
            // Update the Edit Game button's onclick handler
            const editButton = currentGameElement.querySelector('button');
            editButton.setAttribute('onclick', 
                `openEditModal('${team1Id}', '${team2Id}', '${day}', '${gameTime}')`);
            
            // If the day has changed, move the game to the correct day section
            /*if (originalDay !== day) {
                const targetDaySection = document.querySelector(`.day-section h3.day-title:contains('${newDayTitle}')`).closest('.day-section');
                if (targetDaySection) {
                    targetDaySection.appendChild(currentGameElement);
                    
                    // If the old day section is empty, remove it
                    if (currentDaySection.querySelectorAll('.game').length === 0) {
                        currentDaySection.remove();
                    }
                }
            }*/
            if (originalDay !== day) {
                // Find the target day section by iterating through all day sections
                const daySections = document.querySelectorAll('.day-section');
                let targetDaySection = null;
                
                for (const section of daySections) {
                    const titleElement = section.querySelector('.day-title');
                    if (titleElement && titleElement.textContent.trim().toLowerCase() === newDayTitle.toLowerCase()) {
                        targetDaySection = section;
                        break;
                    }
                }
                
                if (targetDaySection) {
                    targetDaySection.appendChild(currentGameElement);
                    
                    // If the old day section is empty, remove it
                    if (currentDaySection.querySelectorAll('.game').length === 0) {
                        currentDaySection.remove();
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
            }, 3000);
        }

        // Event Listeners
        document.getElementById('modalTeam1').addEventListener('change', function() {
            const team2Select = document.getElementById('modalTeam2');
            const selectedTeam = this.value;
            
            Array.from(team2Select.options).forEach(option => {
                option.disabled = option.value === selectedTeam;
            });
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

        // Single form submission handler
        /*document.getElementById('editGameForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            const team1Id = formData.get('team1_id');
            const team2Id = formData.get('team2_id');
            const gameTime = formData.get('time');
            
            // Get the CSRF token from the form
            const token = document.querySelector('input[name="_token"]').value;
            
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
                    original_team1_id: originalTeam1Id,
                    original_team2_id: originalTeam2Id
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
                        gameTime
                    );
                    closeEditModal();
                    showNotification('Game updated successfully!', 'success');
                } else {
                    showNotification(data.message || 'Failed to update game.', 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showNotification('An error occurred while updating the game.', 'error');
            });
        });*/
        document.getElementById('editGameForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            const team1Id = formData.get('team1_id');
            const team2Id = formData.get('team2_id');
            const gameTime = formData.get('time');
            const day = formData.get('day');
            
            const token = document.querySelector('input[name="_token"]').value;
            
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
                    original_team1_id: originalTeam1Id,
                    original_team2_id: originalTeam2Id,
                    original_day: originalDay
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
                        day
                    );
                    closeEditModal();
                    showNotification('Game updated successfully!', 'success');
                } else {
                    showNotification(data.message || 'Failed to update game.', 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showNotification('An error occurred while updating the game.', 'error');
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