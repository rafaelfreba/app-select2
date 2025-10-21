<?php

namespace App\Models;

use App\Http\Resources\Select2Resource;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class Category extends Model
{
    use HasFactory;
    protected $fillable = ['name'];

    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }

    public static function getSelectList(?string $search = null, ?string $dependValue = null): AnonymousResourceCollection
    {
        $query = static::query();

        if ($search) {
            $query->where('name', 'like', "%{$search}%");
        }

        $categories = $query->paginate(15);

        return Select2Resource::collection($categories);
    }
}
