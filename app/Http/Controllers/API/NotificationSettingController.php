<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\NotificationSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class NotificationSettingController extends Controller
{
    public function show($userId)
    {
        $settings = NotificationSetting::firstOrCreate(
            ['user_id' => $userId]
        );

        return response()->json($settings);
    }

    public function update(Request $request, $userId)
    {
        $validator = Validator::make($request->all(), [
            'door_open' => 'boolean',
            'door_left_open' => 'boolean',
            'lock_status' => 'boolean',
            'battery' => 'boolean',
            'internet' => 'boolean',
            'tamper' => 'boolean',
            'notification_sound' => 'string',
            'vibration_enabled' => 'boolean'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 400);
        }

        $settings = NotificationSetting::updateOrCreate(
            ['user_id' => $userId],
            $request->all()
        );

        return response()->json($settings);
    }
}
