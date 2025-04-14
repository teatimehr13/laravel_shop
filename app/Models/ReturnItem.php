<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use PDO;

class ReturnItem extends Model
{
    use HasFactory;
    protected $table = 'return_items';

    const MISSING_PARTS       = 'missing_parts';
    const WRONG_PRODUCT       = 'wrong_product';
    const APPEARANCE_DAMAGED  = 'appearance_damaged';
    const FUNCTION_DEFECT     = 'function_defect';
    const NOT_AS_DESCRIBED    = 'not_as_described';
    const OTHER               = 'other';

    protected $fillable = [
        'return_id',
        'order_item_id',
        'quantity',
        'name',
        'unit_price',
        'reason',
        'subtotal',
        'deduct',
        'final_refund',
        'description'
    ];

    private static function returnReasons()
    {
        return [
            self::MISSING_PARTS       => '商品缺件',
            self::WRONG_PRODUCT       => '收到完全不同的商品',
            self::APPEARANCE_DAMAGED  => '商品外表瑕疪 / 毀損',
            self::FUNCTION_DEFECT     => '商品功能有問題',
            self::NOT_AS_DESCRIBED    => '實品與賣場描述 / 圖片有落差',
            self::OTHER               => '其他(商品須以原包裝退還)',
        ];
    }

    public static function returnReasonOptions()
    {
        return collect(self::returnReasons())
            ->map(fn($key, $value) => [
                'label' => $key,
                'value' => $value
            ])
            ->values();
    }

    public function returnRequest()
    {
        return $this->belongsTo(ReturnRequest::class, 'return_id');
    }

    public function orderItem()
    {
        return $this->belongsTo(OrderItem::class);
    }
}


// public static function returnReasonOptions()
// {
//     $options = [];

//     foreach (self::returnReasons() as $value => $label) {
//         $options[] = [
//             'label' => $label,
//             'value' => $value
//         ];
//     }

//     return $options;
// }
