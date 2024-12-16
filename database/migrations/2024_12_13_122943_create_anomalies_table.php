<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('anomalies', function (Blueprint $table) {
            $table->id();
            $table->foreignId('water_reading_id')->constrained('water_readings')->onDelete('cascade'); // Foreign key to water_readings
            $table->string('type'); // Type of anomaly (e.g., water_temperature, dissolved_oxygen, ph_level)
            $table->float('value'); // Detected parameter value
            $table->text('suggestion'); // Built-in message for high/low values
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('anomalies');
    }
};
