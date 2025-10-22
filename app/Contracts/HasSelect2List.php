<?php

namespace App\Contracts;

use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

interface HasSelect2List
{
    /**
     * @param array $options Pode conter 'search', 'cascade', 'page', etc.
     */
    public static function getSelectList(array $options = []): AnonymousResourceCollection;
}
