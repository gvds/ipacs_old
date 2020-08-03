<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use DB;

class subject extends Model
{
  protected $fillable = [
    'subjectID',
    'project_id',
    'site',
    'user_id',
    'arm_id'
  ];


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
          'site' => $user->projectSite($validatedData['currentProject']->id),
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
}
