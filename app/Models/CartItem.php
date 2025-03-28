<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CartItem extends Model
{
    use HasFactory;

    protected $fillable = ['product_option_id', 'quantity'];

    // protected $casts = [
    //     'productOption' => 'array', // 或者你可以使用其他適合你的格式
    // ];

    public function cart()
    {
        return $this->belongsTo(Cart::class);
    }

    public function productOption()
    {
        return $this->belongsTo(ProductOption::class, 'product_option_id');
    }

    // Laravel Accessor 命名規則
    // subtotal -> getSubtotalAttribute()
    public function getSubtotalAttribute()
    {
        $price = $this->productOption->price ?? $this->productOption->product->price;;
        return $this->quantity * $price;
    }
}
