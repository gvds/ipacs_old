<?php

namespace App\Http\Controllers;

use App\event_sample;
use App\location;
use App\sampletype;
use App\storageLog;
use App\storageReport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Codedge\Fpdf\Fpdf\Fpdf;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Http;

class samplestoreController extends Controller
{

    private $fpdf;

    public function __construct()
    {
        define('FPDF_FONTPATH', public_path() . '/font');
    }

    private function logToStorageLog($sample, $sampletype, $storageEvent, $location_id = null)
    {
        $storageLog = new storageLog();
        $storageLog->storageReport_id = $storageEvent->id;
        $storageLog->sampletype_id = $sampletype->id;
        $storageLog->sample_id = $sample->id;
        $storageLog->location_id = $location_id;
        $storageLog->save();
    }
    /**
     * Finds next available appropriate storage location and marks location as used
     *
     * @param  int  $project
     * @param  int  $sampletype
     * @param  int  $virgin
     * @param  str  $barcode
     * @return  \Illuminate\Http\Response  $nextLocation
     *
     */
    private function storesample(int $project, int $sampletype, int $reuse, string $barcode)
    {

        if ($reuse == 1) {
            $virgin = [0, 1];
        } else {
            $virgin = [1];
        }
        $project = \App\project::find($project);
        $sampletype = \App\sampletype::find($sampletype);

        $nextLocation = location::whereHas('virtualUnit', function ($query) use ($sampletype) {
            $query->where('active', 1)
                ->where('storageSampleType', $sampletype->storageSampleType);
        })
            ->where('storageProjectName', $project->storageProjectName)
            ->where('used', 0)
            ->whereIn('virgin', $virgin)
            ->orderBy('virtualUnit_id')
            ->orderBy('rack')
            ->orderBy('box')
            ->orderBy('position')
            ->first();


        if (empty($nextLocation)) {
            return null;
        }
        $nextLocation->used = 1;
        $nextLocation->barcode = $barcode;
        $nextLocation->save();
        return $nextLocation->id;
    }

    // /**
    //  * Frees storage location and optionally unsets virgin flag
    //  *
    //  * @param  \Illuminate\Http\Request  $request
    //  * @return  \Illuminate\Http\Response
    //  *
    //  */
    // public static function freelocation(Request $request)
    // {
    //     $validatedData = $request->validate([
    //         'project' => 'required',
    //         'location' => 'required|integer',
    //         'virgin' => 'boolean'
    //         // 'barcode' => 'required'
    //     ]);

    //     if ($validatedData['virgin']) {
    //         $virgin = 1;
    //     } else {
    //         $virgin = 0;
    //     }

    //     try {
    //         location::findOrFail($validatedData['location'])->update(['used' => 0, 'virgin' => $virgin]);
    //         return true;
    //     } catch (\Throwable $th) {
    //         return back()->withErrors($th->getMessage());
    //     }
    // }

    /**
     * Presents sets of logged, unstored samples for storage
     *
     * @return \Illuminate\Http\Response
     *
     */
    public function listSamples()
    {
        $sampletypes = sampletype::with(['event_samples' => function ($query) {
            $query->where('site_id', auth()->user()->project_site)
                ->where('samplestatus_id', 2);
        }])
            ->where('project_id', session('currentProject'))
            ->whereNotNull('storageDestination')
            ->get();
        $storageDestinations = [];
        foreach ($sampletypes as $id => $sampletype) {
            if (count($sampletype->event_samples) > 0) {
                if (!array_key_exists($sampletype->storageDestination, $storageDestinations)) {
                    $storageDestinations[$sampletype->storageDestination] = [];
                }
                array_push($storageDestinations[$sampletype->storageDestination], [
                    'sampletype_id' => $sampletype->id,
                    'name' => $sampletype->name,
                    'count' => $sampletype->event_samples->count()
                ]);
            }
        }
        $storageDestinations = collect($storageDestinations);

        $project = \App\project::find(session('currentProject'));
        $lowstorage = location::join('virtualUnits', 'virtualUnit_id', 'virtualUnits.id')
            ->select(DB::raw('storageSampleType, count(*) as total, sum(used) as used'))
            ->where('active', 1)
            ->where('storageProjectName', $project->storageProjectName)
            ->groupBy('storageSampleType')
            ->orderBy('storageSampleType')
            ->havingRaw('(total - used) / total < ?', [0.1])
            ->get();

        return view('samples.allocateStorage', compact('storageDestinations', 'lowstorage'));
    }

    public function allocateStorage(Request $request)
    {
        $request->validate([
            'sampletype' => 'required|array',
            'storageDestination' => 'required',
            'sampletype.*' => 'integer',
            'reuse.*' => [
                'required',
                Rule::in([0, 1])
            ]
        ]);
        $sampletypes = sampletype::whereIn('id', $request->sampletype)
            ->with(['event_samples' => function ($query) {
                $query->where('site_id', auth()->user()->project_site)
                    ->where('samplestatus_id', 2);
            }])
            ->where('project_id', session('currentProject'))
            ->whereNotNull('storageSampleType')
            ->get();
        try {
            DB::beginTransaction();
            $stored_count = 0;
            $arr_nospace = [];
            $project_id = session('currentProject');

            // log to storage report table
            $storageEvent = new storageReport();
            $storageEvent->project_id = session('currentProject');
            $storageEvent->user_id = auth()->user()->id;
            $storageEvent->storageDestination = $request->storageDestination;
            $storageEvent->save();

            foreach ($sampletypes as $sampletype) {

                if ($sampletype->storageDestination === 'BiOS') {
                    foreach ($sampletype->event_samples as $sample) {
                        $sample->location = 0;
                        $sample->samplestatus_id = 3;
                        $sample->save();
                        $this->logToStorageLog($sample, $sampletype, $storageEvent);
                        $stored_count++;
                    }
                } else if ($sampletype->storageDestination === 'Nexus') {
                    foreach ($sampletype->event_samples as $sample) {
                        $sample->location = 0;
                        $sample->samplestatus_id = 3;
                        $sample->save();
                        $this->logToStorageLog($sample, $sampletype, $storageEvent);
                        $stored_count++;
                    }
                } else {
                    foreach ($sampletype->event_samples as $sample) {
                        // Allocate storage position
                        $location_id = $this->storesample($project_id, $sampletype->id, (int)$request->reuse[0], $sample->barcode);
                        if (!is_null($location_id)) {
                            // Update sample record
                            $sample->location = $location_id;
                            $sample->samplestatus_id = 3;
                            $sample->save();
                            $stored_count++;
                        } else {
                            if (array_key_exists($sampletype->name, $arr_nospace)) {
                                $arr_nospace[$sampletype->name] += 1;
                            } else {
                                $arr_nospace[$sampletype->name] = 1;
                            }
                        }
                        // log to storage logs table
                        $this->logToStorageLog($sample, $sampletype, $storageEvent, $location_id);
                    }
                }
            }
            DB::commit();
        } catch (\Throwable $th) {
            DB::rollBack();
            return back()->withInput()->withErrors($th->getMessage());
        }
        return back()->with(['message' => $stored_count . " samples allocated to storage", 'unallocated' => $arr_nospace]);
    }

    public function reportList()
    {
        $reports = storageReport::where('project_id', session('currentProject'))
            ->orderBy('created_at', 'desc')
            ->paginate(30);

        return view('samples.storageReports', compact('reports'));
    }

    public function report(Request $request, storageReport $storageReport)
    {
        if ($storageReport->storageDestination == 'Internal') {

            $storageLogs = $storageReport->storageLogs->whereNotNull('location_id');

            $layout = 'P';
            if ($layout == "P") {
                $this->fpdf = new Fpdf('P');
            } else {
                $this->fpdf = new Fpdf('L');
            }

            $this->fpdf->AddFont('Calibri', 'B', 'calibrib.php');
            $this->fpdf->AddFont('Calibri', '', 'calibri.php');
            $this->fpdf->SetDisplayMode('fullpage');
            $this->fpdf->SetMargins(5, 5);
            $this->fpdf->AddPage();
            $this->fpdf->SetFont('Calibri', 'B', 16);
            $this->fpdf->Cell(0, 9, $request->currentProject->project . " Sample Storage", 0, 1, 'C');
            $this->fpdf->SetFont('Calibri', 'B', 14);
            $this->fpdf->Cell(0, 9, "(" . $storageReport->created_at . " - " . $storageReport->user->fullname . ")", 0, 1, 'C');
            $this->fpdf->SetFont('Calibri', 'B', 11);
            $this->fpdf->Cell(0, 0, '', 'T', 1, 'L');
            $this->fpdf->Cell(42, 7, "Sample Type", '', 0, 'L');
            $this->fpdf->Cell(24, 7, "Subject", '', 0, 'L');
            $this->fpdf->Cell(20, 7, "Event", '', 0, 'L');
            $this->fpdf->Cell(14, 7, "Aliquot", '', 0, 'L');
            if ($layout == "P") {
                $this->fpdf->Cell(23, 7, "Barcode", '', 0, 'L');
            } else {
                $this->fpdf->Cell(40, 7, "Barcode", '', 0, 'L');
            }
            $this->fpdf->Cell(18, 7, "Unit", '', 0, 'L');
            $this->fpdf->SetFont('Calibri', 'B', 8);
            $this->fpdf->Cell(30, 7, " (Virtual-Unit Rack:Box:Position)", '', 1, 'L');
            $this->fpdf->Cell(0, 0, '', 'T', 1, 'L');

            $this->fpdf->SetFont('Calibri', '', 9);

            foreach ($storageLogs as $storageLog) {
                $this->fpdf->Cell(42, 7, $storageLog->sampletype->name, 0, 0, 'L');
                $this->fpdf->Cell(24, 7, $storageLog->sample->event_subject->subject->subjectID, 0, 0, 'L');
                $this->fpdf->Cell(20, 7, $storageLog->sample->event_subject->event->name, 0, 0, 'L');
                $this->fpdf->Cell(14, 7, $storageLog->sample->aliquot, 0, 0, 'C');
                if ($layout == "P") {
                    $this->fpdf->Cell(23, 7, $storageLog->sample->barcode, 0, 0, 'L');
                } else {
                    $this->fpdf->Cell(40, 7, $storageLog->sample->barcode, 0, 0, 'L');
                }
                if (!empty($storageLog->location_id)) {
                    $locstring = "[" . $storageLog->storageLocation->virtualUnit->physicalUnit->unitID . "] : " . $storageLog->storageLocation->virtualUnit->virtualUnit . "   " . $storageLog->storageLocation->rack . " : " . $storageLog->storageLocation->box . " : " . $storageLog->storageLocation->position;
                } else {
                    $locstring = "No Storage location allocated";
                }
                $this->fpdf->Cell(60, 7, $locstring, 0, 1, 'L');
            }
            $this->fpdf->Cell(0, 1, '', 'TB', 1, 'L');

            $storageLogs = $storageReport->storageLogs->whereNull('location_id');
            if ($storageLogs->count() > 0) {
                $this->fpdf->SetFont('Calibri', 'B', 12);
                $this->fpdf->Cell(0, 3, '', '', 1, 'L');
                $this->fpdf->Cell(0, 9, "Samples not allocated storage positions due to the lack of available space", 0, 1, 'L');
                $this->fpdf->SetFont('Calibri', 'B', 11);
                $this->fpdf->Cell(0, 0, '', 'T', 1, 'L');
                $this->fpdf->Cell(30, 7, "Barcode", '', 0, 'L');
                $this->fpdf->Cell(55, 7, "Sample Type", '', 1, 'L');
                $this->fpdf->Cell(0, 0, '', 'T', 1, 'L');
                $this->fpdf->SetFont('Calibri', '', 9);

                foreach ($storageLogs as $storageLog) {
                    $this->fpdf->Cell(30, 7, $storageLog->sample->barcode, 0, 0, 'L');
                    $this->fpdf->Cell(55, 7, $storageLog->sampletype->name, 0, 1, 'L');
                }
            }
            $this->fpdf->Cell(0, 1, '', 'TB', 1, 'L');

            $this->fpdf->Output("storageReport.pdf", "I");
        } else {
            $storageLogs = $storageReport->storageLogs;
            $headers = [
                'Content-type'        => 'text/csv',
                'Content-Disposition' => 'attachment; filename="nexusreport.csv"',
            ];
            $data = "Barcode\tSampleType\tStatus\n";
            foreach ($storageLogs as $log) {
                $samples = [
                    $log->sample->barcode,
                    $log->sampletype->name,
                    $log->sample->status->samplestatus
                ];
                $data .= implode("\t", $samples) . "\n";
            }
            return Response::make($data, 200, $headers);
        }
    }

    public function nexusReport()
    {
        try {
            if (count(auth()->user()->currentSite) !== 0) {
                $siteID = auth()->user()->currentSite[0]->id;
            } else {
                $siteID = '%';
            }
            $samples = event_sample::with('sampletype')
                ->whereHas('sampletype', function ($query) {
                    return $query->where('project_id', session('currentProject'));
                })
                ->where('site_id', 'like', $siteID)
                ->where('location', 0)
                ->get();
            if (count($samples) == 0) {
                throw new Exception('There are no samples assigned to BiOS storage', 1);
            }
            $storageProjectName = request('currentProject')->storageProjectName;
            $containers = Http::withToken(config('services.nexus.token'))
                ->acceptJson()
                ->timeout(5)
                ->withOptions([
                    'verify' => public_path('nexus.pem')
                    // 'verify' => false
                ])
                ->post(config('services.nexus.url') . 'containers', [
                    'storageName' => $storageProjectName
                ]);
            if ($containers->clientError()) {
                throw new Exception('Could not get sample storage status data from Nexus: ' . $containers['message'], 1);
            }
            if ($containers->serverError()) {
                throw new Exception('Nexus server error: ' . $containers['message'], 1);
            }

            $container_arr = (array) json_decode($containers->body());
            foreach ($samples as $key => $sample) {
                $samples[$key]->status = array_key_exists($sample->barcode, $container_arr) ? $container_arr[$sample->barcode] : 'No BiOS Record';
            }

            $headers = [
                'Content-type'        => 'text/csv',
                'Content-Disposition' => 'attachment; filename="nexusreport.csv"',
            ];

            $data = "Barcode\tSampleType\tStatus\n";
            foreach ($samples as $sample) {
                $nexusSamples = [
                    $sample->barcode,
                    $sample->sampletype->name,
                    $sample->status
                ];
                $data .= implode("\t", $nexusSamples) . "\n";
            }
            return Response::make($data, 200, $headers);
        } catch (\Throwable $th) {
            return redirect('/')->withErrors($th->getMessage());
        }
    }
}
