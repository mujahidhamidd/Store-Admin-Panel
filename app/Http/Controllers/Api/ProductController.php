<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
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
        $global_search = $request->query('globalsearch') ?? '';

        $products =  Product::with(['company', 'category.parent'])->when($global_search, function ($query) use ($global_search) {
            $query->where('created_at', 'like', '%' . $global_search . '%')
                ->orWhereHas('appUser', function ($subQuery) use ($global_search) {
                    $subQuery->where('name', 'LIKE', "%{$global_search}%")
                        ->orWhere('mobile', 'LIKE', "%{$global_search}%");
                });
        })

            ->orderBy($sortBy, $direction)
            ->paginate($per_page);

        $products->each(function ($product, $key) {
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
    public function show(Company $company)
    {
        return  $company;
    }


    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Company $company)
    {
        $validated = $request->validate([
            'name' => 'required|string'
        ]);
        $company->update($validated);

        return $company;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Company $company)
    {
        $company->delete();
    }
}
