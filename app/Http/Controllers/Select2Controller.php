<?php

namespace App\Http\Controllers;

use App\Http\Requests\Select2Request;
use Illuminate\Http\Request;

class Select2Controller extends Controller
{
    public function index(Select2Request $request)
    {
        $modelClass = config('select2.models.' . $request->validated('model'));

        $search = $request->validated('search');

        $resource = $modelClass::getSelectList($search);

        return $resource;
    }
}
