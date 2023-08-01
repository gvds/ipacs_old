<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\event_sample;
use App\Rules\BarcodeFormat;
use App\sampletype;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
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

    function file()
    {
        $sampletypes = sampletype::where('project_id', session('currentProject'))
            ->where('primary', 0)
            ->pluck('name', 'id')->prepend('', '');
        return view('derivativesamples.file', compact('sampletypes'));
    }
    public function bulklog(Request $request)
    {
        $validatedData = $request->validate([
            'sampletype' => 'required|exists:sampletypes,id',
            'samplefile' => 'required|file'
        ]);
        $sampletype = sampletype::where('project_id', session('currentProject'))
            ->where('primary', 0)
            ->where('id', $validatedData['sampletype'])
            ->firstOrFail();
        $derivativeCount = 0;
        $aliquots = [];
        try {
            DB::begintransaction();
            $reader = new \PhpOffice\PhpSpreadsheet\Reader\Xlsx();
            $reader->setReadDataOnly(true);
            $reader->setLoadSheetsOnly("Run_Report");
            $spreadsheet = $reader->load($validatedData['samplefile']);
            $sheet = $spreadsheet->getSheet(0)->toArray();
            $header = array_shift($sheet);
            // $sourceCol = array_search('Position Barcode Source', $header) ?: throw new Exception("Parent barcode column not found in sample file", 1);
            // $targetCol = array_search('Position Barcode Target', $header) ?: throw new Exception("Derivative barcode column not found in sample file", 1);
            // $volumeCol = array_search('Transfer Volume', $header) ?: throw new Exception("Volume column not found in sample file", 1);
            $sourceCol = array_search('Position Barcode Source', $header);
            if (!$sourceCol) throw new Exception("Parent barcode column not found in sample file", 1);
            $targetCol = array_search('Position Barcode Target', $header);
            if (!$targetCol) throw new Exception("Derivative barcode column not found in sample file", 1);
            $volumeCol = array_search('Transfer Volume', $header);
            if (!$volumeCol) throw new Exception("Volume column not found in sample file", 1);
            foreach ($sheet as $key => $row) {
                if (!is_null($row[$sourceCol]) && !is_null($row[$targetCol])) {
                    $parent = event_sample::join('sampletypes', 'sampletype_id', '=', 'sampletypes.id')
                        ->where('barcode', $row[$sourceCol])
                        ->where('project_id', session('currentProject'))
                        ->first();
                    if (is_null($parent)) {
                        throw new Exception('Sample ' . $row[$sourceCol] . ' was not found in this project');
                    }
                    if ($parent->site_id !== Auth::user()->ProjectSite) {
                        throw new Exception('Sample ' . $row[$sourceCol] . ' is not logged to your site');
                    }
                    if ($parent->sampletype_id !== $sampletype->parentSampleType_id) {
                        throw new Exception("Sample $row[$sourceCol] is of sample-type '$parent->name' which is not the correct parent for this derivative type");
                    }
                    $derivative = event_sample::join('sampletypes', 'sampletype_id', '=', 'sampletypes.id')
                        ->where('barcode', $row[$targetCol])
                        ->where('project_id', session('currentProject'))
                        ->where('samplestatus_id', '!=', 0)
                        ->first();
                    if (!is_null($derivative)) {
                        throw new Exception('The derivative barcode, ' . $row[$targetCol] . ', has already been assigned');
                    }
                    $aliquots[$parent->barcode] = array_key_exists($parent->barcode, $aliquots) ? $aliquots[$parent->barcode] + 1 : 1;
                    $sample = new event_sample([
                        'sampletype_id' => $sampletype->id,
                        'event_subject_id' => $parent->event_subject_id,
                        'barcode' => $row[$targetCol],
                        'volume' => $row[$volumeCol],
                        'volumeUnit' => $sampletype->volumeUnit,
                        'site_id' => Auth::user()->ProjectSite,
                        'loggedBy' => Auth::user()->id,
                        'logTime' => now(),
                        'samplestatus_id' => 2,
                        'aliquot' => $aliquots[$parent->barcode],
                        'parentBarcode' => $parent->barcode
                    ]);
                    $sample->save();
                    $derivativeCount += 1;
                }
            }
            DB::commit();
        } catch (\Throwable $th) {
            DB::rollBack();
            return back()->withErrors($th->getMessage());
        }

        return redirect('/derivative/file')->with('message', "$derivativeCount derivatives logged successfully");
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
        $maxaliquots = 1;
        foreach ($sampletypes as $sampletype) {
            $maxaliquots = max($maxaliquots, $sampletype->aliquots);
        }
        $parent_sample = $event_sample;
        return view('derivativesamples.log', compact('sampletypes', 'parent_sample', 'maxaliquots', 'subject'));
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
