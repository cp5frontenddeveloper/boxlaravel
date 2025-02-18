<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\AppUpdate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AppUpdateController extends Controller
{
    public function index()
    {
        return AppUpdate::where('is_active', true)
                       ->orderBy('release_date', 'desc')
                       ->get();
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'version' => 'required|string|unique:app_updates,version',
            'release_date' => 'required|date',
            'changes' => 'required|array',
            'changes.*.type' => 'required|in:feature,improvement,fix,security',
            'changes.*.text' => 'required|string'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 400);
        }

        $update = AppUpdate::create($request->all());

        return response()->json($update, 201);
    }

    public function show($id)
    {
        return AppUpdate::findOrFail($id);
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'version' => 'sometimes|required|string|unique:app_updates,version,' . $id,
            'release_date' => 'sometimes|required|date',
            'changes' => 'sometimes|required|array',
            'changes.*.type' => 'required_with:changes|in:feature,improvement,fix,security',
            'changes.*.text' => 'required_with:changes|string',
            'is_active' => 'sometimes|boolean'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 400);
        }

        $update = AppUpdate::findOrFail($id);
        $update->update($request->all());

        return response()->json($update);
    }

    public function destroy($id)
    {
        $update = AppUpdate::findOrFail($id);
        $update->delete();

        return response()->json(null, 204);
    }

    public function getLatestVersion()
    {
        $latestUpdate = AppUpdate::where('is_active', true)
                                ->orderBy('release_date', 'desc')
                                ->first();

        if (!$latestUpdate) {
            return response()->json(['message' => 'No updates found'], 404);
        }

        return response()->json($latestUpdate);
    }
} 