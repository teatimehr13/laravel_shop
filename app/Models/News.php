<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class News extends Model
{
    use HasFactory;
    protected $fillable = [
        'title',
        'date',
        'image',
        'description',
        'is_enabled',
        'news_type',
    ];

    const Promotions  = '公告';
    const Announcements = '促銷優惠';

    const news_type = [
        1 => self::Announcements,
        2 => self::Promotions,
    ];

    //laravel accessor專用 (store_type_name) 
    public function news_type_name()
    {
        if (isset(self::news_type[$this->news_type])) {
            return ucfirst(self::news_type[$this->news_type]);
        } else {
            return ucfirst(self::news_type[0]);
        }
    }   
}
