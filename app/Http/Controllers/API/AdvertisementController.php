<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Advertisement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Validator;

class AdvertisementController extends Controller
{
    public function index()
    {
        try {
            $ads = Advertisement::where('is_active', true)
                ->latest()
                ->get();

            return response()->json([
                'message' => 'تم جلب الإعلانات بنجاح',
                'data' => $ads
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'حدث خطأ أثناء جلب الإعلانات',
                'error'   => $e->getMessage()
            ], 500);
        }
    }

    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'title' => 'required|string|max:255',
                'description' => 'required|string',
                'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
                'is_active' => 'boolean',
                'start_date' => 'nullable|date',
                'end_date' => 'nullable|date|after_or_equal:start_date',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'message' => 'فشل التحقق من صحة البيانات',
                    'errors' => $validator->errors()
                ], 422);
            }

            if ($request->hasFile('image')) {
                $image = $request->file('image');
                $imageName = time() . '_' . uniqid() . '.' . $image->getClientOriginalExtension();
                
                // التأكد من وجود المجلد العام
                $publicPath = public_path('storage/advertisements');
                if (!File::exists($publicPath)) {
                    File::makeDirectory($publicPath, 0755, true);
                }
                
                // حفظ الصورة مباشرة في المجلد العام
                $image->move($publicPath, $imageName);
                
                // استخدام APP_URL من ملف .env
                $baseUrl = rtrim(config('app.url'), '/');
                
                // تعديل مسار الصورة ليكون صحيحاً مع URL الأساسي
                $imageUrl = $baseUrl . '/storage/advertisements/' . $imageName;

                // إنشاء الإعلان
                $advertisement = Advertisement::create([
                    'title' => $request->title,
                    'description' => $request->description,
                    'image' => $imageUrl,
                    'is_active' => $request->is_active ?? true,
                    'start_date' => $request->start_date,
                    'end_date' => $request->end_date,
                ]);

                \Log::info('تم إنشاء الإعلان بنجاح', [
                    'image_path' => $imageUrl,
                    'public_path' => $publicPath . '/' . $imageName,
                    'public_url' => $imageUrl
                ]);

                return response()->json([
                    'message' => 'تم إنشاء الإعلان بنجاح',
                    'data' => $advertisement
                ], 201);
            }

            return response()->json([
                'errors' => ['image' => ['ملف الصورة مطلوب']]
            ], 400);

        } catch (\Exception $e) {
            \Log::error('فشل إنشاء الإعلان: ' . $e->getMessage());
            return response()->json([
                'message' => 'حدث خطأ أثناء إنشاء الإعلان',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
