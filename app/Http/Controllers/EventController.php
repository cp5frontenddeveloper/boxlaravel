<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Event;

class EventController extends Controller
{
    public function index()
    {
        $events = Event::all();
        return response()->json($events);
    }

    public function store(Request $request)
    {
        try {
            $validatedData = $request->validate([
                "device_id"      => 'required|exists:devices,id|integer',
                "event_type"     => 'required|string|max:255',
                "event_data"     => 'required|array',
                "description"    => 'nullable|string|max:500',
                "status"         => 'nullable|string|max:50',
                "timestamp"      => 'required|date',
                "usertype"       => 'required|string|max:255',
                "opening_method" => 'required|string|in:fingerprint,admin,temp_code,subuser'
            ]);

            if (!isset($validatedData['timestamp']) && isset($validatedData['event_data']['timestamp'])) {
                $validatedData['timestamp'] = $validatedData['event_data']['timestamp'];
            }

            if (!isset($validatedData['usertype'])) {
                $validatedData['usertype'] = 'system';
            }

            $event = Event::create([
                'device_id'      => $validatedData['device_id'],
                'event_type'     => $validatedData['event_type'],
                'event_data'     => $validatedData['event_data'],
                'description'    => $validatedData['description'] ?? null,
                'status'        => $validatedData['status'] ?? 'active',
                'timestamp'     => $validatedData['timestamp'],
                'usertype'      => $validatedData['usertype'],
                'opening_method' => $validatedData['opening_method']
            ]);

            return response()->json([
                'message' => 'Event created successfully',
                'event'   => $event
            ], 201);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'message' => 'Validation failed',
                'errors'  => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Something went wrong',
                'error'   => $e->getMessage()
            ], 500);
        }
    }

    public function show(Request $request, $id)
    {
        try {
            $query = Event::query();
            
            if (!$id) {
                return response()->json([
                    'message' => 'معرف الجهاز مطلوب'
                ], 400);
            }

            $query->where('device_id', $id)
                  ->with('device');

            // تصفية حسب التاريخ
            if ($request->has('start_date') && $request->has('end_date')) {
                $query->whereBetween('timestamp', [
                    $request->start_date,
                    $request->end_date
                ]);
            }

            // تصفية حسب نوع الفتح
            if ($request->has('opening_method')) {
                $query->where('opening_method', $request->opening_method);
            }

            // تصفية حسب نوع المستخدم
            if ($request->has('usertype')) {
                $query->where('usertype', $request->usertype);
            }

            $events = $query->latest('timestamp')->get();

            if ($events->isEmpty()) {
                return response()->json([
                    'message' => 'لم يتم العثور على أحداث لهذا الجهاز',
                    'device_id' => $id
                ], 404);
            }

            // تحويل البيانات وإضافة اسم المستخدم
            $events = $events->map(function ($event) {
                $data = $event->toArray();
                $subuser = $event->getSubuserAttribute();
                
                if ($subuser) {
                    unset($data['usertype']);
                    $data['user'] = [
                        'name' => $subuser->username,
                        'email' => $subuser->email,
                        'phone' => $subuser->phone
                    ];
                } else {
                    unset($data['usertype']);
                    $data['user'] = [
                        'name' => 'admin',
                        'email' => null,
                        'phone' => null
                    ];
                }
                
                return $data;
            });

            return response()->json([
                'message' => 'تم استرجاع الأحداث بنجاح!',
                'events' => $events,
                'device_id' => $id,
                'total_count' => $events->count()
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'حدث خطأ أثناء استرجاع البيانات',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function update()
    {
        // Implement the update logic
    }

    public function destroy()
    {
        // Implement the destroy logic
    }
}
