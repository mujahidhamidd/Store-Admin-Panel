<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{

    protected $guarded = [];
    use HasFactory;

    public function apiKey()
    {
        return $this->hasOne('App\Models\ApiKey', 'partner_id', 'id');
    }


    public function company()
    {
        return $this->belongsTo(Company::class)->select('id','name');
    }


    public static  function formatCategory($category)
    {

        $categoryNames = collect();
        self::pluckCategoryNames($category, $categoryNames);

        return $categoryNames;
    }

    public static  function pluckCategoryNames($category,$categoryNames)
    {

        $categoryNames->push($category->name);
        if($category->parent)
        self::pluckCategoryNames($category->parent,$categoryNames);

    }


    public function category()
    {
        return $this->belongsTo(Category::class);
    }
}
