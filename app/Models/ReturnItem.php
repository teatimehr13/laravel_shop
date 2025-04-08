<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use PDO;

class ReturnItem extends Model
{
    use HasFactory;
    protected $table = 'return_items';

    protected $fillable = [
        'return_id',
        'order_item_id',
        'quantity',
        'reason',
    ];

    public function returnRequest(){
        return $this->belongsTo(ReturnRequest::class, 'return_id');
    }

    public function orderItem(){
        return $this->belongsTo(OrderItem::class);
    }
}
