<?php

namespace App\Http\Controllers;

use App\Models\Anomalies;

class AnomalyController extends Controller
{
    public function showAnomalyNotifications()
    {
        $data = Anomalies::with('waterParameter')->where('created_at', '>=', now()->subDay())->get();
        return response()->json($data, 200);
    }

    public function deleteAnomaly(string $id)
    {
        $anomaly = Anomalies::find($id);

        if (!$anomaly) {
            return response()->json([
                'message' => 'Anomaly not found'
            ], 404);
        }

        $anomaly->delete();

        return response()->json([
            'message' => 'Anomaly deleted successfully'
        ], 200);
    }

    public function deleteDailyAnomaly()
    {
       Anomalies::whereDate('created_at', date('Y-m-d'))->delete();
        return response()->json([
            'message' => 'Daily anomalies deleted successfully'
        ], 200);
    }

    public function showAnomalies()
    {
        $data = Anomalies::with('waterParameter')->orderBy('created_at', 'desc')->get();
        return response()->json($data, 200);
    }

    public function deleteAllAnomalies()
    {
        Anomalies::truncate();
        return response()->json([
            'message' => 'All anomalies deleted successfully'
        ], 200);
    }
}