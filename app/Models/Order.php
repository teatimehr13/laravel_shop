<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    const NOT_SELECTED_PAYMENT = 'not_selected_payment';
    const WAITING_FOR_THE_TRANSFER = 'wating_for_the_transfer';
    const PAID = 'paid';
    const SEND_BEFORE_PAID = 'send_before_paid';
    const CANCELLED = 'cancelled';

    const orderStatuses = [
        0 => self::NOT_SELECTED_PAYMENT,
        1 => self::WAITING_FOR_THE_TRANSFER,
        2 => self::PAID,
        3 => self::SEND_BEFORE_PAID,
        4 => self::CANCELLED
    ];

    protected $fillable = [
        'order_number',
        'payment_order_number',
        'order_status',
        'amount',
        'address',
        'user_id'
    ];

    //初始化模型事件，當Order被new時，自動做這些事
    protected static function boot()
    {
        //預先執行父類別
        parent::boot();

        //模型事件處理器，當資料被建立之前，自動執行的邏輯。 (create() or save())
        //$query代表即將被寫入的資料模型實例，和this相似
        static::creating(function ($query) {
            $query->order_number = $query->order_number ?? 'LPB' . now()->timestamp;
            $query->order_status = $query->order_status ?? Order::orderStatusesIndex(Order::NOT_SELECTED_PAYMENT);
            $query->payment_order_number = $query->payment_order_number ?? '';
        });
    }

    public static function orderStatusesIndex($targetName)
    {
        foreach (self::orderStatuses as $index => $name) {
            if ($targetName == $name) {
                return $index;
            }
        }
        return null;
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }

    //避免暴露id，改用order_number作為路由的依據
    public function getRouteKeyName()
    {
        return 'order_number';
    }
}
