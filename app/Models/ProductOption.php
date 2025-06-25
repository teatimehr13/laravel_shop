<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class ProductOption extends Model
{
    use HasFactory;
    // protected $fillable = ['name', 'image', 'price', 'description', 'enable', 'published_status'];
    protected $fillable = ['color_name', 'color_code', 'image', 'price', 'description', 'enable', 'published_status', 'product_id','quantity'];

    protected static function boot()
    {
        parent::boot();

        static::deleting(function ($productOption) {
            if ($productOption->productImages->isNotEmpty()) {
                $productOption->productImages->each(function ($image) {
                    //刪除圖片檔案
                    $path = str_replace('/storage/', '', $image->image);
                    if (Storage::disk('public')->exists($path)) {
                        Storage::disk('public')->delete($path);
                    }
                    $image->delete();
                });
            }

            $productOption->productImages()->detach();
        });
    }


    static public function findIfEnable($id)
    {
        // $option = self::where([
        //     ['enable',true],
        //     ['id', $id],
        // ])->first();

        $option = self::where('enable', true)->where('id', $id)->first();


        //先找到productOptiond的enable為是的目標後
        //再找到關聯的prodcut的published_status為enable
        // Log::info(@$option->product->is_published());
        if ($option) {
            // Log::info($option->product);
            if (@$option->product->is_published()) {
                return $option;
            }
        }
        return null;
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function cartItems()
    {
        return $this->hasMany(CartItem::class);
    }

    //多對多關聯
    public function productImages()
    {
        return $this->belongsToMany(ProductImage::class, 'product_option_images', 'product_option_id', 'product_image_id');
    }
}
