<?php

namespace App\Http\Controllers;

use App\event_subject;
use App\event_sample;
use App\Rules\BarcodeFormat;
use Carbon\Carbon;
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
            list($project_id, $subjectID, $event_subject_id) = explode('_', $validatedData['pse']);
            if ($project_id != session('currentProject')) {
                throw new Exception("This barcode does not belong to the current project", 1);
            }
            $event_subject = \App\event_subject::where('id', $event_subject_id)->firstOr(function () {
                throw new Exception("The event record could not be found");
            });
            $subject = \App\subject::where('id', $event_subject->subject_id)->first();
            if ($subjectID !== $subject->subjectID) {
                throw new Exception('Invalid PSE: The subject ID does not match the event record');
            }
            switch ($subject->subject_status) {
                case 0:
                    throw new Exception("This subject has not yet been enroled");
                    break;
                case 2:
                    throw new Exception("This subject has been dropped");
                    break;
            }
            switch ($event_subject->eventstatus_id) {
                case 0:
                case 1:
                    throw new Exception("This event has not yet been scheduled");
                    break;
                case 5:
                    throw new Exception("This event has been missed");
                    break;
                case 6:
                    throw new Exception("This event has been cancelled");
                    break;
            }

            $sampletypes = \App\sampletype::with(['event_samples' => function ($query) use ($event_subject) {
                $query->where('event_subject_id', $event_subject->id);
            }])
                ->where('project_id', $project_id)
                ->where('primary', true)
                ->where('active', true)
                ->orderBy('sampleGroup')
                ->orderBy('name')
                ->get();
            $maxaliquots = 1;
            foreach ($sampletypes as $sampletype) {
                $maxaliquots = max($maxaliquots, $sampletype->aliquots);
            }
        } catch (\Throwable $th) {
            return back()->with('error', $th->getMessage());
        }
        $samplestatuses = \App\sampleStatus::pluck('samplestatus', 'id');
        return view('primarysamples.register', compact('sampletypes', 'samplestatuses', 'event_subject_id', 'maxaliquots'));
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
                new BarcodeFormat,
                // 'regex:/^[A-Z]{0,6}\d{3,8}$/',
                'distinct',
                'unique:event_sample,barcode'
            ],
            // 'vol.*.*' => 'required_with:type.*.*|numeric',
            'vol.*.*' => 'required_with:type.*.*',
            'aliquot.*.*' => 'required_with:type.*.*|integer|min:1'
        ];
        $messages = [
            'type.required' => 'All samples have already been recorded',
        ];
        $attributes = [
            'type.*.*' => "Barcode ':input'",
            'vol.*.*' => "Volume :input"
        ];
        $validatedData = Validator::make($request->all(), $rules, $messages, $attributes)->validate();

        // Log event if it is not currently logged
        $event_subject = \App\event_subject::findOrFail($validatedData['event_subject_id']);
        if ($event_subject->eventstatus_id === 2) {
            if (Carbon::today() > Carbon::parse($event_subject->maxDate)) {
                $event_subject->eventstatus_id = 4; // late
            } else {
                $event_subject->eventstatus_id = 3;
            }
            $event_subject->save();
        }

        $user = auth()->user();
        $records = 0;
        if (isset($validatedData['type'])) {
            foreach ($validatedData['type'] as $sampletype_id => $barcodes) {
                if (count($barcodes) > 0) {
                    foreach ($barcodes as $number => $barcode) {
                        if ($barcode != null) {
                            $sample = new event_sample;
                            $sample->sampletype_id = $sampletype_id;
                            $sample->event_subject_id = $validatedData['event_subject_id'];
                            $sample->barcode = $barcode;
                            $sample->volume = $validatedData['vol'][$sampletype_id][$number];
                            $sample->site_id = $user->projectSite;
                            if ($validatedData['log'] == 1) {
                                $sample->loggedBy = $user->id;
                                $sample->logTime = now();
                                $sample->samplestatus_id = 2;
                            } else {
                                $sample->samplestatus_id = 1;
                            }
                            $sample->aliquot = $validatedData['aliquot'][$sampletype_id][$number];
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
            list($project_id, $subjectID, $event_subject_id) = explode('_', $validatedData['pse']);
            if ($project_id != session('currentProject')) {
                throw new Exception("This barcode does not belong to the current project", 1);
            }
            $event_subject = \App\event_subject::where('id', $event_subject_id)->firstOr(function () {
                throw new Exception("The event record could not be found");
            });
            $subject = \App\subject::where('id', $event_subject->subject_id)->first();
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
            }
            switch ($event_subject->eventstatus_id) {
                case 0:
                case 1:
                    throw new Exception("This event has not yet been scheduled");
                    break;
                case 5:
                    throw new Exception("This event has been missed");
                    break;
                case 6:
                    throw new Exception("This event has been cancelled");
                    break;
            }

            $sampletypes = \App\sampletype::join('event_sample', 'sampletypes.id', '=', 'sampletype_id')
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
        return view('primarysamples.log', compact('sampletypes', 'samplestatuses', 'event_subject'));
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
            // if ($subject->user_id !== auth()->user()->id) {
            //     throw new Exception("You do not have permission to access this subject's record");
            // }
            $event_sample = event_sample::where('barcode', $validatedData['barcode'])
                ->where('event_subject_id', $validatedData['event_subject_id'])
                ->first();
            if (is_null($event_sample)) {
                throw new Exception("This barcode does not exist in this event", 1);
            }
            if (!$event_sample->sampletype->primary) {
                throw new Exception("This is not a primary sample");
            }
            switch ($event_sample->samplestatus_id) {
                case 0:
                    throw new Exception("This sample had not been registered");
                    break;
                case 1:
                    break;
                default:
                    throw new Exception("Sample " . $validatedData['barcode'] . " has already been logged");
                    break;
            }
            $event_sample->samplestatus_id = 2;
            $user = auth()->user();
            $event_sample->loggedBy = $user->id;
            $event_sample->logTime = now();
            $event_sample->save();
        } catch (\Throwable $th) {
            return back()->withErrors($th->getMessage());
        }
        return back();
    }
}
