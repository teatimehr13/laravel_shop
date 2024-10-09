<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class Product extends Model
{
    use HasFactory;
    protected $fillable = ['name', 'image', 'description', 'published_status', 'subcategory_id'];

    const DISABLED = 'disabled';
    const ENABLED = 'enabled';
    const DRAFT = 'draft';

    const published_statuses = [
        0 => self::DISABLED,
        1 => self::ENABLED,
        2 => self::DRAFT
    ];

    public function subcategory(){
        return $this->belongsTo(Subcategory::class);
    }

    public function product_options(){
        return $this->hasMany(ProductOption::class);
    }

    public function published_status_name(){
        if(isset(self::published_statuses[$this->published_status])){
            return ucfirst(self::published_statuses[$this->published_status]);
        } else {
            return ucfirst(self::published_statuses[0]);
        }
    }

    //狀態要為enable
    public function is_published(){
        return self::published_statuses[$this->published_status] == self::ENABLED;
    }
}
