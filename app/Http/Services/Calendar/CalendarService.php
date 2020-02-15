<?php

namespace App\Services\Calendar;

use Carbon\Carbon;
use Carbon\CarbonPeriod;


class CalendarService
{
    /**
     * Returns a collection of Calendar Busy times.
     *
     * @param Carbon $startDate
     * @param Carbon $endDate
     * @return array
     */
    public static function getCalendarBusyTimes(Carbon $startDate, Carbon $endDate)
    {
        $timezone = CalendarService::getCalendarTimezone();
        $period = CarbonPeriod::since($startDate->startOfHour())->hours(1)->until($endDate);
        $hoursBusy = [8,9,12,14,16];

        $dates = [];

        foreach ($period as $date) {

            $isBusy = array_search($date->hour, $hoursBusy);

            if($isBusy > -1) {
                $dates[] = [
                    'start_date' => $date,
                    'end_date' => $date->copy()->addHour()
                ];
            }
        }

        return $dates;
    }

    public static function getWorkingHours(Carbon $startDate, Carbon $endDate)
    {
        $timezone = CalendarService::getCalendarTimezone();
        $period = CarbonPeriod::since($startDate->startOfHour())->hours(1)->until($endDate);
        $hoursWorking = [8,9,10,11,12,13,14,15,16,17,18,19,20];

        $dates = [];

        foreach ($period as $date) {

            $isWorking = array_search($date->hour, $hoursWorking);

            if($isWorking > -1) {
                $dates[] = [
                    'start_date' => $date,
                    'end_date' => $date->copy()->addHour()
                ];
            }
        }

        return $dates;
    }


    /**
     * @param Carbon $startDate
     * @param Carbon $endDate
     * @return \Illuminate\Support\Collection
     */
    public static function freeTimes(Carbon $startDate, Carbon $endDate)
    {
        $busyTimes = collect(self::getCalendarBusyTimes($startDate, $endDate));
        $workingTimes = collect(self::getWorkingHours($startDate, $endDate));

        $freeTimes = $workingTimes->reject(function ($workingTime) use($busyTimes){
            return self::timeIsInPeriod($workingTime['start_date'], $workingTime['end_date'], $busyTimes);
        });

        return $freeTimes->values();

    }

    public static function timeIsInPeriod(Carbon $startDate, Carbon $endDate, $busyTimes)
    {
        foreach($busyTimes as $bt){
            if($bt['start_date']->equalTo($startDate) && $bt['end_date']->equalTo($endDate)){
                return true;
            }
        }
        return false;
    }

    /**
     * Returns the Calendar Timezone setting.
     *
     * @return String
     */
    public static function getCalendarTimezone(){
        return 'America/Los_Angeles';
    }
}
