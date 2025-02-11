<?php

namespace App\Http\Controllers;
use Illuminate\Support\Carbon;
use App\Models\Anomalies;
use App\Models\WaterParameter;  // Use WaterParameter model instead of WaterReading
use Illuminate\Http\Request;

class WaterReadingController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $data = WaterParameter::all();
        return response()->json($data, 200);
    }
    public function getWaterReadingRange(Request $request)
    {
        $range = $request->input('range');
    
        switch ($range) {
            case 'all':
                $data = WaterParameter::all();
                break;
            case 'daily':
                $data = WaterParameter::whereDate('created_at', Carbon::now()->toDateString())->get();
                break;
            case 'weekly':
                $data = WaterParameter::whereBetween('created_at', [
                    Carbon::now()->startOfWeek(), 
                    Carbon::now()->endOfWeek()
                ])->get();
                break;
            case 'monthly':
                $data = WaterParameter::whereYear('created_at', Carbon::now()->year)
                                      ->whereMonth('created_at', Carbon::now()->month)
                                      ->get();
                break;
            case 'yearly':
                $data = WaterParameter::whereYear('created_at', Carbon::now()->year)->get();
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
        $data = WaterParameter::orderBy('created_at', 'desc')->first();
    
        if (!$data) {
            return response()->json([
                'message' => 'No parameters found'
            ], 404);
        }
    
        // Format created_at as UTC ISO 8601 with microseconds and Z
        $data->created_at = Carbon::parse($data->created_at)->format('Y-m-d\TH:i:s.u\Z');
    
        return response()->json($data);
    }
    public function store(Request $request)
    {
        // Validate incoming data from Arduino
        $validatedData = $request->validate([
            'do' => 'required|numeric',
            'ph' => 'required|numeric',
            'temp' => 'required|numeric',
        ]);
    
        // Store the validated data in the WaterParameter model
        $waterParameter = WaterParameter::create([
            'dissolved_oxygen' => $validatedData['do'],
            'ph_level' => $validatedData['ph'],
            'water_temperature' => $validatedData['temp'],
        ]);
    
        // Detect anomalies (if applicable)
        $this->detectAnomalies($waterParameter);
    
        // Return JSON response
        return response()->json([
            'message' => 'Data received successfully',
            'data' => $waterParameter->load('anomalies') // If anomalies are related to the reading
        ], 200);
    }

    private function detectAnomalies(WaterParameter $parameter)
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

        // Example thresholds for anomaly detection
        if ($parameter->water_temperature > 28) {
            $anomalies[] = [
                'type' => 'water_temperature',
                'value' => $parameter->water_temperature,
                'suggestion' => $messages['water_temperature']['high'],
            ];
        } elseif ($parameter->water_temperature < 22) {
            $anomalies[] = [
                'type' => 'water_temperature',
                'value' => $parameter->water_temperature,
                'suggestion' => $messages['water_temperature']['low'],
            ];
        }

        if ($parameter->dissolved_oxygen < 6) {
            $anomalies[] = [
                'type' => 'dissolved_oxygen',
                'value' => $parameter->dissolved_oxygen,
                'suggestion' => $messages['dissolved_oxygen']['low'],
            ];
        } elseif ($parameter->dissolved_oxygen > 9) {
            $anomalies[] = [
                'type' => 'dissolved_oxygen',
                'value' => $parameter->dissolved_oxygen,
                'suggestion' => $messages['dissolved_oxygen']['high'],
            ];
        }

        if ($parameter->ph_level > 8.5) {
            $anomalies[] = [
                'type' => 'ph_level',
                'value' => $parameter->ph_level,
                'suggestion' => $messages['ph_level']['high'],
            ];
        } elseif ($parameter->ph_level < 6.5) {
            $anomalies[] = [
                'type' => 'ph_level',
                'value' => $parameter->ph_level,
                'suggestion' => $messages['ph_level']['low'],
            ];
        }

        // Save the anomalies to the database
        foreach ($anomalies as $anomaly) {
            Anomalies::create(array_merge($anomaly, ['water_parameter_id' => $parameter->id]));
        }
    }

    // Additional methods for specific time range filters
    public function showReadingPerDay()
    {
        $data = WaterParameter::whereDate('created_at', date('Y-m-d'))->get();
        return response()->json($data, 200);
    }

    public function showReadingPerWeek()
    {
        $data = WaterParameter::whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])->get();
        return response()->json($data, 200);
    }

    public function showReadingPerMonth()
    {
        $data = WaterParameter::whereMonth('created_at', date('m'))->get();
        return response()->json($data, 200);
    }

    public function showReadingPerYear()
    {
        $data = WaterParameter::whereYear('created_at', date('Y'))->get();
        return response()->json($data, 200);
    }

    public function showReadingPerCustomDate(Request $request)
    {
        $data = WaterParameter::whereBetween('created_at', [$request->start_date, $request->end_date])->get();
        return response()->json($data, 200);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $data = WaterParameter::find($id);
        
        if (!$data) {
            return response()->json([
                'message' => 'Data not found'
            ], 404);
        }

        return response()->json($data);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $data = WaterParameter::find($id);

        if (!$data) {
            return response()->json([
                'message' => 'Data not found'
            ], 404);
        }

        $data->delete();

        return response()->json([
            'message' => 'Data deleted successfully'
        ], 200);
    }
}
