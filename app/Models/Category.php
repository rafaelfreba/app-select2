<?php

namespace App\Models;

use App\Contracts\HasSelect2List;
use App\Http\Resources\Select2Resource;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class Category extends Model implements HasSelect2List
{
    use HasFactory;
    protected $fillable = ['name'];

    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }

    public static function getSelectList(array $options = []): AnonymousResourceCollection
    {
        $query = static::query();

        $search = $options['search'] ?? null;

        if ($search) {
            $query->where('name', 'like', "%{$search}%");
        }

        $categories = $query->paginate(15);

        return Select2Resource::collection($categories);
    }
}
