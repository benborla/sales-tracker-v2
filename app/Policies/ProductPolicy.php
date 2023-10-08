<?php

namespace App\Policies;

use App\Models\Product;
use Illuminate\Auth\Access\HandlesAuthorization;

class ProductPolicy
{
    use HandlesAuthorization;

    public function viewAny()
    {
        return i('can view', Product::class);
    }

    public function view()
    {
        return i('can view', Product::class);
    }

    public function create()
    {
        return i('can create', Product::class);
    }

    public function update()
    {
        return i('can update', Product::class);
    }

    public function delete()
    {
        return i('can delete', Product::class);
    }
}
