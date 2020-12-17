<?php

namespace App\Http\Controllers;

use App\manifestItem;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ManifestItemController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
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
            'manifest_id' => 'required|exists:manifests,id',
            'barcode' => 'required|exists:event_sample,barcode'
        ]);
        $manifest = \App\manifest::find($validatedData['manifest_id']);
        if ($manifest->user->ProjectSite !== $manifest->sourceSite_id) {
            return back()->with('error', 'This manifest does not originate at your site');
        }
        $event_sample = \App\event_sample::join('sampletypes', 'sampletype_id', '=', 'sampletypes.id')
            ->where('barcode', $validatedData['barcode'])
            ->where('project_id', session('currentProject'))
            ->select('event_sample.id', 'samplestatus_id')
            ->first();
        if (is_null($event_sample)) {
            return back()->with('error', 'Sample ' . $validatedData['barcode'] . ' was not found in this project');
        }
        $event_sample = \App\event_sample::find($event_sample->id);
        if ($event_sample->site->id !== Auth::user()->ProjectSite) {
            return back()->with('error', 'Sample ' . $validatedData['barcode'] . ' is not logged to your site');
        }
        if ($event_sample->samplestatus_id === 4) {
            return back()->with('error', 'Sample ' . $validatedData['barcode'] . ' has already been added to a manifest');
        }
        if (!in_array($event_sample->samplestatus_id, [2, 3])) {
            return back()->with('error', 'Sample ' . $validatedData['barcode'] . ' has a status of ' . $event_sample->status->samplestatus . ' and is not available for shipping');
        }
        try {
            DB::beginTransaction();
            $manifestItem = new manifestItem([
                'manifest_id' => $manifest->id,
                'event_sample_id' => $event_sample->id,
                'user_id' => Auth::user()->id,
                'prior_samplestatus_id' => $event_sample->samplestatus_id
            ]);
            $manifestItem->save();
            $event_sample->update(['samplestatus_id' => 4]);
            DB::commit();
        } catch (\Throwable $th) {
            DB::rollBack();
            return back()->with('error', $th->getMessage());
        }
        return back()->with('message', $validatedData['barcode'] . " added to manifest");
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\manifestItem  $manifestItem
     * @return \Illuminate\Http\Response
     */
    public function show(manifestItem $manifestItem)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\manifestItem  $manifestItem
     * @return \Illuminate\Http\Response
     */
    public function edit(manifestItem $manifestItem)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\manifestItem  $manifestItem
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, manifestItem $manifestItem)
    {
        $validatedData = $request->validate([
            'manifest_id' => 'required|exists:manifests,id',
            'barcode' => 'required|exists:event_sample,barcode'
        ]);
        try {
            $manifest = \App\manifest::find($validatedData['manifest_id']);
            if (Auth::user()->ProjectSite !== $manifest->destinationSite_id) {
                throw new Exception('This manifest has not been sent to your site', 1);
            }
            $manifestItem = manifestItem::join('event_sample', 'event_sample_id', 'event_sample.id')
                ->where('barcode', $validatedData['barcode'])
                ->select('manifest_items.id', 'event_sample_id', 'received')
                ->first();
            if (is_null($manifestItem)) {
                throw new Exception('Sample ' . $validatedData['barcode'] . ' was not found in this manifest', 1);
            }
            if ($manifestItem->received === 1) {
                throw new Exception('Sample ' . $validatedData['barcode'] . ' has already been logged in', 1);
            }
            DB::beginTransaction();
            manifestItem::find($manifestItem->id)->log_received($validatedData['barcode']);
            \App\event_sample::find($manifestItem->event_sample_id)->logAsReceived();
            DB::commit();
        } catch (\Throwable $th) {
            DB::rollBack();
            return back()->with('error', $th->getMessage());
        }
        return back()->with('message', $validatedData['barcode'] . " logged as received");
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\manifestItem  $manifestItem
     * @return \Illuminate\Http\Response
     */
    public function destroy(manifestItem $manifestItem)
    {
        $manifest = \App\manifest::find($manifestItem->manifest_id);
        if ($manifest->user->ProjectSite !== $manifest->sourceSite_id) {
            return back()->with('error', 'This manifest does not orriginate at your site');
        }
        $event_sample = \App\event_sample::find($manifestItem->event_sample_id);
        if (is_null($event_sample)) {
            return back()->with('error', 'The sample could not be found in this project');
        };
        try {
            DB::beginTransaction();
            $event_sample->update(['samplestatus_id' => $manifestItem->prior_samplestatus_id]);
            $manifestItem->delete();
            DB::commit();
        } catch (\Throwable $th) {
            DB::rollBack();
            return back()->with('error', $th->getMessage());
        }
        return back()->with('message', "Sample removed from manifest");
    }
}
