<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WaterReading extends Model
{
    use HasFactory;

    protected $fillable = [
        'water_temperature',
        'dissolved_oxygen',
        'ph_level',
    ];

    protected $casts = [
        'water_temperature' => 'float',
        'dissolved_oxygen' => 'float',
        'ph_level' => 'float',
    ];

    public function anomalies()
    {
        return $this->hasMany(Anomalies::class);
    }
}
