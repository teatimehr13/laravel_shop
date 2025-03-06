<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Subcategory extends Model
{
    use HasFactory;

    protected $fillable = ['name','search_key','order_index','show_in_list'];

    public function products(){
        return $this->hasMany(Product::class);
    }

    public function category(){
        return $this->belongsTo(Category::class);
    }

    //抓到對應的Product裡第一張圖，因為subcategories資料表沒存圖
    public function firstProductImage(){
        return $this->hasOne(Product::class)->select('id', 'subcategory_id', 'image')->orderBy('id');
    }
}
