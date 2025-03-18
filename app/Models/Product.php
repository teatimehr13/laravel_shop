<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class Product extends Model
{
    use HasFactory;
    protected $fillable = ['name', 'image', 'title', 'price', 'published_status', 'subcategory_id', 'slug', 'description'];

    const DISABLED = 'disable';
    const ENABLED = 'enable';
    const DRAFT = 'draft';

    const published_statuses = [
        0 => self::DISABLED,
        1 => self::ENABLED,
        2 => self::DRAFT
    ];

    // 自動產生 Slug
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($product) {
            $product->slug = Str::slug($product->name, '-');
        });
    }

    public function subcategory()
    {
        return $this->belongsTo(Subcategory::class);
    }

    public function product_options()
    {
        return $this->hasMany(ProductOption::class);
    }

    public function product_descriptions()
    {
        return $this->hasMany(ProductDescription::class);
    }

    public function product_images()
    {
        return $this->hasMany(ProductImage::class);
    }

    public function published_status_name()
    {
        if (isset(self::published_statuses[$this->published_status])) {
            return ucfirst(self::published_statuses[$this->published_status]);
        } else {
            return ucfirst(self::published_statuses[0]);
        }
    }

    //狀態要為enable
    public function is_published()
    {
        return self::published_statuses[$this->published_status] == self::ENABLED;
    }
}
