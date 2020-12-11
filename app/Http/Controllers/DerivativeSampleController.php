<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\event_sample;
use App\Rules\BarcodeFormat;
use App\sampletype;
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

            $sampletypes = sampletype::with(['event_samples' => function ($query) use ($event_subject) {
                $query->where('event_subject_id', $event_subject->id)
                    ->whereIn('samplestatus_id', [2, 3, 9]);
            }])
                ->where('project_id', $project_id)
                ->where('primary', true)
                ->orderBy('sampleGroup')
                ->orderBy('name')
            ->get();
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
        $parentsample = event_sample::join('sampletypes', 'sampletype_id', '=', 'sampletypes.id')
            ->select('event_sample.id')
            ->where('barcode', $validatedData['parent'])
            ->where('project_id', session('currentProject'))
            ->first();
        if (is_null($parentsample)) {
            return back()->withErrors('Sample barcode ' . $validatedData['parent'] . ' does not exist in this project');
        }
        return redirect()->action(
            'DerivativeSampleController@retrieve',
            ['event_sample' => $parentsample->id]
        );
    }

    public function retrieve(event_sample $event_sample)
    {
        $subject = $event_sample->event_subject->subject;

        if ($subject->site_id !== auth()->user()->project_site) {
            return back()->withErrors('This sample does not belong to your site');
        }
        $parentsampletype = sampletype::find($event_sample->sampletype_id);

        $sampletypes = sampletype::with(['event_samples' => function ($query) use ($event_sample) {
            $query->where('event_subject_id', $event_sample->event_subject_id);
        }])
            ->where('parentSampleType_id', $parentsampletype->id)
            ->where('project_id', session('currentProject'))
            ->where('active', true)
            ->orderBy('sampleGroup')
            ->orderBy('name')
            ->get();
        $parent_sample = $event_sample;
        return view('derivativesamples.log', compact('sampletypes', 'parent_sample'));
    }

    public function log(Request $request)
    {
        $rules = [
            'event_subject_id' => 'required|integer|exists:event_subject,id',
            'parent_sample_id' => 'required|integer|exists:event_sample,id',
            'type' => ['required', 'array'],
            'vol' => 'array',
            'aliquot' => 'array',
            'type.*.*' => [
                'nullable',
                new BarcodeFormat,
                // 'regex:/^[A-Z]{0,6}\d{3,12}$/',
                'distinct',
                'unique:event_sample,barcode',
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
        $parent_sample = event_sample::find($validatedData['parent_sample_id']);
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
                            $sample->loggedBy = $user->id;
                            $sample->logTime = now();
                            $sample->samplestatus_id = 2;
                            $sample->aliquot = $validatedData['aliquot'][$sampletype_id][$number];
                            $sample->parentBarcode = $parent_sample->barcode;
                            $sample->save();
                            $records++;
                        }
                    }
                }
            }
        }
        return back()->withInput($request->input())->with('message', "$records samples logged");
    }
}
