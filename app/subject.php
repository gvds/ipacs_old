<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use DB;

class subject extends Model
{
  protected $fillable = [
    'subjectID',
    'project_id',
    'site_id',
    'user_id',
    'arm_id'
  ];

  public function events()
  {
    return $this->belongsToMany(event::class)
      ->withPivot('eventstatus_id', 'reg_timestamp', 'log_timestamp', 'eventDate', 'minDate', 'maxDate', 'itteration')
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
        subject::create([
          'subjectID' => $subjectID,
          'project_id' => $validatedData['currentProject']->id,
          'site_id' => $user->projectSite($validatedData['currentProject']->id),
          'user_id' => $user->id,
          'arm_id' => $validatedData['arm'],
        ]);
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

  public function enrol($enrolDate)
  {
    $this->enrolDate = $enrolDate;
    $this->armBaselineDate = $enrolDate;
    $this->subject_status = 1;
    return $this->save();
  }

  public function switchArm(Int $switchArm, $switchDate)
  {
    $this->previous_arm_id = $this->arm_id;
    $this->previousArmBaselineDate = $this->armBaselineDate;
    $this->armBaselineDate = $switchDate;
    $this->arm_id = $switchArm;
    return $this->save();
  }

  public function reverseArmSwitch()
  {
    $this->arm_id = $this->previous_arm_id;
    $this->armBaselineDate = $this->previousArmBaselineDate;
    $this->previous_arm_id = null;
    $this->previousArmBaselineDate = null;
    return $this->save();
  }

  public function revertArmSwitchEvents($currentArm, $previousArm)
  {
    $response = $this->events()->detach($this->events()->where('arm_id', $currentArm)->pluck('id'));
    if ($response === 0) {
      return (false);
    }
    $previousEvents = $this->events()->where('arm_id', $previousArm)->get();
    foreach ($previousEvents as $key => $previousEvent) {
      $response = $this->events()->wherePivot('eventstatus_id', 6)->updateExistingPivot($previousEvent->id, ['eventstatus_id' => 0]);
    }
    return true;
  }

  public function createArmEvents($arm, $baselineDate)
  {
    $existingEvents = $this->events()->get();
    foreach ($existingEvents as $key => $existingEvent) {
      if ($existingEvent->pivot->eventstatus_id === 0) {
        $event = \App\event::find($existingEvent->id);
        $this->events()->wherePivot('eventstatus_id', 0)->updateExistingPivot($existingEvent->id, ['eventstatus_id' => 6]);
      }
    }
    foreach ($arm->events as $key => $event) {
      if ($event->active) {
        $timestamp = null;
        if ($event->autolog === 1) {
          $eventstatus = 3;
          $timestamp = now();
        } else {
          if ($event->offset === 0) {
            if ($arm->arm_num === 0) {
              $eventstatus = 3;
              $timestamp = now();
            } else {
              $eventstatus = 1;
            }
          } else {
            $eventstatus = 0;
          }
        }
        $eventDate = Carbon::parse($baselineDate)->addDays($event->offset, 'days');
        $minDate = $eventDate->copy()->subDays($event->offset_ante_window, 'days');
        $maxDate = $eventDate->copy()->addDays($event->offset_post_window, 'days');
        $response = $this->events()->attach($event->id, [
          'eventstatus_id' => $eventstatus,
          'eventDate' => $eventDate,
          'minDate' => $minDate,
          'maxDate' => $maxDate,
          'reg_timestamp' => $timestamp,
          'log_timestamp' => $timestamp
        ]);
        if ($response) {
          return ($response);
        }
      }
    }
    return true;
  }
}
