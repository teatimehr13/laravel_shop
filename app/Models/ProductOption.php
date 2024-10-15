<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;

class ProductOption extends Model
{
    use HasFactory;
    protected $fillable = ['name', 'image', 'price', 'description', 'enable', 'published_status'];

    static public function findIfEnable($id){
        // $option = self::where([
        //     ['enable',true],
        //     ['id', $id],
        // ])->first();

        $option = self::where('enable', true)->where('id', $id)->first();
        

        //先找到productOptiond的enable為是的目標後
        //再找到關聯的prodcut的published_status為enable
        // Log::info(@$option->product->is_published());
        if($option){
            // Log::info($option->product);
            if(@$option->product->is_published()){
                return $option;
            }
        }
        return null;
    }

    public function product(){
        return $this->belongsTo(Product::class);
    }

    public function orderItems(){
        return $this->hasMany(OrderItem::class);
    }

    public function cartItems(){
        return $this->hasMany(CartItem::class);
    }
}
