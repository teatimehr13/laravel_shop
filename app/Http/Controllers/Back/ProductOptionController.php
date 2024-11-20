<?php

namespace App\Http\Controllers;
namespace App\Http\Controllers\Back;
use App\Http\Controllers\Controller;
use App\Http\Requests\Back\ProductRequest;
use App\Http\Resources\Back\ProductResource;
use App\Models\Product;
use App\Models\ProductOption;
use App\Models\Subcategory;
use Illuminate\Http\Request;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Inertia\Inertia;

class ProductOptionController extends Controller
{
    public function index(Product $product)
    {
        $productOptions = $product->product_options()->get();
        return Inertia::render('Back/ProductOption', [
            'productOptions' => $productOptions
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(ProductRequest $request) {}

    /**
     * Store a newly created resource in storage.
     */
    public function store(ProductRequest $request)
    {
      
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {

    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update($id, ProductRequest $request)
    {

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {

    }
}
