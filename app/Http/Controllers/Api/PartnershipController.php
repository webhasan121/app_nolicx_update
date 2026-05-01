<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\DeveloperAccess;
use App\Models\ManagementAccess;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class PartnershipController extends Controller
{
    public function developer(Request $request)
    {
        return $this->statusResponse($request, DeveloperAccess::class, 'developerRequest');
    }

    public function applyDeveloper(Request $request)
    {
        return $this->apply($request, DeveloperAccess::class, 'Developer application submitted');
    }

    public function management(Request $request)
    {
        return $this->statusResponse($request, ManagementAccess::class, 'managementRequest');
    }

    public function applyManagement(Request $request)
    {
        return $this->apply($request, ManagementAccess::class, 'Management application submitted');
    }

    private function statusResponse(Request $request, string $modelClass, string $requestKey)
    {
        $user = $request->user();
        $application = $modelClass::where('applied_id', $user->id)->first();

        return response()->json([
            'success' => true,
            'message' => 'Partnership status fetched',
            'data' => [
                'name' => $user->name,
                'email' => $user->email,
                'phone' => $user->phone,
                'hasApplied' => (bool) $application,
                $requestKey => $application ? $this->applicationPayload($application) : null,
            ],
        ]);
    }

    private function apply(Request $request, string $modelClass, string $message)
    {
        $validated = $request->validate([
            'message' => ['nullable', 'string', 'max:500'],
        ]);

        $exists = $modelClass::where('applied_id', $request->user()->id)->exists();

        if ($exists) {
            return response()->json([
                'success' => false,
                'message' => 'You already applied!',
                'errors' => null,
            ], 409);
        }

        $application = $modelClass::create([
            'applied_id' => $request->user()->id,
            'message' => $validated['message'] ?? null,
            'status' => null,
        ]);

        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => $this->applicationPayload($application),
        ], 201);
    }

    private function applicationPayload(Model $application): array
    {
        return [
            'id' => $application->id,
            'status' => $application->status,
            'message' => $application->message,
            'commission' => $application->commission,
            'created_at' => $application->created_at?->toDateTimeString(),
        ];
    }
}
