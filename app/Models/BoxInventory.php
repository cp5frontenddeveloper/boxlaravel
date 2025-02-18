<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BoxInventory extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'inventory_boxes';

    protected $fillable = [
        'invoice_number',
        'workshop_id',
        'box_type_id',
        'box_status_id',
        'quantity',
        'received_quantity',
        'order_date',
        'actual_delivery_date',
        'notes'
    ];

    protected $dates = [
        'order_date',
        'actual_delivery_date',
        'deleted_at'
    ];

    public static function getAvailableQuantity($boxTypeId)
    {
        return self::where('box_type_id', $boxTypeId)
            ->where('box_status_id', 1) // نفترض أن 1 هي حالة المتاح
            ->sum('quantity');
    }

    public static function deductStock($boxTypeId, $quantity)
    {
        $inventories = self::where('box_type_id', $boxTypeId)
            ->where('box_status_id', 1)
            ->where('quantity', '>', 0)
            ->orderBy('order_date')
            ->get();

        $remainingQuantity = $quantity;
        $updatedInventories = [];

        foreach ($inventories as $inventory) {
            if ($remainingQuantity <= 0) break;

            $deductAmount = min($inventory->quantity, $remainingQuantity);
            $inventory->quantity -= $deductAmount;
            $remainingQuantity -= $deductAmount;
            
            $inventory->save();
            $updatedInventories[] = $inventory;
        }

        if ($remainingQuantity > 0) {
            throw new \Exception('الكمية المطلوبة غير متوفرة في المخزون');
        }

        return $updatedInventories;
    }

    public static function addStock($boxTypeId, $quantity)
    {
        $inventory = self::where('box_type_id', $boxTypeId)
            ->where('box_status_id', 1)
            ->orderBy('order_date', 'desc')
            ->first();

        if ($inventory) {
            $inventory->quantity += $quantity;
            $inventory->save();
        } else {
            $inventory = self::create([
                'box_type_id' => $boxTypeId,
                'box_status_id' => 1,
                'quantity' => $quantity,
                'invoice_number' => 'INV-' . date('Y-m') . '-' . rand(1000, 9999),
                'order_date' => now(),
                'notes' => 'إضافة مخزون من إلغاء طلب'
            ]);
        }

        return $inventory;
    }

    public function boxType()
    {
        return $this->belongsTo(BoxType::class);
    }

    public function boxStatus()
    {
        return $this->belongsTo(BoxStatus::class);
    }

    public function workshop()
    {
        return $this->belongsTo(Workshop::class);
    }
}
