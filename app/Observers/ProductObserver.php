<?php

namespace App\Observers;

use App\Models\Product;

class ProductObserver
{
    public function creating(Product $product)
    {
        $product->created_by = auth()->user()->id;
        $product->updated_by = auth()->user()->id;
    }

    public function updating(Product $product)
    {
        $product->updated_by = auth()->user()->id;
    }
}
