<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductImage extends Model
{
    use HasFactory;
    protected $fillable = ['image', 'alt_text', 'product_id', 'order', 'is_combination'];

    public function product(){
        return $this->belongsTo(Product::class);
    }
}
