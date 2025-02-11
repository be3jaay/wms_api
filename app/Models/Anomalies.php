<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\WaterParameter;  // Use WaterParameter model instead of WaterReading


class Anomalies extends Model
{
    use HasFactory;

    protected $fillable = ['water_reading_id', 'type', 'value', 'suggestion'];

    public function waterReading()
    {
        return $this->belongsTo(WaterParameter::class);
    }
}
