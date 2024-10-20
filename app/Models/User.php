<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'is_admin'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    public function carts(){
        return $this->hasMany(Cart::class);
    }

    //一個使用者一個購物車
    public function purchase_cart(){
        return $this->hasOne(Cart::class)->where('cart_type', Cart::typeIndex(Cart::PURCHASE));
    }

    public function getPurchaseCartOrCreate(){
        $purchase_cart = $this->purchase_cart;
        if(!$purchase_cart){
            $purchase_cart = Cart::create([
                'user_id' => $this->id,
                'cart_type' => Cart::typeIndex(Cart::PURCHASE)
            ]);
        }

        return $purchase_cart;
    }
}
