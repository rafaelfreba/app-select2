<?php

use App\Http\Controllers\Select2Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/select2/{model}', [Select2Controller::class, 'index'])->name('select2.list');

Route::get('/editar', function(){
    return view('welcome', ['user_id' => 1, 'product_id' => 1]);
})->name('editar');

Route::post('/salvar', function(Request $request){
    $request->validate([
        'user_id' => ['required', 'integer', 'exists:users,id'],
        'product_id' => ['required', 'numeric','min:5000','exists:products,id'],
    ]);

    dd($request->validated());

    // return redirect('/')->with('success', 'Formulário salvo com sucesso!');
})->name('salvar');
