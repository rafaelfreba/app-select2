<?php

namespace App\Models;

use App\Contracts\HasSelect2List;
use App\Http\Resources\Select2Resource;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class Product extends Model implements HasSelect2List
{
    use HasFactory;
    public static function getSelectList(?string $search = null): AnonymousResourceCollection
    {
        $query = static::query();

        if ($search) {
            $query->where('name', 'like', "%{$search}%");
        }

        $products = $query->paginate(15);

        return Select2Resource::collection($products);
    }

}
