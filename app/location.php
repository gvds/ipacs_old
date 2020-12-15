<?php

namespace App;

use Exception;
use Illuminate\Database\Eloquent\Model;

class location extends Model
{

  protected $guarded = [];

  public function virtualUnit()
  {
    return $this->belongsTo(virtualUnit::class, 'virtualUnit_id', 'id');
  }

  public function freelocation()
  {
    $response = $this->update(['used' => 0, 'virgin' => 0, 'barcode' => null]);
    if (!$response) {
      throw new Exception("Storage location could not be freed", 1);
    }
  }
}
