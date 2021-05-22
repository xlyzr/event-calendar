<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Event;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class EventController extends Controller
{
    //
    
    public function getEvents(Request $request)
    {;
        $dateFrom = $request->get('dateFrom');
        $dateTo = $request->get('dateTo');
        $daysLength = Carbon::parse($dateFrom)->diff($dateTo)->days;
        for($i=0; $i <= $daysLength; $i++)
        {
            $events = DB::table('events')->select('name', 'event_date')->where('event_date', Carbon::parse($dateFrom)->addDay($i)->format('Y-m-d'))->first();
            $dates[] = $events;
        }
        // $events = DB::table('events')->select('name', 'event_date')->whereBetween('event_date', [$dateFrom, $dateTo])->get();
        return $dates;
    }

    public function saveEvents(Request $request)
    {
        DB::table('events')->truncate();
        $input = $request->all();

        $difference = Carbon::parse($input['dateFrom'])->diff($input['dateTo'])->days;
        for($i = 0; $i <= $difference; $i++) {
            $event = new Event;
            $currentDate = Carbon::parse($input['dateFrom'])->addDay($i);
            $currentDate = Carbon::parse($currentDate)->toDateString();
            if($this->checkWeekDay($currentDate, $input['checkDays']))
            {
                $event->name =  $input['name'];
                $event->event_date = $currentDate; 
                $event->save();
            }
        }
        return response()->json([
            "data" => $input,
            "diff" => $difference,
            "weekday" => Carbon::parse($input['dateFrom'])->dayOfWeek
        ]);
    }

    public function checkWeekDay($date, $checkDays)
    {
        $hasSelectedDays = count($checkDays) > 0 ? true : false;
        if($hasSelectedDays) {
            for($i = 0; $i < count($checkDays); $i++) {
                if($checkDays[$i] == Carbon::parse($date)->dayOfWeek) {
                    return true;
                }
            }
            if($i == count($checkDays)) {
                return false;
            }
        } else {
            return true;
        }
    }
}
