<?php

namespace App;

use Exception;
use Illuminate\Database\Eloquent\Model;

class manifestItem extends Model
{
    protected $guarded = [];

    public function event_sample()
    {
        return $this->belongsTo(event_sample::class);
    }

    public function log_received(string $barcode)
    {
        $response = $this->update([
            'received' => 1,
            'receivedTime' => now()
        ]);
        if (!$response) {
            throw new Exception("Error updating manifest item $barcode status as received", 1);
        }
    }
}
