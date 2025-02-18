<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Lockfinger;
class LockfingerController extends Controller
{
    public function updateorcreatestatelock(Request $request)
    {
        $device_id = $request->input("device_id");
        $state_lock = $request->input("state_lock");
        $lockdate = Lockfinger::where("device_id",$device_id)->first();
        if($lockdate)
        {
            if($lockdate->statelock !== $state_lock)
                {
                    $lockdate->update([
                        'statelock'=>$state_lock
                    ]);
                    return response()->json(['message' => ' statelock updated'], 200);
                }else{
                    return response()->json(['message' => 'Token and Access Token are up to date'], 200);
                }
        }else{
            Lockfinger::create([
                'device_id'=>$device_id,
                'statelock'=>$state_lock
            ]);
            return response()->json(['message'=>'state lock create']);
        }
    }
    public function getlockstate($device_id)
    {
        // استرداد بيانات التوكن من خلال user_id
        $userToken = Lockfinger::where('device_id', $device_id)->first();

        if ($userToken) {
            // إذا كانت البيانات موجودة، قم بإرجاعها
            return response()->json([
                'device_id' => $userToken->device_id,
                'statelock' => $userToken->statelock,
            ], 200);
        } else {
            // إذا لم تكن البيانات موجودة، قم بإرجاع رسالة خطأ
            return response()->json(['message' => 'No token found for this user'], 404);
        }
    }
}
