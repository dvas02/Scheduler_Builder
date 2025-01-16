<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

/*Route::get('/scheduler', function () {
    return view('scheduler-page');
});*/

Route::get('/scheduler', [App\Http\Controllers\ScheduleController::class, 'index']);
//Route::post('/generate-schedule', [App\Http\Controllers\ScheduleController::class, 'generateSchedule']);
Route::post('/scheduler', [App\Http\Controllers\ScheduleController::class, 'generateSchedule']);

Route::post('/edit-game', [App\Http\Controllers\EditGameButtonController::class, 'editGameHandler'])->name('editGame');

