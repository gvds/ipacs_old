<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\event_subject;
use Carbon\Carbon;

class EventSubjectController extends Controller
{
    public function index()
    {
        $records = event_subject::where('labelStatus', '1')
            ->join('subjects', 'subject_id', 'subjects.id')
            ->join('events', 'event_id', 'events.id')
            ->where('project_id', session('currentProject'))
            ->where('user_id',auth()->user()->id)
            ->where('active', true)
            ->get();
        dd($records);
    }

    public function addEventsToLabelQueue($thresholdDate = null)
    {
        $thresholdDate = Carbon::parse('friday last week');
        $records = event_subject::where('labelStatus', '0')
            ->join('events', 'event_id', 'events.id')
            ->join('arms', 'arm_id', 'arms.id')
            ->where('project_id', session('currentProject'))
            ->where('minDate', "<=", $thresholdDate)
            ->where('active', true)
            ->update(['labelStatus' => 1]);
        return redirect('/')->with('message', "$records events added to the label queue");
    }
}
