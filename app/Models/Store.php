<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Store extends Model
{
    use HasFactory;
    protected $fillable = [
        'store_name',
        'address',
        'opening_hours',
        'contact_number',
        'image',
        'is_enabled',
        'store_type'
    ];

    const DirectStore = '直營';
    const PartnerOutlet = '特約展售';
    const AuthorizedDealer = '授權經銷商';

    const store_type = [
        0 => self::DirectStore,
        1 => self::PartnerOutlet,
        2 => self::AuthorizedDealer
    ];

    //laravel accessor專用 (store_type_name) 
    public function getStoreTypeNameAttribute()
    {
        if (isset(self::store_type[$this->store_type])) {
            return ucfirst(self::store_type[$this->store_type]);
        } else {
            return ucfirst(self::store_type[0]);
        }
    }

    protected $appends = ['store_type_name'];

}
