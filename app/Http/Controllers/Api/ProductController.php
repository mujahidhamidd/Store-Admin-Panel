<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Company;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $per_page = $request->query('per_page') ?? 10;
        $direction =  $request->query('direction') ?? 'desc';
        $sortBy =  $request->query('sortBy') ?? "id";
        $search = $request->query('search') ?? '';
        $mainCategoryFilter =  $request->query('main_category_filter');
        $subCategoryFiltter =  $request->query('sub_category_filter');
        if ($mainCategoryFilter) {
            $mainCategorytree = Category::find($mainCategoryFilter)->subCategories;
            $mainCategoryFilter = Category::getChildrensIds($mainCategorytree);
        }
        if ($subCategoryFiltter) {
            $subCategory = Category::find($subCategoryFiltter);
            $subCategorytree =  $subCategory->subCategories;
            // if sub category has children filter by category itself and children // else filter only by category id
            if ($subCategorytree->count() > 0) {
                $subCategoryFiltter = Category::getChildrensIds($subCategorytree);
            } else {
                $subCategoryFiltter =array();
                $subCategoryFiltter[] = $subCategory->id;
            }
        }

        $products =  Product::with(['company', 'category.parent'])->when($search, function ($query) use ($search) {
            $query->where('name', 'like', '%' . $search . '%');
        })->when($mainCategoryFilter, function ($query) use ($mainCategoryFilter) {
            $query->whereIn('category_id', $mainCategoryFilter);
        })
            ->when($subCategoryFiltter, function ($query) use ($subCategoryFiltter) {
                $query->whereIn('category_id', $subCategoryFiltter);
            })
            ->orderBy($sortBy, $direction)
            ->paginate($per_page);

        $products->each(function ($product) {
            if ($product->category)
                return $product->category_text = $product->formatCategory($product->category)->reverse()->implode(' / ');
        });

        return     $products;
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string',
            'price' => 'required|numeric',
            'category_id' => 'required|exists:categories,id',
            'company_id' => 'required|exists:companies,id',
        ]);
        return   Product::create($validated);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Product $product)
    {
        return  $product;
    }


    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Product $product)
    {
        $validated = $request->validate([
            'name' => 'required|string'
        ]);
        $product->update($validated);

        return $product;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Product $product)
    {
        $product->delete();
    }
}
