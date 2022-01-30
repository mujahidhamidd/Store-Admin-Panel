<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;

    use HasFactory;

    protected $guarded = ['id'];
    
    /**
     * tree
     * form tree of categories and sub categories recursively
     *
     * @return collection
     */
    public static function tree()
    {
        // get all categories
        $categories = self::get();

        // only get parent one
        $parentCategories = $categories->whereNull('parent_id');
        
        //call format tree to get the childs of parent categories
        self::formatTree($parentCategories, $categories);

        return $parentCategories->values();
    }

    private static function formatTree($categories, $allCategories)
    {
        foreach ($categories as $category) {
            $category->children = $allCategories->where('parent_id', $category->id)->values();
            if ($category->children->isNotEmpty()) {
                self::formatTree($category->children, $allCategories);
            }
        }
    }
    
    /**
     * getChildrensIds
     * recive category and get its childs recursively
     *
     * @param  mixed $category
     * @return collection
     */
    public static  function getChildrensIds($category)
    {
        $categoryIds = collect();
        self::pluckCategoryIds($category, $categoryIds);
        return $categoryIds;
    }
    public static  function pluckCategoryIds($category,$categoryIds)
    {
        foreach($category as $cat){
            $categoryIds->push($cat->id);
            if($cat->subCategories){
                self::pluckCategoryIds($cat->subCategories,$categoryIds);
            }

        }
     

    }

    public function subCategories()
    {
        return $this->hasMany(Category::class, 'parent_id')->with('subCategories');
    }

    public function parent()
    {
        return $this->belongsTo(Category::class, 'parent_id')->with('parent');
    }
}
