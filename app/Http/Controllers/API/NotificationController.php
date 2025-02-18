<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use App\Models\NotificationSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Events\NewNotificationEvent;

class NotificationController extends Controller
{
    public function index(Request $request)
    {
        $notifications = Notification::where('user_id', $request->user_id)
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json($notifications);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'device_id' => 'required|exists:devices,id',
            'user_id' => 'required|exists:users,id',
            'type' => 'required|in:door_open,door_left_open,lock_status,battery,internet,tamper',
            'message' => 'required|string'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors()
            ], 400);
        }

        $notification = Notification::create($request->all());
        
        // إرسال الإشعار
        event(new NewNotificationEvent($notification));

        return response()->json($notification, 201);
    }

    public function markAsRead($id)
    {
        $notification = Notification::findOrFail($id);
        $notification->update(['is_read' => true]);

        return response()->json(['message' => 'Notification marked as read']);
    }

    public function markAllAsRead(Request $request)
    {
        Notification::where('user_id', $request->user_id)
            ->update(['is_read' => true]);

        return response()->json(['message' => 'All notifications marked as read']);
    }

    public function destroy($id)
    {
        $notification = Notification::findOrFail($id);
        $notification->delete();

        return response()->json(['message' => 'Notification deleted']);
    }

    public function update(Request $request, $id)
    {
        $notification = Notification::findOrFail($id);
        
        $validator = Validator::make($request->all(), [
            'device_id' => 'exists:devices,id',
            'user_id' => 'exists:users,id',
            'type' => 'in:door_open,door_left_open,lock_status,battery,internet,tamper',
            'message' => 'string',
            'is_read' => 'boolean'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors()
            ], 400);
        }

        $notification->update($request->all());

        // إذا تم تحديث الإشعار، نقوم بإرسال حدث جديد
        event(new NewNotificationEvent($notification));

        return response()->json([
            'message' => 'Notification updated successfully',
            'notification' => $notification
        ]);
    }
}
