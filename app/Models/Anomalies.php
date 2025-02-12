<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\WaterParameter;

class Anomalies extends Model
{
    use HasFactory;

    protected $fillable = ['water_parameter_id', 'type', 'value', 'suggestion'];

    public function waterParameter()
    {
        return $this->belongsTo(WaterParameter::class);
    }
}