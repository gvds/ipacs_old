<?php

namespace App\Http\Controllers;

use App\event_sample;
use App\manifest;
use App\manifestItem;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

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
        $manifestItems = $manifest->manifestItems;
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
        $manifestItems = $manifest->manifestItems;
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
        $manifestItems = $manifest->manifestItems;
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
            $manifestItems = $manifest->manifestItems->pluck('event_sample_id');
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
        switch ($manifest->manifestStatus_id) {
            case 1:
                return back()->with('error', 'This manifest has not been shipped');
            case 3:
                return back()->with('error', 'This manifest has already been received ');
        }
        if ($manifest->destinationSite_id !== Auth::user()->ProjectSite) {
            return back()->with('error', 'This manifest does not belong to your site');
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
                ->pluck('prior_sampletatus_id', 'event_sample_id')
                ->toArray();
            $event_samples = event_sample::whereIn('id', array_keys($nonReceivedManifestItems))
                ->get();
            foreach ($event_samples as $event_sample) {
                $event_sample->returnToSource($nonReceivedManifestItems[$event_sample->id]);
            }
            DB::commit();
        } catch (\Throwable $th) {
            DB::rollBack();
            return back()->with('error', $th->getMessage());
        }
        return redirect('/manifest/receive');
    }

    public function shipperLogReceived(manifest $manifest)
    {
        switch ($manifest->manifestStatus_id) {
            case 1:
                return back()->with('error', 'This manifest has not been shipped');
            case 3:
                return back()->with('error', 'This manifest has already been received ');
        }
        if ($manifest->sourceSite_id !== Auth::user()->ProjectSite) {
            return back()->with('error', 'This manifest was not sent from your site');
        }
        try {
            DB::beginTransaction();
            $manifest->update([
                'manifestStatus_id' => 3,
                'received_user_id' => Auth::user()->id,
                'receivedDate' => now()
            ]);
            manifestItem::where('manifest_id', $manifest->id)
                ->update(['received' => 1]);

            $receivedManifestItems = manifestItem::where('manifest_id', $manifest->id)
                ->pluck('event_sample_id');
            $event_samples = event_sample::whereIn('id', $receivedManifestItems)
                ->get();
            if (count($event_samples) !== count($receivedManifestItems)) {
                throw new Exception("Not all the received manifest items could be found", 1);
            }
            foreach ($event_samples as $event_sample) {
                $event_sample->logIntoSite($manifest->destinationSite_id);
            }
            DB::commit();
        } catch (\Throwable $th) {
            DB::rollBack();
            return back()->with('error', $th->getMessage());
        }
        return redirect('/manifest');
    }

    public function receiveall(manifest $manifest)
    {
        if ($manifest->destinationSite_id !== Auth::user()->ProjectSite) {
            return back()->with('error', 'This manifest does not belong to your site');
        }
        $timestamp = now();
        try {
            DB::beginTransaction();
            manifestItem::where('manifest_id', $manifest->id)
                ->update(['received' => 1, 'receivedTime' => $timestamp]);
            $receivedManifestItems = manifestItem::where('manifest_id', $manifest->id)
                ->pluck('event_sample_id');
            $event_samples = event_sample::whereIn('id', $receivedManifestItems)
                ->get();
            if (count($event_samples) !== count($receivedManifestItems)) {
                throw new Exception("Not all the received manifest items could be found", 1);
            }
            foreach ($event_samples as $event_sample) {
                $event_sample->logIntoSite($manifest->destinationSite_id);
            }
            DB::commit();
        } catch (\Throwable $th) {
            DB::rollBack();
            return back()->with('error', $th->getMessage());
        }
        return redirect("/manifest/receive/$manifest->id");
    }

    public function samplelist(manifest $manifest)
    {
        return $manifest->samplelist();
    }

    public function itemlist(manifest $manifest)
    {
        return $manifest->itemlist();
    }
}
