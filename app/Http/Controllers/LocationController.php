<?php

namespace App\Http\Controllers;

use App\Models\country;
use Illuminate\Http\Request;

class LocationController extends Controller {
    public function countries(Request $request) {
        $query = country::with(['states'])->select('id', 'name')->orderBy('name')->get();
        if ($request->country_id) {
            $countries = $query->where('id', $request->country_id)->get();
        } else {
            $countries = $query;
        }
        
        try {
            return response()->json([
                'success' => true,
                'message' => 'Countries data fetched',
                'result' => $query,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch countries',
                'result' => $e->getMessage()
            ], 500);
        }
    }
}
