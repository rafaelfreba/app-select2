<?php

namespace App\Models;

use App\Contracts\HasSelect2List;
use App\Http\Resources\Select2Resource;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class Product extends Model implements HasSelect2List
{
    use HasFactory;

    // ATUALIZADO: Assinatura do método e lógica interna
    public static function getSelectList(array $options = []): AnonymousResourceCollection
    {
        $query = static::query();

        // Extrai os valores do array de opções
        $search = $options['search'] ?? null;
        $cascadeValue = $options['cascade'] ?? null; // Usando o novo nome

        // Lógica de dependência com o novo nome
        if ($cascadeValue) {
            $query->where('category_id', $cascadeValue);
        }

        if ($search) {
            $query->where('name', 'like', "%{$search}%");
        }

        $products = $query->paginate(15);

        return Select2Resource::collection($products);
    }
}
