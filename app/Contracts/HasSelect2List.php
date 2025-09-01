<?php

namespace App\Contracts;

use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

interface HasSelect2List
{
    public static function getSelectList(?string $search = null): AnonymousResourceCollection;
}
