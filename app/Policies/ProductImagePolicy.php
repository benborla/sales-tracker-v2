<?php

namespace App\Policies;

use App\Models\ProductImage;
use Illuminate\Auth\Access\HandlesAuthorization;

class ProductImagePolicy
{
    use HandlesAuthorization;

    public function viewAny()
    {
        return i('can view', ProductImage::class);
    }

    public function view()
    {
        return i('can view', ProductImage::class);
    }

    public function create()
    {
        return i('can add', ProductImage::class);
    }

    public function update()
    {
        return i('can update', ProductImage::class);
    }

    public function delete()
    {
        return i('can delete', ProductImage::class);
    }

    public function addImages()
    {
        return i('can add', ProductImage::class);
    }
}
