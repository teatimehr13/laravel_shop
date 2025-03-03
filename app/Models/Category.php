<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class Category extends Model
{
    use HasFactory;
    protected $fillable = ['name','search_key','order_index','show_in_list'];

    public function subcategories(){
        return $this->hasMany(Subcategory::class, 'category_id', 'id');
    }

    public function products(){
        //透過subcategory找到product
        return $this->hasManyThrough(Product::class, Subcategory::class); 
    }

    public static function category_asc(){
        return self::orderBy('order_index', 'ASC');
    }
}
