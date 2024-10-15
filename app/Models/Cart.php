<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cart extends Model
{
    use HasFactory;

    const PURCHASE = 'purchase';
    const NEXT_TIME = 'next_time';

    const types = [
        0 => self::PURCHASE,
        1 => self::NEXT_TIME
    ];

    protected $fillable = [
        'cart_type',
        'user_id'
    ]; 
}
