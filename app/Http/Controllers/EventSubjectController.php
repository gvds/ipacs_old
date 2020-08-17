<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\event_subject;
use Carbon\Carbon;
use Exception;

class EventSubjectController extends Controller
{
    public function index()
    {
        return view('event_subject.show');
    }

    public function show(Request $request)
    {
        $validatedData = $request->validate([
            'pse' => 'required|regex:/^\d+_([A-Za-z0-9]+)_\d+$/'
        ]);
        try {
            list($project_id, $subjectID, $id) = explode('_', $validatedData['pse']);
            if ($project_id != session('currentProject')) {
                throw new Exception("This barcode does not belong to the current project", 1);
            }
            $event_subject = event_subject::where('id', $id)->firstOr(function () {
                throw new Exception("The event record could not be found");
            });
            $subject = \App\subject::where('id', $event_subject->subject_id)->first();
            if ($subject->user_id !== auth()->user()->id) {
                throw new Exception("You do not have permission to access this subject's record", 1);
            }
            if ($subjectID != $subject->subjectID) {
                throw new Exception('Invalid PSE: The subject ID does not match the event record');
            }
            switch ($subject->subject_status) {
                case 0:
                    throw new Exception("This subject has not yet been enroled");
                    break;
                case 2:
                    throw new Exception("This subject has been dropped");
                    break;
                default:
                    break;
            }
            if ($event_subject->eventstatus_id < 2) {
                throw new Exception("This event has not yet been scheduled");
            }
            if ($event_subject->eventstatus_id == 4) {
                $statuscriteria = [3];
            }
            if ($event_subject->samplecount == '') {
                $statuscriteria = [2, 3, 5, 6];
            } else {
                $statuscriteria = [3];
            }
        } catch (\Throwable $th) {
            return redirect()->back()->with('error', $th->getMessage());
        }
        $eventstatuses = \App\eventStatus::whereIn('id', $statuscriteria)->pluck('eventstatus', 'id');
        return view('event_subject.show', compact('event_subject', 'eventstatuses'));
    }

    public function update(Request $request, event_subject $event_subject)
    {
        $validatedData = $request->validate([
            'logdate' => 'required|date|before_or_equal:' . today(),
            'eventstatus' => 'required|in:2,3,5,6'
        ]);
        try {
            event_subject::findOrFail($event_subject->id);
            $subject = \App\subject::where('id', $event_subject->subject_id)->first();
            if ($subject->project_id !== session('currentProject')) {
                throw new Exception("This event does not belong to the current project", 1);
            }
            if ($subject->user_id !== auth()->user()->id) {
                throw new Exception("You do not have permission to access this subject's record", 1);
            }
            switch ($subject->subject_status) {
                case 0:
                    throw new Exception("This subject has not yet been enroled");
                    break;
                case 2:
                    throw new Exception("This subject has been dropped");
                    break;
                default:
                    break;
            }
        } catch (\Throwable $th) {
            return redirect()->back()->with('error', $th->getMessage());
        }
        if ($validatedData['eventstatus'] == 3 & Carbon::parse($validatedData['logdate']) > Carbon::parse($event_subject->maxDate)) {
            $validatedData['eventstatus'] = 4;
        }
        $event_subject->eventstatus_id = $validatedData['eventstatus'];
        $event_subject->logDate = in_array($validatedData['eventstatus'], [3, 4, 5]) ? $validatedData['logdate'] : null;
        $event_subject->save();
        $pse = session('currentProject') . "_" . $event_subject->subject->subjectID . '_' . $event_subject->id;
        return redirect()->action(
            'EventSubjectController@show',
            ['pse' => $pse]
        )->with('message', 'Event status updated');
    }

}
