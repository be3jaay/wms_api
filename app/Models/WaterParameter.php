<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Anomalies;  // Use WaterParameter model instead of WaterReading

class WaterParameter extends Model
{
    use HasFactory;

    // Define the table name
    protected $table = 'water_parameters'; // The table name is 'water_parameters'

    // Define the fillable columns (fields that can be mass-assigned)
    protected $fillable = ['do', 'ph', 'temp', 'timestamp'];

    // If you're using timestamps and want to handle the timestamp automatically:
    public $timestamps = false; // Set to false if you don't want Laravel to automatically manage the timestamps
    
    public function anomalies()
    {
        return $this->hasMany(Anomalies::class);
    }
}
