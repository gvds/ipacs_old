<?php

namespace App;

use Carbon\Carbon;
use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class subject extends Model
{
  protected $fillable = [
    'subjectID',
    'project_id',
    'site_id',
    'user_id',
    'arm_id',
    'firstname',
    'surname',
    'address1',
    'address2',
    'address3'
  ];

  public function events()
  {
    return $this->belongsToMany(event::class)
      ->withPivot('id', 'eventstatus_id', 'logDate', 'eventDate', 'minDate', 'maxDate', 'iteration', 'labelStatus')
      ->withTimestamps();
  }

  public function arm()
  {
    return $this->belongsTo(arm::class);
  }

  public function previous_arm()
  {
    return $this->belongsTo(arm::class, 'previous_arm_id');
  }

  public function arms()
  {
    return $this->belongsToMany(arm::class)->withTimestamps();
  }

  public function site()
  {
    return $this->belongsTo(site::class);
  }

  public function user()
  {
    return $this->belongsTo(User::class);
  }

  public function getFullNameAttribute()
  {
    return $this->firstname . ' ' . $this->surname;
  }

  public static function createSubjects($validatedData)
  {
    $user = auth()->user();
    $currentProject = \App\project::find($validatedData['currentProject']->id);
    $last_subject_id = $currentProject->last_subject_id ?: 0;
    $subject_id_prefix = $currentProject->subject_id_prefix;
    $subject_id_digits = $currentProject->subject_id_digits;
    try {
      DB::beginTransaction();
      for ($i = 0; $i < $validatedData['records']; $i++) {
        $last_subject_id = ++$last_subject_id;
        $subjectID = $subject_id_prefix . str_pad($last_subject_id, $subject_id_digits, '0', STR_PAD_LEFT);
        $subject = subject::create([
          'subjectID' => $subjectID,
          'project_id' => $validatedData['currentProject']->id,
          'site_id' => $user->projectSite,
          'user_id' => $user->id,
          'arm_id' => $validatedData['arm'],
        ]);
        // Add event entries for the subject's arm to the event_subject table
        $arm = $subject->arm()->with('events')->first();
        $response = $subject->createArmEvents($arm);
        if ($response !== true) {
          throw new \ErrorException("Events for $subject->subjectID could not be created : $response");
        }
      }
      $currentProject->last_subject_id = $last_subject_id;
      $currentProject->save();

      DB::commit();
    } catch (\Throwable $th) {
      DB::rollback();
      return $th->getMessage();
    }
    return 0;
  }

  public function enrol($validatedData)
  {
    $this->enrolDate = $validatedData['enrolDate'];
    $this->armBaselineDate = $validatedData['enrolDate'];
    $this->subject_status = 1;
    $this->firstname = $validatedData['firstname'];
    $this->surname = $validatedData['surname'];
    $this->address1 = $validatedData['address1'];
    $this->address2 = $validatedData['address2'];
    $this->address3 = $validatedData['address3'];
    $response = $this->save();
    if ($response !== true) {
      throw new \ErrorException("Subject $this->subjectID failed to enrol : $response");
    }
  }

  public function switchArm(Int $switchArm, $switchDate)
  {
    $this->previous_arm_id = $this->arm_id;
    $this->previousArmBaselineDate = $this->armBaselineDate;
    $this->armBaselineDate = $switchDate;
    $this->arm_id = $switchArm;
    $response = $this->save();
    if ($response !== true) {
      throw new \ErrorException("Subject $this->subjectID failed to switch : $response");
    }
    $this->arms()->attach($this->arm_id, ['armBaselineDate' => $switchDate, 'previous_arm_id' => $this->arm_id]);
  }

  public function cancelOutstandingEvents()
  {
    $this->events()
      ->whereNotIn('eventstatus_id', [3, 4, 5])
      ->update(['eventstatus_id' => 6]);
    $this->events()
      ->where('labelStatus', '1')
      ->update(['labelStatus' => 0]);
  }

  public function reverseArmSwitch()
  {
    $this->arms()->detach($this->arm_id);
    $this->arm_id = $this->previous_arm_id;
    $this->armBaselineDate = $this->previousArmBaselineDate;
    $this->previous_arm_id = null;
    $this->previousArmBaselineDate = null;
    $response = $this->save();
    if ($response !== true) {
      throw new \ErrorException("Subject $this->subjectID failed to reverse switch : $response");
    }
  }

  public function revertArmSwitchEvents($currentArm, $previousArm)
  {
    $response = $this->events()->detach($this->events()->where('arm_id', $currentArm)->pluck('events.id'));
    if ($response === 0) {
      throw new \ErrorException("Events for $this->subjectID could not be reverted");
    }
    try {
      $previousEvents = $this->events()->where('arm_id', $previousArm)->get();
      foreach ($previousEvents as $previousEvent) {
        if (Carbon::parse($previousEvent->pivot->maxDate) < Carbon::today()) {
          $evenstatus_id = 5;
        } else {
          $evenstatus_id = 0;
        }
        $this->events()
          ->wherePivot('eventstatus_id', 6)
          ->updateExistingPivot($previousEvent->id, ['eventstatus_id' => $evenstatus_id]);
      }
    } catch (\Throwable $th) {
      throw new \ErrorException("Previous arm events for $this->subjectID could not be restored");
    }
  }

  public function createArmEvents($arm)
  {
    foreach ($arm->events as $event) {
      if ($event->active) {
        if ($event->autolog == 1) {
          $labelStatus = 2;
        } elseif ($event->offset == 0){
          $labelStatus = 1;
        } else {
          $labelStatus = 0;
        }
        $response = $this->events()->attach($event->id, ['labelStatus' => $labelStatus]);
        if ($response) {
          return ($response);
        }
      }
    }
    return true;
  }


  public function setEventDates(Event $event, $baselineDate)
  {
    if ($event->active) {
      $timestamp = null;
      if ($event->autolog === 1) {
        $eventstatus = 3;
        $timestamp = now();
      } else {
        if ($event->offset === 0) {
          $eventstatus = 2;
        } else {
          $eventstatus = 0;
        }
      }
      $eventDate = Carbon::parse($baselineDate)->addDays($event->offset, 'days');
      $minDate = $eventDate->copy()->subDays($event->offset_ante_window, 'days');
      $maxDate = $eventDate->copy()->addDays($event->offset_post_window, 'days');
      $response = $this->events()->updateExistingPivot($event->id, [
        'eventstatus_id' => $eventstatus,
        'eventDate' => $eventDate,
        'minDate' => $minDate,
        'maxDate' => $maxDate,
        'logDate' => $timestamp
        ]);
      if ($response === 1) {
        return true;
      } else {
        return ($response);
      }
    }
  }

  private function curl(array $params, array $data = [])
  {
    $user = Auth::user()->teams()->where('teams.id', session('currentProject'))->first();
    $token = $user->pivot->redcap_api_token;
    if (empty($token)) {
      throw new Exception('You do not have a REDCap API token in your ' . config('app.name') . ' project account');
    }

    $fields = array(
      'token'   => $token,
      'format'  => 'json',
      'type'    => 'flat',
      'returnFormat' => 'json',
      'data'    => json_encode([$data])
    );

    $fields = array_merge($fields, $params);

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, config('services.redcap.url'));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_VERBOSE, 0);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_AUTOREFERER, true);
    curl_setopt($ch, CURLOPT_MAXREDIRS, 10);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
    curl_setopt($ch, CURLOPT_FRESH_CONNECT, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($fields, '', '&'));
    return curl_exec($ch);
  }

  public function createREDCapRecord()
  {
    $arm_num = arm::find($this->arm_id)->arm_num; // Have to do it this way because changes are not reflected in relations
    $params = [
      'content' => 'event',
      'arms' => [$arm_num]
    ];
    $events = $this->curl($params);
    $events = json_decode($events, true);
    if (array_key_exists('error', $events)) {
      throw new Exception('REDCap Error: ' . $events['error']);
    }
    $event_name = $events[0]['unique_event_name'];

    $dag = strtolower(auth()->user()->currentSite[0]->name);

    // New SubjectID for REDCap database
    $params = [
      'content' => 'record',
    ];
    $data = [
      'record_id' => $this->subjectID,
      'redcap_event_name' => $event_name,
      // 'redcap_data_access_group' => $dag,
    ];
    $response = $this->curl($params, $data);
    $returnmsg = json_decode($response, true);
    if (array_key_exists("error", $returnmsg)) {
      throw new Exception($returnmsg['error']);
    } elseif ($returnmsg['count'] === 0) {
      throw new Exception('REDCap record was not created');
    }
  }

  // public function addRepeatingEvent(event_subject $event_subject, int $instance)
  // {
  //   $event = event::where('id',$event_subject->event_id)->with('arm')->first();

  //   $redcap_event = DB::connection('redcap')->select("select * from redcap_events_metadata where event_id = $event->redcap_event_id");

  //   $unique_event_name = str_replace(' ','_',strtolower($redcap_event[0]->descrip)) . '_arm' . '_' . $event->arm->arm_num;

  //   $params = [
  //     'content' => 'record',
  //   ];
  //   $data = [
  //     'record_id' => $this->subjectID,
  //     'unscheduled_visit_complete' => 0,
  //     'redcap_event_name' => $unique_event_name,
  //     'redcap_repeat_instance' => $instance,
  //   ];
  //   // dd($data);
  //   $response = $this->curl($params, $data);
  //   $returnmsg = json_decode($response, true);
  //   if (array_key_exists("error", $returnmsg)) {
  //     throw new Exception($returnmsg['error']);
  //   } elseif ($returnmsg['count'] === 0) {
  //     throw new Exception('REDCap record was not created');
  //   }
  // }

  public function checkAccessPermission()
  {
    if ($this->user_id === auth()->user()->id or auth()->user()->hasRole('sysadmin')) {
      return true;
    }
    return false;
  }

}
