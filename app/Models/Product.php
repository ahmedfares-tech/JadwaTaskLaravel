<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Option;

class Product extends Model
{
    use HasFactory;
    protected $fillable = [
        'name',
        'image',
        'price',
        'category_id',
        'description'
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }
    /**
     * Get all of the comments for the Product
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasManyThrough
     */
    public function options()
    {
        return $this->hasMany(products_options::class)->with('option');
    }
    // public function options()
    // {

    //     // return $this->hasManyThrough(
    //     //     products_options::class,
    //     //     Option::class,
    //     //     'product_id',
    //     //     // 'option_id',
    //     //     // 'id',
    //     //     // 'id',
    //     //     // 'product_id',
    //     //     'id',
    //     //     'id',
    //     //     // 'option_id'
    //     // );
    //     // return $this->belongsToMany('Part', 'project_part', 'project_id', 'part_id')
    //     // ->selectRaw('parts.*, sum(project_part.count) as pivot_count')
    //     // ->withTimestamps()
    //     // ->groupBy('project_part.pivot_part_id')
    //     // return $this->belongsToMany(Option::class, 'products_options', 'product_id', 'option_id')
    //     // ->withPivot(['price', 'value', 'optional','id'])
    //     // ->groupBy('products_options.option_id');
    //     // return $this->hasMany(products_options::class, 'product_id', 'id')->with('option');
    //     // ->groupBy('products_options.optional');
    // }
    public function testRel()
    {
        return $this->options()->groupBy('option_id');
    }
}
