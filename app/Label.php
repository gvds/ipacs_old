<?php

namespace App;

use Carbon\Carbon;

class Label
{
    
  public static function addEventsToLabelQueue($thresholdDate = null)
  {
      $thresholdDate = Carbon::parse('next friday');
      $records = event_subject::whereIn('eventstatus_id',[0,1,2])
          ->where('labelStatus', '0')
          ->join('events', 'event_id', 'events.id')
          ->join('arms', 'arm_id', 'arms.id')
          ->where('project_id', session('currentProject'))
          ->whereNotNull('eventDate')
          ->where('minDate', "<=", $thresholdDate)
          ->where('active', true)
          ->update(['labelStatus' => 1]);
          return $records;
  }

}
