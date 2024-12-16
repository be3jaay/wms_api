<?php

namespace App\Http\Controllers;

use App\Models\Anomalies;
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

    public function getWaterReadingRange(Request $request)
    {
        $range = $request->input('range');

        switch ($range) {
            case 'all':
                $data = WaterReading::all();
                break;
            case 'daily':
                $data = WaterReading::whereDate('created_at', date('Y-m-d'))->get();
                break;
            case 'weekly':
                $data = WaterReading::whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])->get();
                break;
            case 'monthly':
                $data = WaterReading::whereMonth('created_at', date('m'))->get();
                break;
            case 'yearly':
                $data = WaterReading::whereYear('created_at', date('Y'))->get();
                break;
            default:
                return response()->json([
                    'message' => 'Invalid range parameter'
                ], 400);
        }

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

    public function store(Request $request)
    {
        //
        $storeWaterReading = $request->validate([
            'water_temperature' => 'required|numeric',
            'dissolved_oxygen' => 'required|numeric',
            'ph_level' => 'required|numeric',
        ]);

        $readings = WaterReading::create($storeWaterReading);

        $this->detectAnomalies($readings);

        return response()->json([
            'message' => 'Water reading created successfully',
            'data' => $readings->load('anomalies')
        ], 201);
    }
    private function detectAnomalies(WaterReading $reading)
    {
        $messages = [
            'water_temperature' => [
                'high' => 'Water temperature is too high. Check cooling systems.',
                'low' => 'Water temperature is too low. Consider heating measures.',
            ],
            'dissolved_oxygen' => [
                'high' => 'Dissolved oxygen is too high. Investigate potential over-aeration.',
                'low' => 'Dissolved oxygen is too low. Check aerators or oxygen levels.',
            ],
            'ph_level' => [
                'high' => 'pH level is too high. Adjust with appropriate chemicals.',
                'low' => 'pH level is too low. Neutralize with a suitable base.',
            ],
        ];

        $anomalies = [];

        // Example thresholds
        if ($reading->water_temperature > 28) {
            $anomalies[] = [
                'type' => 'water_temperature',
                'value' => $reading->water_temperature,
                'suggestion' => $messages['water_temperature']['high'],
            ];
        } elseif ($reading->water_temperature < 22) {
            $anomalies[] = [
                'type' => 'water_temperature',
                'value' => $reading->water_temperature,
                'suggestion' => $messages['water_temperature']['low'],
            ];
        }

        if ($reading->dissolved_oxygen < 6) {
            $anomalies[] = [
                'type' => 'dissolved_oxygen',
                'value' => $reading->dissolved_oxygen,
                'suggestion' => $messages['dissolved_oxygen']['low'],
            ];
        } elseif ($reading->dissolved_oxygen > 9) {
            $anomalies[] = [
                'type' => 'dissolved_oxygen',
                'value' => $reading->dissolved_oxygen,
                'suggestion' => $messages['dissolved_oxygen']['high'],
            ];
        }

        if ($reading->ph_level > 8.5) {
            $anomalies[] = [
                'type' => 'ph_level',
                'value' => $reading->ph_level,
                'suggestion' => $messages['ph_level']['high'],
            ];
        } elseif ($reading->ph_level < 6.5) {
            $anomalies[] = [
                'type' => 'ph_level',
                'value' => $reading->ph_level,
                'suggestion' => $messages['ph_level']['low'],
            ];
        }


        foreach ($anomalies as $anomaly) {
            Anomalies::create(array_merge($anomaly, ['water_reading_id' => $reading->id]));
        }
    }

    public function showReadingPerDay()
    {
        $data = WaterReading::whereDate('created_at', date('Y-m-d'))->get();
        return response()->json($data, 200);
    }

    public function showReadingPerWeek()
    {
        $data = WaterReading::whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])->get();
        return response()->json($data, 200);
    }

    public function showReadingPerMonth()
    {
        $data = WaterReading::whereMonth('created_at', date('m'))->get();
        return response()->json($data, 200);
    }

    public function showReadingPerYear()
    {
        $data = WaterReading::whereYear('created_at', date('Y'))->get();
        return response()->json($data, 200);
    }

    public function showReadingPerCustomDate(Request $request)
    {
        $data = WaterReading::whereBetween('created_at', [$request->start_date, $request->end_date])->get();
        return response()->json($data, 200);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id) {}
}
