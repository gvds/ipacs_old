<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\event_sample;
use App\sample;
use Exception;
use Illuminate\Support\Facades\Validator;

class DerivativeSampleController extends Controller
{
    public function primaries(Request $request)
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
                $query->where('event_subject_id', $event_subject->id)
                    ->whereIn('samplestatus_id', [2, 3, 9]);
            }])
                ->where('project_id', $project_id)
                ->where('primary', true)
                ->orderBy('sampleGroup')
                ->orderBy('name')
                ->get();
            // dd($sampletypes);
        } catch (\Throwable $th) {
            return back()->with('error', $th->getMessage());
        }
        return view('derivativesamples.pse', compact('sampletypes', 'event_subject_id'));
    }

    public function parent(Request $request)
    {
        $validatedData = $request->validate([
            'parent' => 'required|exists:event_sample,barcode'
        ]);
        $parentsampletype = event_sample::join('samples', 'sample_id', '=', 'samples.id')
            ->select('event_sample.id')
            ->where('barcode', $validatedData['parent'])
            ->where('project_id', session('currentProject'))
            ->first();
        return redirect()->action(
            'DerivativeSampleController@retrieve',
            ['event_sample' => $parentsampletype->id]
        );
    }

    public function retrieve(event_sample $event_sample)
    {
        $parentsampletype = sample::find($event_sample->sample_id);

        $sampletypes = sample::with(['event_samples' => function ($query) use ($event_sample) {
            $query->where('event_subject_id', $event_sample->event_subject_id);
        }])
            ->where('parentSampleType_id', $parentsampletype->id)
            ->where('project_id', session('currentProject'))
            ->where('active', true)
            ->orderBy('sampleGroup')
            ->orderBy('name')
            ->get();
        return view('derivativesamples.log', compact('sampletypes', 'event_sample'));
        dd($sampletypes);
    }

    public function log(Request $request)
    {
        $rules = [
            'event_subject_id' => 'required|integer|exists:event_subject,id',
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
                            $sample = new event_sample;
                            $sample->sample_id = $sample_id;
                            $sample->event_subject_id = $validatedData['event_subject_id'];
                            $sample->barcode = $barcode;
                            $sample->volume = $validatedData['vol'][$sample_id][$number];
                            $sample->site = $user->projectSite;
                            $sample->loggedBy = $user->id;
                            $sample->logTime = now();
                            $sample->samplestatus_id = 2;
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
}
