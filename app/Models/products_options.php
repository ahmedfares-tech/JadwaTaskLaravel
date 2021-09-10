<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class products_options extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $fillable = [
        'product_id',
        'option_id',
        'value',
        'price',
        'optional'
    ];
    protected $appends = ['checked'];
    public function getCheckedAttribute()
    {
        return $checked = false;
    }
    public function product()
    {
        return $this->hasOne(Product::class, 'id', 'product_id');
    }
    public function option()
    {
        return $this->hasOne(Option::class, 'id', 'option_id');
    }
}
