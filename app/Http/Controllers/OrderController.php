<?php
namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\BoxInventory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB; // إضافة هذا السطر مهم جداً
class OrderController extends Controller
{
    public function index(Request $request)
    {
        if ($request->user() && $request->user()->getTable() === 'representatives') {
            return Order::with(['client', 'boxType'])
                ->where('representative_id', $request->user()->id)
                ->get();
        }
        return Order::with(['client', 'boxType', 'representative'])->get();
    }

    public function getRepresentativeOrders($representativeId)
    {
        return Order::with(['client', 'boxType'])
            ->where('representative_id', $representativeId)
            ->get();
    }

    public function store(Request $request)
    {
        try {
            DB::beginTransaction();

            $validated = $request->validate([
                'client_id' => 'required|exists:clients,id',
                'representative_id' => 'required|exists:representatives,id',
                'box_type_id' => 'required|exists:box_types,id',
                'quantity' => 'required|integer|min:1',
                'receipt_date' => 'required|date',
                'receipt_method' => 'required|string',
                'price' => 'required|numeric|min:0',
                'notes' => 'nullable|string'
            ]);

            // التحقق من المخزون المتوفر
            $availableQuantity = BoxInventory::getAvailableQuantity($validated['box_type_id']);
            
            if ($availableQuantity < $validated['quantity']) {
                throw new \Exception("الكمية المطلوبة غير متوفرة. الكمية المتاحة: {$availableQuantity}");
            }

            // خصم من المخزون
            BoxInventory::deductStock($validated['box_type_id'], $validated['quantity']);

            // إنشاء الطلب
            $order = Order::create($validated);

            DB::commit();
            return $order->load(['client', 'boxType', 'representative']);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => $e->getMessage()
            ], 422);
        }
    }

    public function show(Order $order)
    {
        return $order->load(['client', 'boxType', 'representative']);
    }

    public function update(Request $request, Order $order)
    {
        try {
            DB::beginTransaction();

            $validated = $request->validate([
                'client_id' => 'sometimes|exists:clients,id',
                'representative_id' => 'sometimes|exists:representatives,id',
                'box_type_id' => 'sometimes|exists:box_types,id',
                'quantity' => 'sometimes|integer|min:1',
                'receipt_date' => 'sometimes|date',
                'receipt_method' => 'sometimes|string',
                'price' => 'sometimes|numeric|min:0',
                'is_completed' => 'sometimes|boolean',
                'notes' => 'nullable|string'
            ]);

            // التعامل مع تغيير الكمية
            if (isset($validated['quantity']) && $validated['quantity'] !== $order->quantity) {
                if ($validated['quantity'] > $order->quantity) {
                    // طلب كمية إضافية
                    $additionalQuantity = $validated['quantity'] - $order->quantity;
                    $availableQuantity = BoxInventory::getAvailableQuantity($order->box_type_id);
                    
                    if ($availableQuantity < $additionalQuantity) {
                        throw new \Exception("الكمية الإضافية غير متوفرة. الكمية المتاحة: {$availableQuantity}");
                    }
                    
                    BoxInventory::deductStock($order->box_type_id, $additionalQuantity);
                } else {
                    // إرجاع الكمية الزائدة للمخزون
                    $returnQuantity = $order->quantity - $validated['quantity'];
                    BoxInventory::addStock($order->box_type_id, $returnQuantity);
                }
            }

            $order->update($validated);
            
            DB::commit();
            return $order->load(['client', 'boxType', 'representative']);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => $e->getMessage()
            ], 422);
        }
    }

    public function destroy(Order $order)
    {
        try {
            DB::beginTransaction();

            // إرجاع الكمية للمخزون
            BoxInventory::addStock($order->box_type_id, $order->quantity);

            $order->delete();

            DB::commit();
            return response()->noContent();

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => $e->getMessage()
            ], 422);
        }
    }
}