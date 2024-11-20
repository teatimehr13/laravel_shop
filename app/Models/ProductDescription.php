<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductDescription extends Model
{
    use HasFactory;
    protected $fillable = ['description', 'type', 'order', 'link_text', 'link_url',
                            'promo_start_date', 'promo_end_date'];
                            
    public function product(){
        return $this->belongsTo(Product::class);
    }
}
