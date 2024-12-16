<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Anomalies extends Model
{
    use HasFactory;

    protected $fillable = ['water_reading_id', 'type', 'value', 'suggestion'];

    public function waterReading()
    {
        return $this->belongsTo(WaterReading::class);
    }
}
