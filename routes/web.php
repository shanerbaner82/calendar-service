<?php

use App\Services\Calendar\CalendarService;
use Illuminate\Support\Carbon;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::get('/busy', function () {
    $startDate = Carbon::now()->startOfDay();
    $endDate = Carbon::now()->endOfDay();
    $busyTimes = collect(CalendarService::getCalendarBusyTimes($startDate, $endDate));
    return $busyTimes;
});

Route::get('/free', function () {
    $startDate = Carbon::now()->startOfDay();
    $endDate = Carbon::now()->endOfDay();
    $freeTimes = CalendarService::freeTimes($startDate, $endDate);

    return $freeTimes;
});
