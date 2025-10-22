<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\Select2Request;

class Select2Controller extends Controller
{
    public function index(Select2Request $request)
    {
        $modelClass = config('select2.models.' . $request->validated('model'));

        $options = $request->validated();

        $resource = $modelClass::getSelectList($options);

        return $resource;
    }
}
