<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Models\ReservationAnalysis;

class InterfacingController extends Controller
{
    /**
     * Receive parsed results from the Mindray BC-3000 local agent.
     */
    public function receiveMindrayResults(Request $request)
    {
        // Add basic security or token auth later if needed, since it's local it's somewhat safe
        $sampleId = $request->input('sample_id');
        $results = $request->input('results'); // Expected array of parsed parameters e.g., ['WBC' => 5.5, 'RBC' => 4.2...]

        if (!$sampleId || empty($results)) {
            return response()->json(['error' => 'Invalid payload. Missing sample_id or results.'], 400);
        }

        try {
            // Find the pending Reservation Analysis that matches this Sample ID.
            // Assuming the operator enters the Reservation ID or Request ID as the "Sample ID" in the Mindray machine.
            $resAnalyses = ReservationAnalysis::with('analyse')
                ->where('reservation_id', $sampleId) // or whatever field the Sample ID maps to
                ->orWhere('id', $sampleId)
                ->get();

            if ($resAnalyses->isEmpty()) {
                return response()->json(['error' => 'No analysis record found for this Sample ID.'], 404);
            }

            // Find the FNS/NFS/CBC Analysis within this Reservation
            // Usually we look for keyword 'FNS', 'NFS', 'CBC' in the analysis name
            $targetAnalysis = $resAnalyses->filter(function($ra) {
                $name = strtolower($ra->analyse->name ?? '');
                return strpos($name, 'fns') !== false || strpos($name, 'nfs') !== false || strpos($name, 'cbc') !== false;
            })->first();

            if (!$targetAnalysis) {
                // Fallback to the first available if not explicitly named FNS
                $targetAnalysis = $resAnalyses->first();
            }

            // Format the Mindray parameters into a readable text block for the result_value
            $formattedResult = "--- Mindray BC-3000 Auto-Result ---\n";
            foreach ($results as $param => $val) {
                $formattedResult .= "$param: $val\n";
            }

            // Update the clinical results if found
            if ($targetAnalysis) {
                $targetAnalysis->update([
                    'result_value' => $formattedResult,
                    'clinical_status' => 'Pending Review (Auto)', // Mark it for doctor review
                ]);
            }

            Log::info("Analyses found: " . count($resAnalyses));
            Log::info("Interfacing: Mindray BC-3000 results successfully saved for Sample ID {$sampleId}");

            return response()->json([
                'success' => true,
                'message' => 'Results saved successfully.',
                'reservation_analysis_id' => $targetAnalysis?->id
            ]);

        } catch (\Exception $e) {
            Log::error("Interfacing Error (Mindray BC-3000): " . $e->getMessage());
            return response()->json(['error' => 'Internal server error processing results.'], 500);
        }
    }
}
