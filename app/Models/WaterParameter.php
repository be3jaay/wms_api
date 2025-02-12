<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Anomalies;

class WaterParameter extends Model
{
    use HasFactory;

    protected $table = 'water_parameters';
    protected $fillable = ['do', 'ph', 'temp'];
    public $timestamps = false;

    public function anomalies()
    {
        return $this->hasMany(Anomalies::class);
    }
}