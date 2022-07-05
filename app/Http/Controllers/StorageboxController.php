<?php

namespace App\Http\Controllers;

use App\sampletype;
use App\storagebox;
use App\storageboxposition;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StorageboxController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $validated = $request->validate([
            'sampletype' => 'exists:sampletypes,id|nullable'
        ]);
        $sampletypes = sampletype::where('project_id', session('currentProject'))
            ->where('storageDestination', 'StorageBox')
            ->orderBy('name')
            ->get();
        if (isset($validated['sampletype'])) {
            $storageboxes = storagebox::whereRelation('sampletype', 'project_id', session('currentProject'))
                ->get();
        } else {
            $storageboxes = [];
        }
        return view('storage.storagebox.index', compact('storageboxes', 'sampletypes'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $sampletypes = sampletype::where('project_id', session('currentProject'))
            ->where('storageDestination', 'StorageBox')
            ->orderBy('name')
            ->pluck('name', 'id')
            ->prepend('', '');
        return view('storage.storagebox.create', compact('sampletypes'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'barcode' => 'required|min:6|max:20',
            'sampletype_id' => 'required|exists:sampletypes,id',
            'positions' => 'required|integer|min:1|max:150'
        ]);
        DB::beginTransaction();
        try {
            $storagebox = storagebox::create($validated);
            $boxpositions = [];
            for ($i = 1; $i <= $validated['positions']; $i++) {
                $boxpositions[] = [
                    'storagebox_id' => $storagebox->id,
                    'position' => $i,
                    'created_at' =>  \Carbon\Carbon::now(),
                    'updated_at' => \Carbon\Carbon::now(),
                ];
            }
            storageboxposition::insert($boxpositions);
            DB::commit();
        } catch (\Throwable $th) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Storage box creation failed: ' . $th->getMessage());
        }
        return redirect("/storagebox/$storagebox->id");
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\storagebox  $storagebox
     * @return \Illuminate\Http\Response
     */
    public function show(storagebox $storagebox)
    {
        $storagebox = $storagebox::with('boxPositions')->first();
        $used = count($storagebox->usedPositions());
        return view('storage.storagebox.show', compact('storagebox', 'used'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\storagebox  $storagebox
     * @return \Illuminate\Http\Response
     */
    public function edit(storagebox $storagebox)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\storagebox  $storagebox
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, storagebox $storagebox)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\storagebox  $storagebox
     * @return \Illuminate\Http\Response
     */
    public function destroy(storagebox $storagebox)
    {
        //
    }

    public function search(Request $request)
    {
        $validated = $request->validate([
            'barcode' => 'required'
        ]);
        $storagebox = storagebox::where('barcode', $validated['barcode'])->first();
        return redirect("/storagebox/$storagebox->id");
    }
}
