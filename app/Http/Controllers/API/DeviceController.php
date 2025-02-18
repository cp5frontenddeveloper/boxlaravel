<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Device;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class DeviceController extends Controller
{
        // Display a listing of the mac addresses
        public function index()
        {
            // $devices = Device::all();
            // return response()->json($devices);
            return Device::with(['user', 'codeList'])->get();
        }

        // Store a newly created mac address in storage
        public function store(Request $request)
        {

            $validator = Validator::make($request->all(), [
                'device_name' => 'required|string|max:255',
                'mac_address' => 'required|string|unique:devices',
                'user_id' => 'required|integer|exists:users,id',
                'usercode' => 'required|string|size:4|regex:/^[0-9]+$/',
                'site_data' => 'nullable|string|max:500',
                'location_name' => 'nullable|string|max:255',
                'latitude' => 'nullable|numeric|between:-90,90',
                'longitude' => 'nullable|numeric|between:-180,180',
            ]);

            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()], 400);
            }

            // $device = Device::create([
            //     'device_name' => $request->device_name,
            //     'mac_address' => $request->mac_address,
            //     'user_id' => $request->user_id,
            // ]);

            $device = Device::create($request->all());

            return response()->json($device, 201);
        }

        // Display the specified mac address
        public function show($userId)
        {
            // ابحث عن جميع الأجهزة المرتبطة بالمستخدم بواسطة user_id
            $devices = Device::where('user_id', $userId)->get();

            if ($devices->isEmpty()) {
                return response()->json(['error' => 'No devices found for this user'], 404);
            }

            return response()->json($devices);
        }


        // Update the specified mac address in storage
        public function update(Request $request, $id)
    {
        $device = Device::find($id);

        if (!$device) {
            return response()->json(['error' => 'Device not found'], 404);
        }

        $validator = Validator::make($request->all(), [
            'device_name' => 'sometimes|required|string|max:255',
            'usercode' => 'sometimes|required|string|size:4|regex:/^[0-9]+$/',
            'site_data' => 'nullable|string|max:500',
            'location_name' => 'nullable|string|max:255',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 400);
        }

        $device->update($request->only([
            'device_name', 
            'usercode', 
            'site_data',
            'location_name',
            'latitude',
            'longitude'
        ]));

        return response()->json(["state" => 'successful']);
    }

        // Remove the specified mac address from storage
        public function destroy($id)
        {
            $device = Device::find($id);

            if (!$device) {
                return response()->json(['error' => 'Device not found'], 404);
            }

            $device->delete();

            return response()->json(['message' => 'Device deleted successfully']);
        }
        public function getDeviceCountByUserId($userId)
        {
            // ابحث عن جميع الأجهزة المرتبطة بالمستخدم بواسطة user_id
            $deviceCount = Device::where('user_id', $userId)->count();

            if ($deviceCount == 0) {
                return response()->json(['error' => 'No devices found for this user'], 404);
            }

            return response()->json(['device_count' => $deviceCount], 200);
        }
}