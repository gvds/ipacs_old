<?php

namespace App\Http\Controllers;

use App\event_sample;
use App\manifest;
use App\manifestItem;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Response;

class ManifestController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $manifests = manifest::where('project_id', session('currentProject'))
            ->where('sourceSite_id', Auth::user()->ProjectSite)
            ->whereIn('manifestStatus_id', [1, 2])
            ->orderBy('created_at', 'desc')
            ->get();
        $sites = \App\site::where('project_id', session('currentProject'))
            ->where('id', '!=', Auth::user()->ProjectSite)
            ->orderBy('name')
            ->pluck('name', 'id');
        return view('manifests.index', compact('manifests', 'sites'));
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index_received()
    {
        $manifests = manifest::where('project_id', session('currentProject'))
            ->where('destinationSite_id', Auth::user()->ProjectSite)
            ->whereIn('manifestStatus_id', [2, 3])
            ->orderBy('created_at', 'desc')
            ->get();
        return view('manifests.index_received', compact('manifests'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'destinationSite_id' => 'required|exists:sites,id'
        ]);
        $user = Auth::user();
        $manifest = new manifest([
            'project_id' => session('currentProject'),
            'user_id' => $user->id,
            'sourceSite_id' => $user->currentSite[0]->id,
            'destinationSite_id' => $validatedData['destinationSite_id']
        ]);
        $manifest->save();
        return back()->with('message', 'New manifest created');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\manifest  $manifest
     * @return \Illuminate\Http\Response
     */
    public function show(manifest $manifest)
    {
        if ($manifest->sourceSite_id !== Auth::user()->ProjectSite) {
            return back()->with('error', 'That manifest does not belong to your site');
        }
        $manifestItems = manifestItem::where('manifest_id', $manifest->id)
            ->orderBy('id')
            ->get();
        return view('manifests.show', compact('manifest', 'manifestItems'));
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\manifest  $manifest
     * @return \Illuminate\Http\Response
     */
    public function show_received(manifest $manifest)
    {
        if ($manifest->destinationSite_id !== Auth::user()->ProjectSite) {
            return back()->with('error', 'That manifest does not belong to your site');
        }
        $manifestItems = manifestItem::where('manifest_id', $manifest->id)
            ->orderBy('id')
            ->get();
        return view('manifests.show_received', compact('manifest', 'manifestItems'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\manifest  $manifest
     * @return \Illuminate\Http\Response
     */
    public function edit(manifest $manifest)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\manifest  $manifest
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, manifest $manifest)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\manifest  $manifest
     * @return \Illuminate\Http\Response
     */
    public function destroy(manifest $manifest)
    {
        if ($manifest->sourceSite_id !== Auth::user()->ProjectSite) {
            return back()->with('error', 'That manifest does not belong to your site');
        }
        $manifestItems = manifestItem::where('manifest_id', $manifest->id)
            ->get();
        try {
            DB::beginTransaction();
            foreach ($manifestItems as $manifestItem) {
                event_sample::find($manifestItem->event_sample_id)
                    ->update(['sampleStatus_id' => $manifestItem->prior_samplestatus_id]);
            }
            $manifest->delete();
            DB::commit();
        } catch (\Throwable $th) {
            DB::rollBack();
            return back()->with('error', $th->getMessage());
        }
        return redirect('/manifest');
    }

    public function ship(manifest $manifest)
    {
        if ($manifest->sourceSite_id !== Auth::user()->ProjectSite) {
            return back()->with('error', 'That manifest does not belong to your site');
        }
        try {
            DB::beginTransaction();
            $manifest->update(['manifestStatus_id' => 2, 'shippedDate' => now()]);
            $manifestItems = manifestItem::where('manifest_id', $manifest->id)
                ->pluck('event_sample_id');
            $event_samples = event_sample::whereIn('id', $manifestItems)
                ->get();
            if (count($event_samples) !== count($manifestItems)) {
                throw new Exception("Not all the manifest items could be found", 1);
            }
            foreach ($event_samples as $event_sample) {
                $event_sample->logAsTransferred();
            }
            DB::commit();
        } catch (\Throwable $th) {
            DB::rollBack();
            return back()->with('error', $th->getMessage());
        }
        return redirect('/manifest');
    }

    public function receive(manifest $manifest)
    {
        if ($manifest->destinationSite_id !== Auth::user()->ProjectSite) {
            return back()->with('error', 'That manifest does not belong to your site');
        }
        try {
            DB::beginTransaction();
            $manifest->update([
                'manifestStatus_id' => 3,
                'received_user_id' => Auth::user()->id,
                'receivedDate' => now()
            ]);
            $receivedManifestItems = manifestItem::where('manifest_id', $manifest->id)
                ->where('received', 1)
                ->pluck('event_sample_id');
            $event_samples = event_sample::whereIn('id', $receivedManifestItems)
                ->get();
            if (count($event_samples) !== count($receivedManifestItems)) {
                throw new Exception("Not all the received manifest items could be found", 1);
            }
            foreach ($event_samples as $event_sample) {
                $event_sample->logIntoSite(Auth::user()->ProjectSite);
            }
            $nonReceivedManifestItems = manifestItem::where('manifest_id', $manifest->id)
                ->where('received', 0)
                ->pluck('event_sample_id');
            $event_samples = event_sample::whereIn('id', $nonReceivedManifestItems)
                ->get();
            foreach ($event_samples as $event_sample) {
                $event_sample->returnToSource();
            }
            DB::commit();
        } catch (\Throwable $th) {
            DB::rollBack();
            return back()->with('error', $th->getMessage());
        }
        return redirect('/manifest/receive');
    }

    public function receiveall(manifest $manifest)
    {
        if ($manifest->destinationSite_id !== Auth::user()->ProjectSite) {
            return back()->with('error', 'That manifest does not belong to your site');
        }
        $timestamp = now();
        manifestItem::where('manifest_id', $manifest->id)
            ->update(['received' => 1, 'receivedTime' => $timestamp]);
        return redirect("/manifest/receive/$manifest->id");
    }

    public function samplelist(manifest $manifest)
    {
        // dd($manifest->destination->name);
        $event_samples = event_sample::with('storagelocation', 'sampletype')
            ->whereIn('samplestatus_id', [2,3])
            ->whereHas('sampletype', function ($query) use ($manifest) {
                return $query->where('project_id', session('currentProject'))
                    ->where('transferDestination', $manifest->destination->name);
            })
            ->whereHas('site', function ($query) {
                return $query->where('id', auth()->user()->currentsite[0]->id);
            })
            ->get();
        $headers = [
            'Content-type'        => 'text/csv',
            'Content-Disposition' => 'attachment; filename="samplelist.csv"',
        ];

        $data = "Barcode\tSampleType\tArm\tEvent\tAlquot\tVolume\tStatus\tSubjectID\tLocation\n";
        foreach ($event_samples as $key => $sample) {
            $sampledata = [
                $sample->barcode,
                $sample->sampletype->name,
                $sample->event_subject->event->arm->name,
                $sample->event_subject->event->name,
                $sample->aliquot,
                $sample->volume . $sample->sampletype->volumeUnit,
                $sample->status->samplestatus,
                $sample->event_subject->subject->subjectID
            ];
            if (!empty($sample->storagelocation)) {
                array_push($sampledata, '(' . $sample->storagelocation->virtualUnit->physicalUnit->unitID . ') ' . $sample->storagelocation->virtualUnit->virtualUnit . ' - ' . $sample->storagelocation->rack . ':' . $sample->storagelocation->box . ':' . $sample->storagelocation->position);
            }
            $data .= implode("\t", $sampledata) . "\n";
        }
        return Response::make($data, 200, $headers);
    }
}
