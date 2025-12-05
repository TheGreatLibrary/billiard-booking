<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\DB;

class Resource extends Model
{
    public $timestamps = false;
    
    protected $fillable = [
        'model_id', 
        'place_id', 
        'zone_id',
        'code', 
        'state_id', 
        'note',
        'grid_x', 
        'grid_y', 
        'grid_width', 
        'grid_height', 
        'rotation',
        'quantity',  // ← НОВОЕ
        'type'       // ← НОВОЕ (table/equipment)
    ];

    protected $casts = [
        'quantity' => 'integer',
        'grid_x' => 'integer',
        'grid_y' => 'integer',
        'grid_width' => 'integer',
        'grid_height' => 'integer',
        'rotation' => 'integer',
    ];

    // ==========================================
    // RELATIONSHIPS
    // ==========================================
    
    public function productModel(): BelongsTo
    {
        return $this->belongsTo(ProductModel::class, 'model_id');
    }

    public function place(): BelongsTo
    {
        return $this->belongsTo(Place::class);
    }

    public function zone(): BelongsTo
    {
        return $this->belongsTo(Zone::class);
    }

    public function state(): BelongsTo
    {
        return $this->belongsTo(StateProduct::class, 'state_id');
    }

    // ==========================================
    // TYPE CHECKING
    // ==========================================
    
    /**
     * Это физический стол с координатами
     */
    public function isTable(): bool
    {
        return $this->type === 'table';
    }

    /**
     * Это инвентарь (кий, мел, перчатка и т.д.)
     */
    public function isEquipment(): bool
    {
        return $this->type === 'equipment';
    }

    /**
     * Имеет ли ресурс координаты на карте
     */
    public function hasCoordinates(): bool
    {
        return $this->grid_x !== null && $this->grid_y !== null;
    }

    // ==========================================
    // AVAILABILITY LOGIC
    // ==========================================
    
    /**
     * Получить доступное количество для конкретного слота
     * 
     * @param string $slotDate Дата слота (YYYY-MM-DD)
     * @param string $slotTime Время слота (HH:00)
     * @return int Доступное количество
     */
    public function getAvailableQuantity(string $slotDate, string $slotTime): int
    {
        if ($this->isTable()) {
            // Для столов: либо занят (0), либо свободен (1)
            $isBooked = DB::table('bookings')
                ->join('booking_slots', 'bookings.id', '=', 'booking_slots.booking_id')
                ->where('bookings.resource_id', $this->id)
                ->where('booking_slots.slot_date', $slotDate)
                ->where('booking_slots.slot_time', $slotTime)
                ->whereIn('bookings.status', ['pending', 'confirmed'])
                ->exists();
            
            return $isBooked ? 0 : 1;
        }
        
        // Для инвентаря: общее количество минус забронированное
        $bookedQty = DB::table('booking_equipment')
            ->join('bookings', 'booking_equipment.booking_id', '=', 'bookings.id')
            ->join('booking_slots', 'bookings.id', '=', 'booking_slots.booking_id')
            ->where('booking_equipment.product_model_id', $this->model_id)
            ->where('booking_slots.slot_date', $slotDate)
            ->where('booking_slots.slot_time', $slotTime)
            ->whereIn('bookings.status', ['pending', 'confirmed'])
            ->sum('booking_equipment.qty');
        
        return max(0, $this->quantity - $bookedQty);
    }

    /**
     * Проверить доступность для массива слотов
     * 
     * @param array $slots [['date' => '2025-12-05', 'time' => '12:00'], ...]
     * @param int $requestedQty Запрашиваемое количество
     * @return bool
     */
    public function isAvailableForSlots(array $slots, int $requestedQty = 1): bool
    {
        foreach ($slots as $slot) {
            $available = $this->getAvailableQuantity($slot['date'], $slot['time']);
            if ($requestedQty > $available) {
                return false;
            }
        }
        return true;
    }

    /**
     * Получить минимальное доступное количество среди всех слотов
     * 
     * @param array $slots
     * @return int
     */
    public function getMinAvailableQuantity(array $slots): int
    {
        $min = PHP_INT_MAX;
        
        foreach ($slots as $slot) {
            $available = $this->getAvailableQuantity($slot['date'], $slot['time']);
            $min = min($min, $available);
        }
        
        return $min === PHP_INT_MAX ? $this->quantity : $min;
    }

    // ==========================================
    // QUERY SCOPES
    // ==========================================
    
    /**
     * Только столы
     */
    public function scopeTables($query)
    {
        return $query->where('type', 'table');
    }

    /**
     * Только инвентарь
     */
    public function scopeEquipment($query)
    {
        return $query->where('type', 'equipment');
    }

    /**
     * Только активные ресурсы
     */
    public function scopeActive($query)
    {
        return $query->whereHas('state', function($q) {
            $q->where('name', 'active');
        });
    }

    /**
     * Ресурсы с координатами (для карты)
     */
    public function scopeWithCoordinates($query)
    {
        return $query->whereNotNull('grid_x')
                     ->whereNotNull('grid_y');
    }

    /**
     * Инвентарь, доступный для бронирования
     */
    public function scopeAvailableEquipment($query)
    {
        return $query->equipment()
                     ->where('quantity', '>', 0)
                     ->active();
    }

    // ==========================================
    // HELPER METHODS
    // ==========================================
    
    /**
     * Получить цену за единицу (для equipment)
     */
    public function getPriceEach(): int
    {
        return $this->productModel->base_price_each ?? 0;
    }

    /**
     * Получить цену за час (для table)
     */
    public function getPriceHour(): int
    {
        return $this->productModel->base_price_hour ?? 0;
    }

    /**
     * Строковое представление
     */
    public function getDisplayName(): string
    {
        $model = $this->productModel->name ?? 'Unknown';
        $code = $this->code ? " ({$this->code})" : '';
        
        if ($this->isEquipment()) {
            return "{$model} (x{$this->quantity}){$code}";
        }
        
        return "{$model}{$code}";
    }
}