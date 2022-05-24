<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Response;

class manifest extends Model
{
    protected $guarded = [];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function receiver()
    {
        return $this->belongsTo(user::class, 'received_user_id', 'id');
    }

    public function destination()
    {
        return $this->belongsTo(site::class, 'destinationSite_id', 'id');
    }

    public function source()
    {
        return $this->belongsTo(site::class, 'sourceSite_id', 'id');
    }

    public function manifestItems()
    {
        return $this->hasMany(manifestItem::class)->orderBy('id');
    }

    public function samplelist()
    {
        $event_samples = event_sample::with('storagelocation', 'sampletype')
            ->whereIn('samplestatus_id', [2, 3])
            ->whereHas('sampletype', function ($query) {
                return $query->where('project_id', session('currentProject'))
                    ->where('transferDestination', $this->destination->name);
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

    public function itemlist()
    {
        $items = manifestItem::with('event_sample')
            ->where('manifest_id', $this->id)
            ->get();
        $headers = [
            'Content-type'        => 'text/csv',
            'Content-Disposition' => 'attachment; filename="samplelist.csv"',
        ];

        $data = "Barcode\tAlquot\n";
        foreach ($items as $key => $item) {
            $sampledata = [
                $item->event_sample->barcode,
                $item->event_sample->aliquot,
            ];
            $data .= implode("\t", $sampledata) . "\n";
        }
        return Response::make($data, 200, $headers);
    }
}
