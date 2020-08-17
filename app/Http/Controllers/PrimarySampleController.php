<?php

namespace App\Http\Controllers;

use App\event_subject;
use App\eventSubject_sample;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PrimarySampleController extends Controller
{

    public function primary(Request $request)
    {
        $validatedData = $request->validate([
            'pse' => 'required|regex:/^\d+_([A-Za-z0-9]+)_\d+$/'
        ]);
        try {
            list($project_id, $subjectID, $id) = explode('_', $validatedData['pse']);
            if ($project_id != session('currentProject')) {
                throw new Exception("This barcode does not belong to the current project", 1);
            }
            $event_subject = \App\event_subject::where('id', $id)->firstOr(function () {
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
            if ($event_subject->eventstatus_id === 5) {
                throw new Exception("This event has been missed");
            }
            if ($event_subject->eventstatus_id === 6) {
                throw new Exception("This event has been cancelled");
            }

            $sampletypes = \App\sample::with(['event_samples' => function ($query) use ($event_subject) {
                $query->where('event_subject_id', $event_subject->id);
            }])
                ->where('project_id', $project_id)
                ->where('primary', true)
                ->where('active', true)
                ->orderBy('sampleGroup')
                ->orderBy('name')
                ->get();
        } catch (\Throwable $th) {
            return back()->with('error', $th->getMessage());
        }
        $samplestatuses = \App\sampleStatus::pluck('samplestatus', 'id');
        return view('primarysamples.register', compact('sampletypes', 'samplestatuses', 'id'));
    }

    public function registerprimary(Request $request)
    {
        $rules = [
            'event_subject_id' => 'required|integer|exists:event_subject,id',
            'log' => 'required|boolean',
            'type' => 'required|array',
            'vol' => 'array',
            'aliquot' => 'array',
            'type.*.*' => [
                'nullable',
                'regex:/^[A-Z]{0,6}\d{3,8}$/',
                'distinct',
                'unique:event_sample,barcode'
            ],
            'vol.*.*' => 'required|numeric',
            'aliquot.*.*' => 'required|integer|min:1'
        ];
        $messages = [
            'type.required' => 'All samples have already been recorded',
        ];
        $attributes = [
            'type.*.*' => "Barcode ':input'",
            'vol.*.*' => "Volume :value"
        ];
        $validatedData = Validator::make($request->all(), $rules, $messages, $attributes)->validate();

        $user = auth()->user();
        $records = 0;
        if (isset($validatedData['type'])) {
            foreach ($validatedData['type'] as $sample_id => $barcodes) {
                if (count($barcodes) > 0) {
                    foreach ($barcodes as $number => $barcode) {
                        if ($barcode != null) {
                            $sample = new eventSubject_sample;
                            $sample->sample_id = $sample_id;
                            $sample->event_subject_id = $validatedData['event_subject_id'];
                            $sample->barcode = $barcode;
                            $sample->volume = $validatedData['vol'][$sample_id][$number];
                            $sample->site = $user->projectSite;
                            if ($validatedData['log'] == 1) {
                                $sample->loggedBy = $user->id;
                                $sample->logTime = now();
                            }
                            $sample->samplestatus_id = $validatedData['log'] + 1;
                            $sample->aliquot = $validatedData['aliquot'][$sample_id][$number];
                            $sample->save();
                            $records++;
                        }
                    }
                }
            }
        }
        return back()->withInput($request->input())->with('message', "$records samples registered");
    }

    public function primarylogging(Request $request)
    {
        $validatedData = $request->validate([
            'pse' => 'required|regex:/^\d+_([A-Za-z0-9]+)_\d+$/'
        ]);
        try {
            list($project_id, $subjectID, $id) = explode('_', $validatedData['pse']);
            if ($project_id != session('currentProject')) {
                throw new Exception("This barcode does not belong to the current project", 1);
            }
            $event_subject = \App\event_subject::where('id', $id)->firstOr(function () {
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
            if ($event_subject->eventstatus_id === 5) {
                throw new Exception("This event has been missed");
            }
            if ($event_subject->eventstatus_id === 6) {
                throw new Exception("This event has been cancelled");
            }

            $samples = \App\sample::join('event_sample', 'samples.id', '=', 'sample_id')
                ->where('event_subject_id', $event_subject->id)
                ->where('project_id', $project_id)
                ->where('primary', true)
                ->where('active', true)
                ->orderBy('sampleGroup')
                ->orderBy('name')
                ->get();
        } catch (\Throwable $th) {
            return redirect()->back()->with('error', $th->getMessage());
        }
        $samplestatuses = \App\sampleStatus::pluck('samplestatus', 'id');
        return view('primarysamples.log', compact('samples', 'samplestatuses', 'event_subject'));
    }

    public function log(Request $request)
    {
        $validatedData = $request->validate([
            'event_subject_id' => 'required|integer|exists:event_subject,id',
            'barcode' => 'required|regex:/^[A-Z]{0,6}\d{3,8}$/|exists:event_sample,barcode'
        ]);
        try {
            $subject = event_subject::find($validatedData['event_subject_id'])->subject;
            if ($subject->project_id !== session('currentProject')) {
                throw new Exception("This barcode does not belong to the current project");
            }
            if ($subject->user_id !== auth()->user()->id) {
                throw new Exception("You do not have permission to access this subject's record");
            }
            $eventSubject_sample = eventSubject_sample::where('barcode', $validatedData['barcode'])
                ->where('event_subject_id', $validatedData['event_subject_id'])
                ->first();
            if (is_null($eventSubject_sample)) {
                throw new Exception("This barcode does not exist in this event", 1);
            }
            if (!$eventSubject_sample->sample->primary) {
                throw new Exception("This is not a primary sample");
            }
            switch ($eventSubject_sample->samplestatus_id) {
                case 0:
                    throw new Exception("This sample had not been registered");
                    break;
                case 1:
                    break;
                default:
                    throw new Exception("Sample " . $validatedData['barcode'] . " has already been logged");
                    break;
            }
            $eventSubject_sample->samplestatus_id = 2;
            $eventSubject_sample->save();
        } catch (\Throwable $th) {
            return back()->withErrors($th->getMessage());
        }
        return back();
    }
}
