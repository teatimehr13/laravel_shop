<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use PDO;

class Order extends Model
{
    use HasFactory;

    const NOT_SELECTED_PAYMENT = 'not_selected_payment';
    const WAITING_FOR_THE_TRANSFER = 'wating_for_the_transfer';
    const PAID = 'paid';
    const SEND_BEFORE_PAID = 'send_before_paid';
    const CANCELLED = 'cancelled';

    const STATUS_PENDING = 'pending';  //0
    const STATUS_PAID = 'paid'; //1
    const STATUS_PROCESSING = 'processing';  //2
    const STATUS_SHIPPED = 'shipped'; //3
    const STATUS_DELIVERED = 'delivered'; //4
    const STATUS_COMPLETED = 'completed'; //5
    const STATUS_CANCELLED = 'cancelled'; //6
    const STATUS_REFUNDED = 'refunded'; //7
    const STATUS_PARTIAL_REFUNDED = 'partial_refunded'; //8


    const PAYMENT_CASH = 'cash';
    const PAYMENT_CREDIT_CARD = 'credit_card';
    const PAYMENT_LINE_PAY = 'line_pay';
    const PAYMENT_ATM = 'atm';
    const PAYMENT_CVS = 'cvs';

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
        'user_id',
        'payment_method',
        'payment_status',
        'fulfilment_status',
        'payment_token'
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

    //給前台使用者看的
    public static function orderStatusStepMap(): array
    {
        return [
            0 => 0, // 訂單成立
            1 => 1, // 已付款
            2 => 1, // 處理中 也屬於付款後狀態
            3 => 2, // 出貨中
            4 => 3, // 已送達
            5 => 3, // 已完成 → 當成已送達
            6 => 3, // 取消 → 歸類為已結束
            7 => 3, // 已退款 → 同上
            8 => 3, // 部分退款 → 同上
        ];
    }

    public static function paymentMethodOptions()
    {
        return [
            self::PAYMENT_CASH => '貨到付款',
            self::PAYMENT_CREDIT_CARD => '信用卡',
            self::PAYMENT_LINE_PAY => 'LINE Pay',
            self::PAYMENT_ATM => 'ATM 轉帳',
            self::PAYMENT_CVS => '超商繳費',
        ];
    }

    public static function statusChineseLabels()
    {
        return [
            self::STATUS_PENDING => '待付款',
            self::STATUS_PAID => '已付款',
            self::STATUS_PROCESSING => '處理中',
            self::STATUS_SHIPPED => '已出貨',
            self::STATUS_DELIVERED => '已送達',
            self::STATUS_COMPLETED => '已完成',
            self::STATUS_CANCELLED => '已取消',
            self::STATUS_REFUNDED => '已退款',
            self::STATUS_PARTIAL_REFUNDED => '部分退款',
        ];
    }

    public static function statusKeys()
    {
        return [
            0 => self::STATUS_PENDING,
            1 => self::STATUS_PAID,
            2 => self::STATUS_PROCESSING,
            3 => self::STATUS_SHIPPED,
            4 => self::STATUS_DELIVERED,
            5 => self::STATUS_COMPLETED,
            6 => self::STATUS_CANCELLED,
            7 => self::STATUS_REFUNDED,
            8 => self::STATUS_PARTIAL_REFUNDED,
        ];
    }

    public static function orderStatusSelect()
    {
        $keys = self::statusKeys();
        $labels = self::statusChineseLabels();

        $result = [];
        foreach ($keys as $index => $statusKey) {
            $result[$index] = $labels[$statusKey] ?? $statusKey;
        }

        return $result;
    }


    //會輸出 order_status_labe的屬性，也可直接寫在controller
    // $key = Order::statusKeys()[$order->order_status] ?? null;
    // $order->order_status_label = Order::orderStatusChineseLabels()[$key] ?? '未知狀態';
    public function getOrderStatusLabelAttribute()
    {
        $key = self::statusKeys()[$this->order_status] ?? null;
        return self::statusChineseLabels()[$key] ?? '';
    }


    public function getStepAttribute(): int
    {
        // if (in_array($this->fulfilment_status, ['cancelled', 'returned'])) {
        //     return 3; // 特殊結束狀態 → 統一當成 step 3
        // }

        if ($this->fulfilment_status === 'delivered') {
            return 3;
        }

        if ($this->fulfilment_status === 'shipped') {
            return 2;
        }

        if ($this->payment_status === 'paid') {
            return 1;
        }

        if ($this->fulfilment_status === 'cancelled') {
            return -1; 
        }
        if ($this->fulfilment_status === 'returned') {
            return -2;
        }


        return 0; // 預設為訂單剛成立
    }

    public static function paymentStatusLabels(): array
    {
        return [
            'pending'        => '待付款',
            'paid'           => '已付款',
            'refunded'       => '已退款',
            'partial_refund' => '部分退款',
        ];
    }

    public static function fulfilmentStatusLabels(): array
    {
        return [
            'pending'    => '待出貨',
            'processing' => '處理中',
            'shipped'    => '配送中',
            'delivered'  => '已送達',
            'cancelled'  => '已取消',
            'returned'   => '已退貨',
        ];
    }

    public function getPaymentStatusLabelAttribute(): string
    {
        return self::paymentStatusLabels()[$this->payment_status] ?? $this->payment_status;
    }

    public function getFulfilmentStatusLabelAttribute(): string
    {
        return self::fulfilmentStatusLabels()[$this->fulfilment_status] ?? $this->fulfilment_status;
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

    //一個訂單可以被退多次
    public function returns()
    {
        return $this->hasMany(ReturnRequest::class);
    }
}
