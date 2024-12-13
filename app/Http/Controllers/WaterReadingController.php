<?php

namespace App\Http\Controllers;

use App\Models\WaterReading;
use Illuminate\Http\Request;

class WaterReadingController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
        $data = WaterReading::all();
        return response()->json($data, 200);
    }

    public function getCurrentWaterReading()
    {
        $data = WaterReading::orderBy('created_at', 'desc')->first();

        if (!$data) {
            return response()->json([
                'message' => 'No parameters found'
            ], 404);
        }

        return response()->json($data);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
        $storeWaterReading = $request->validate([
            'water_temperature' => 'required|numeric',
            'dissolved_oxygen' => 'required|numeric',
            'ph_level' => 'required|numeric',
        ]);

        $readings = WaterReading::create($storeWaterReading);

        return response()->json($readings, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
