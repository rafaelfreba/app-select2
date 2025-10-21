<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use App\Contracts\HasSelect2List;
use Illuminate\Support\Facades\Log;
use App\Http\Resources\Select2Resource;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class User extends Authenticatable implements HasSelect2List
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public static function getSelectList(?string $search = null, ?string $dependValue = null): AnonymousResourceCollection
    {
        $query = static::query();

        if ($search) {
            $query->where('name', 'like', "%{$search}%");
        }

        $users = $query->paginate(15);

        return Select2Resource::collection($users);
    }
}
