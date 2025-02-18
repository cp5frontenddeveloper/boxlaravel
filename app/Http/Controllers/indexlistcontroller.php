<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\indexlist;

class indexlistcontroller extends Controller
{
    public function updateorcreateindex(Request $request)
    {
        $device_id = $request->input("device_id");
        $pointers = $request->input("indexlist");  // Assuming this is the correct input for 'index'
        $indexdate = indexlist::where("device_id", $device_id)->first();

        if ($indexdate) {
            if ($indexdate->pointer !== $pointers) {
                // Update if pointer is different
                $indexdate->update([
                    'pointer' => $pointers  // Use the correct variable here
                ]);
                return response()->json(['message' => 'Pointer updated'], 200);
            } else {
                return response()->json(['message' => 'Pointer and Access Token are up to date'], 200);
            }
        } else {
            // Create new entry if it doesn't exist
            indexlist::create([
                'device_id' => $device_id,
                'pointer' => $pointers  // Ensure the correct value is used here
            ]);
            return response()->json(['message' => 'State lock created'], 201);
        }
    }

    public function getindex($device_id)
    {
        // استرداد بيانات التوكن من خلال user_id
        $indexlists = indexlist::where('device_id', $device_id)->first();

        if ($indexlists) {
            // إذا كانت البيانات موجودة، قم بإرجاعها
            return response()->json([
                'device_id' => $indexlists->device_id,
                'pointer' => $indexlists->pointer,
            ], 200);
        } else {
            // إذا لم تكن البيانات موجودة، قم بإرجاع رسالة خطأ
            return response()->json(['message' => 'No token found for this user'], 404);
        }
    }
}
