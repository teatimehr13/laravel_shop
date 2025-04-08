<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use PhpParser\Node\Stmt\Return_;

class ReturnRequest extends Model
{
    use HasFactory;

    protected $table = 'returns';
    protected $fillable = [
        'order_id',
        'return_number',
        'status'
    ];

    //一個退貨單有多個退貨商品
    public function returnItems(){
        return $this->hasMany(ReturnItem::class, 'return_id');
    }

    //一個退貨單屬於一張訂單
    public function order(){
        return $this->belongsTo(Order::class);
    }
}
