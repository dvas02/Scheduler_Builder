<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>League Schedule Builder</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            padding-top: 20px;
            padding-bottom: 40px;
        }
        .location-details {
            padding: 10px;
            border: 1px solid #eee;
            border-radius: 5px;
            margin-top: 10px;
        }
    </style>
</head>
<body>
<div class="container">
    <h1 class="mb-4">League Schedule Builder</h1>

    <!-- Schedule Form -->
    <div class="card mb-4">
        <div class="card-header">
            <h5>Configure Schedule</h5>
        </div>
        <div class="card-body">
            <form method="POST" action="/scheduler">
                @csrf
                
                <!-- Basic Settings -->
                <div class="row mb-3">
                    <div class="col-md-4">
                        <label for="weeks" class="form-label">Number of Weeks</label>
                        <input type="number" class="form-control" id="weeks" name="weeks" min="1" value="{{ $params['weeks'] ?? 8 }}">
                    </div>
                    <div class="col-md-4">
                        <label for="gameLength" class="form-label">Game Length (minutes)</label>
                        <input type="number" class="form-control" id="gameLength" name="gameLength" min="15" step="15" value="{{ $params['gameLength'] ?? 60 }}">
                    </div>
                    <div class="col-md-4">
                        <label for="targetGames" class="form-label">Target Games per Team</label>
                        <input type="number" class="form-control" id="targetGames" name="targetGames" min="1" value="{{ $params['targetGames'] ?? 10 }}">
                    </div>
                </div>
                
                <!-- Days Configuration -->
                <div class="card mb-3">
                    <div class="card-header">
                        <h6>Available Days</h6>
                    </div>
                    <div class="card-body">
                        @php
                            $days = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'];
                            $displayDays = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];
                        @endphp
                        
                        <div class="accordion" id="daysAccordion">
                            @foreach($days as $index => $day)
                                <div class="accordion-item">
                                    <h2 class="accordion-header" id="heading{{ $day }}">
                                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" 
                                            data-bs-target="#collapse{{ $day }}" aria-expanded="false" aria-controls="collapse{{ $day }}">
                                            <div class="form-check form-switch">
                                                <input class="form-check-input day-toggle" type="checkbox" id="{{ $day }}_enabled" 
                                                    name="{{ $day }}_enabled" value="1" 
                                                    {{ isset($params[$day.'_enabled']) && $params[$day.'_enabled'] ? 'checked' : '' }}
                                                    onclick="event.stopPropagation();">
                                                <label class="form-check-label" for="{{ $day }}_enabled">{{ $displayDays[$index] }}</label>
                                            </div>
                                        </button>
                                    </h2>
                                    <div id="collapse{{ $day }}" class="accordion-collapse collapse" 
                                        aria-labelledby="heading{{ $day }}" data-bs-parent="#daysAccordion">
                                        <div class="accordion-body">
                                            <div class="row mb-3">
                                                <div class="col-md-6">
                                                    <label for="{{ $day }}_start" class="form-label">Start Time</label>
                                                    <input type="time" class="form-control" id="{{ $day }}_start" 
                                                        name="{{ $day }}_start" value="{{ $params[$day.'_start'] ?? '18:00' }}">
                                                </div>
                                                <div class="col-md-6">
                                                    <label for="{{ $day }}_end" class="form-label">End Time</label>
                                                    <input type="time" class="form-control" id="{{ $day }}_end" 
                                                        name="{{ $day }}_end" value="{{ $params[$day.'_end'] ?? '22:00' }}">
                                                </div>
                                            </div>
                                            
                                            <!-- Locations for this day -->
                                            <div class="card">
                                                <div class="card-header">
                                                    <h6>Locations Available on {{ $displayDays[$index] }}</h6>
                                                </div>
                                                <div class="card-body">
                                                    @foreach($locations ?? [] as $location)
                                                        <div class="location-section mb-3">
                                                            <div class="form-check form-switch mb-2">
                                                                <input class="form-check-input location-toggle" type="checkbox" 
                                                                    id="{{ $day }}_loc_{{ $location[0] }}_enabled" 
                                                                    name="{{ $day }}_loc_{{ $location[0] }}_enabled" value="1"
                                                                    {{ isset($params[$day.'_loc_'.$location[0].'_enabled']) && $params[$day.'_loc_'.$location[0].'_enabled'] ? 'checked' : '' }}>
                                                                <label class="form-check-label" for="{{ $day }}_loc_{{ $location[0] }}_enabled">
                                                                    {{ $location[1] }}
                                                                </label>
                                                            </div>
                                                            
                                                            <div class="location-details" id="{{ $day }}_loc_{{ $location[0] }}_details" 
                                                                 style="display: {{ isset($params[$day.'_loc_'.$location[0].'_enabled']) && $params[$day.'_loc_'.$location[0].'_enabled'] ? 'block' : 'none' }}">
                                                                <div class="row">
                                                                    <div class="col-md-3">
                                                                        <label for="{{ $day }}_loc_{{ $location[0] }}_start" class="form-label">Start Time</label>
                                                                        <input type="time" class="form-control" 
                                                                            id="{{ $day }}_loc_{{ $location[0] }}_start" 
                                                                            name="{{ $day }}_loc_{{ $location[0] }}_start" 
                                                                            value="{{ $params[$day.'_loc_'.$location[0].'_start'] ?? '' }}"
                                                                            placeholder="Use day default">
                                                                    </div>
                                                                    <div class="col-md-3">
                                                                        <label for="{{ $day }}_loc_{{ $location[0] }}_end" class="form-label">End Time</label>
                                                                        <input type="time" class="form-control" 
                                                                            id="{{ $day }}_loc_{{ $location[0] }}_end" 
                                                                            name="{{ $day }}_loc_{{ $location[0] }}_end" 
                                                                            value="{{ $params[$day.'_loc_'.$location[0].'_end'] ?? '' }}"
                                                                            placeholder="Use day default">
                                                                    </div>
                                                                    <div class="col-md-3">
                                                                        <label for="{{ $day }}_loc_{{ $location[0] }}_fields" class="form-label">Number of Fields</label>
                                                                        <input type="number" class="form-control" 
                                                                            id="{{ $day }}_loc_{{ $location[0] }}_fields" 
                                                                            name="{{ $day }}_loc_{{ $location[0] }}_fields" min="1" 
                                                                            value="{{ $params[$day.'_loc_'.$location[0].'_fields'] ?? '1' }}">
                                                                    </div>
                                                                    <div class="col-md-3">
                                                                        <label for="{{ $day }}_loc_{{ $location[0] }}_field_name" class="form-label">Field Name Prefix</label>
                                                                        <input type="text" class="form-control" 
                                                                            id="{{ $day }}_loc_{{ $location[0] }}_field_name" 
                                                                            name="{{ $day }}_loc_{{ $location[0] }}_field_name" 
                                                                            value="{{ $params[$day.'_loc_'.$location[0].'_field_name'] ?? 'Field' }}">
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
                
                <button type="submit" class="btn btn-primary">Generate Schedule</button>
            </form>
        </div>
    </div>

    <!-- Generated Schedule -->
    @if(isset($schedule))
        <div class="card">
            <div class="card-header">
                <h5>Generated Schedule</h5>
            </div>
            <div class="card-body">
                <div class="accordion" id="scheduleAccordion">
                    @foreach($schedule as $weekNumber => $games)
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="weekHeading{{ $weekNumber }}">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" 
                                    data-bs-target="#weekCollapse{{ $weekNumber }}" aria-expanded="false" 
                                    aria-controls="weekCollapse{{ $weekNumber }}">
                                    Week {{ $weekNumber }} ({{ count($games) }} games)
                                </button>
                            </h2>
                            <div id="weekCollapse{{ $weekNumber }}" class="accordion-collapse collapse" 
                                aria-labelledby="weekHeading{{ $weekNumber }}" data-bs-parent="#scheduleAccordion">
                                <div class="accordion-body">
                                    
                                    @php
                                        // Group games by day
                                        $gamesByDay = [];
                                        foreach ($games as $game) {
                                            if (!isset($gamesByDay[$game['day']])) {
                                                $gamesByDay[$game['day']] = [];
                                            }
                                            $gamesByDay[$game['day']][] = $game;
                                        }
                                        // Sort days
                                        ksort($gamesByDay);
                                    @endphp
                                    
                                    <div class="accordion" id="dayAccordion{{ $weekNumber }}">
                                        @foreach($gamesByDay as $day => $dayGames)
                                            <div class="accordion-item">
                                                <h2 class="accordion-header" id="dayHeading{{ $weekNumber }}_{{ $day }}">
                                                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" 
                                                        data-bs-target="#dayCollapse{{ $weekNumber }}_{{ $day }}" 
                                                        aria-expanded="false" aria-controls="dayCollapse{{ $weekNumber }}_{{ $day }}">
                                                        {{ ucfirst($day) }} ({{ count($dayGames) }} games)
                                                    </button>
                                                </h2>
                                                <div id="dayCollapse{{ $weekNumber }}_{{ $day }}" class="accordion-collapse collapse" 
                                                    aria-labelledby="dayHeading{{ $weekNumber }}_{{ $day }}" 
                                                    data-bs-parent="#dayAccordion{{ $weekNumber }}">
                                                    <div class="accordion-body">
                                                        
                                                        @php
                                                            // Group games by location
                                                            $gamesByLocation = [];
                                                            foreach ($dayGames as $game) {
                                                                $locationId = $game['location_id'] ?? 0;
                                                                $locationName = $game['location_name'] ?? 'Unknown Location';
                                                                if (!isset($gamesByLocation[$locationId])) {
                                                                    $gamesByLocation[$locationId] = [
                                                                        'name' => $locationName,
                                                                        'games' => []
                                                                    ];
                                                                }
                                                                $gamesByLocation[$locationId]['games'][] = $game;
                                                            }
                                                            // Sort by location id
                                                            ksort($gamesByLocation);
                                                        @endphp
                                                        
                                                        <div class="accordion" id="locationAccordion{{ $weekNumber }}_{{ $day }}">
                                                            @foreach($gamesByLocation as $locationId => $locationData)
                                                                <div class="accordion-item">
                                                                    <h2 class="accordion-header" id="locHeading{{ $weekNumber }}_{{ $day }}_{{ $locationId }}">
                                                                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" 
                                                                            data-bs-target="#locCollapse{{ $weekNumber }}_{{ $day }}_{{ $locationId }}" 
                                                                            aria-expanded="false" aria-controls="locCollapse{{ $weekNumber }}_{{ $day }}_{{ $locationId }}">
                                                                            {{ $locationData['name'] }} ({{ count($locationData['games']) }} games)
                                                                        </button>
                                                                    </h2>
                                                                    <div id="locCollapse{{ $weekNumber }}_{{ $day }}_{{ $locationId }}" class="accordion-collapse collapse" 
                                                                        aria-labelledby="locHeading{{ $weekNumber }}_{{ $day }}_{{ $locationId }}" 
                                                                        data-bs-parent="#locationAccordion{{ $weekNumber }}_{{ $day }}">
                                                                        <div class="accordion-body">
                                                                            <table class="table table-striped">
                                                                                <thead>
                                                                                    <tr>
                                                                                        <th>Time</th>
                                                                                        <th>Field</th>
                                                                                        <th>Division</th>
                                                                                        <th>Team 1</th>
                                                                                        <th>Team 2</th>
                                                                                        <th>Actions</th>
                                                                                    </tr>
                                                                                </thead>
                                                                                <tbody>
                                                                                    @foreach($locationData['games'] as $game)
                                                                                        <tr class="game-row" data-game-id="{{ $game['team1_id'] }}-{{ $game['team2_id'] }}-{{ $game['day'] }}-{{ $locationId }}">
                                                                                            <td>{{ $game['time'] }}</td>
                                                                                            <td>{{ $game['field'] }}</td>
                                                                                            <td>
                                                                                                <span class="badge bg-{{ isset($game['division']) && $game['division'] == 1 ? 'primary' : 'success' }}">
                                                                                                    Division {{ $game['division'] ?? '?' }}
                                                                                                </span>
                                                                                            </td>
                                                                                            <td>{{ $game['team1_name'] }}</td>
                                                                                            <td>{{ $game['team2_name'] }}</td>
                                                                                            <td>
                                                                                                <button type="button" class="btn btn-sm btn-primary edit-game-btn"
                                                                                                    data-team1-id="{{ $game['team1_id'] }}"
                                                                                                    data-team2-id="{{ $game['team2_id'] }}"
                                                                                                    data-team1-name="{{ $game['team1_name'] }}"
                                                                                                    data-team2-name="{{ $game['team2_name'] }}"
                                                                                                    data-day="{{ $game['day'] }}"
                                                                                                    data-time="{{ $game['time'] }}"
                                                                                                    data-location-id="{{ $game['location_id'] ?? 0 }}"
                                                                                                    data-location-name="{{ $game['location_name'] ?? 'Unknown' }}"
                                                                                                    data-field="{{ $game['field'] }}">
                                                                                                    Edit
                                                                                                </button>
                                                                                            </td>
                                                                                        </tr>
                                                                                    @endforeach
                                                                                </tbody>
                                                                            </table>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            @endforeach
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
        
        <!-- Team Statistics -->
        <div class="card mt-4">
            <div class="card-header">
                <h5>Team Statistics</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    @foreach($statistics as $team)
                        <div class="col-md-3 mb-3">
                            <div class="card h-100">
                                <div class="card-body">
                                    <h6 class="card-title">{{ $team['name'] }}</h6>
                                    <p class="card-text">
                                        Division: {{ $team['division'] ?? 'N/A' }}<br>
                                        Games: {{ $team['games'] }} / {{ $team['target_games'] }}
                                    </p>
                                    <div class="progress">
                                        <div class="progress-bar {{ $team['games'] >= $team['target_games'] ? 'bg-success' : 'bg-info' }}" 
                                            role="progressbar" style="width: {{ ($team['games'] / $team['target_games']) * 100 }}%;" 
                                            aria-valuenow="{{ $team['games'] }}" aria-valuemin="0" aria-valuemax="{{ $team['target_games'] }}">
                                            {{ $team['games'] }}/{{ $team['target_games'] }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    @endif
</div>

<!-- Edit Game Modal -->
<div class="modal fade" id="editGameModal" tabindex="-1" aria-labelledby="editGameModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editGameModalLabel">Edit Game</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="editGameForm">
                @csrf
                <div class="modal-body">
                    <!-- Team 1 Selection -->
                    <div class="mb-3">
                        <label for="team1_id" class="form-label">Team 1</label>
                        <select class="form-select" id="team1_id" name="team1_id" required>
                            @foreach(\App\Models\Team::all() as $team)
                                <option value="{{ $team[0] }}">{{ $team[1] }}</option>
                            @endforeach
                        </select>
                    </div>
                    
                    <!-- Team 2 Selection -->
                    <div class="mb-3">
                        <label for="team2_id" class="form-label">Team 2</label>
                        <select class="form-select" id="team2_id" name="team2_id" required>
                            @foreach(\App\Models\Team::all() as $team)
                                <option value="{{ $team[0] }}">{{ $team[1] }}</option>
                            @endforeach
                        </select>
                    </div>
                    
                    <!-- Day Selection -->
                    <div class="mb-3">
                        <label for="day" class="form-label">Day</label>
                        <select class="form-select" id="day" name="day" required>
                            <option value="monday">Monday</option>
                            <option value="tuesday">Tuesday</option>
                            <option value="wednesday">Wednesday</option>
                            <option value="thursday">Thursday</option>
                            <option value="friday">Friday</option>
                            <option value="saturday">Saturday</option>
                            <option value="sunday">Sunday</option>
                        </select>
                    </div>
                    
                    <!-- Location Selection -->
                    <div class="mb-3">
                        <label for="location_id" class="form-label">Location</label>
                        <select class="form-select" id="location_id" name="location_id" required>
                            @foreach(\App\Models\Location::all() as $location)
                                <option value="{{ $location[0] }}">{{ $location[1] }}</option>
                            @endforeach
                        </select>
                    </div>
                    
                    <!-- Time Input -->
                    <div class="mb-3">
                        <label for="time" class="form-label">Time</label>
                        <input type="time" class="form-control" id="time" name="time" required>
                    </div>
                    
                    <!-- Field Input -->
                    <div class="mb-3">
                        <label for="field" class="form-label">Field</label>
                        <input type="text" class="form-control" id="field" name="field" required>
                    </div>
                    
                    <!-- Hidden fields for original values -->
                    <input type="hidden" id="original_team1_id" name="original_team1_id">
                    <input type="hidden" id="original_team2_id" name="original_team2_id">
                    <input type="hidden" id="original_day" name="original_day">
                    <input type="hidden" id="original_location_id" name="original_location_id">
                    <input type="hidden" id="original_field" name="original_field">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save Changes</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Bootstrap JS Bundle with Popper -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Toggle location details based on checkbox state
    document.querySelectorAll('.location-toggle').forEach(function(toggle) {
        const locationId = toggle.id;
        const detailsDiv = document.querySelector('#' + locationId.replace('_enabled', '_details'));
        
        // Set initial state
        if (detailsDiv) {
            detailsDiv.style.display = toggle.checked ? 'block' : 'none';
        }
        
        // Add change event
        toggle.addEventListener('change', function() {
            if (detailsDiv) {
                detailsDiv.style.display = this.checked ? 'block' : 'none';
            }
        });
    });
    
    // Edit Game Modal
    const editBtns = document.querySelectorAll('.edit-game-btn');
    const editModal = new bootstrap.Modal(document.getElementById('editGameModal'));
    
    editBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            // Set form values from data attributes
            document.getElementById('team1_id').value = this.dataset.team1Id;
            document.getElementById('team2_id').value = this.dataset.team2Id;
            document.getElementById('day').value = this.dataset.day;
            document.getElementById('time').value = this.dataset.time;
            document.getElementById('location_id').value = this.dataset.locationId;
            document.getElementById('field').value = this.dataset.field;
            
            // Set original values for reference
            document.getElementById('original_team1_id').value = this.dataset.team1Id;
            document.getElementById('original_team2_id').value = this.dataset.team2Id;
            document.getElementById('original_day').value = this.dataset.day;
            document.getElementById('original_location_id').value = this.dataset.locationId;
            document.getElementById('original_field').value = this.dataset.field;
            
            // Show modal
            editModal.show();
        });
    });
    
    // Edit Game Form Submit
    document.getElementById('editGameForm').addEventListener('submit', function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        const gameRow = document.querySelector(`.game-row[data-game-id="${formData.get('original_team1_id')}-${formData.get('original_team2_id')}-${formData.get('original_day')}-${formData.get('original_location_id')}"]`);
        
        fetch('/edit-game', {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Close modal
                editModal.hide();
                
                // Update row in the table
                const cells = gameRow.querySelectorAll('td');
                cells[0].textContent = data.data.time;
                cells[1].textContent = data.data.field;
                cells[3].textContent = data.data.team1_name;
                cells[4].textContent = data.data.team2_name;
                
                // Update data attributes for future edits
                const editBtn = gameRow.querySelector('.edit-game-btn');
                editBtn.dataset.team1Id = formData.get('team1_id');
                editBtn.dataset.team2Id = formData.get('team2_id');
                editBtn.dataset.team1Name = data.data.team1_name;
                editBtn.dataset.team2Name = data.data.team2_name;
                editBtn.dataset.day = formData.get('day');
                editBtn.dataset.time = formData.get('time');
                editBtn.dataset.locationId = formData.get('location_id');
                editBtn.dataset.locationName = data.data.location_name;
                editBtn.dataset.field = formData.get('field');
                
                // Update game-row data-id
                gameRow.dataset.gameId = `${formData.get('team1_id')}-${formData.get('team2_id')}-${formData.get('day')}-${formData.get('location_id')}`;
                
                // Show success message
                alert('Game updated successfully!');
            } else {
                alert('Error: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred while updating the game.');
        });
    });
});
</script>
</body>
</html>