<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CollectProductOrder extends Model
{
    use HasFactory;
    protected $fillable = [
        'products_options_id',

        'cart_id'
    ];
    public function cart()
    {
        return $this->belongsTo(Cart::class, 'id', 'cart_id');
    }
    public function items()
    {
        return $this->hasMany(products_options::class, 'id', 'products_options_id');
    }
}
