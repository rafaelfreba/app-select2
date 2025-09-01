<?php

return [
    /*
     * Mapeamento de models permitidas para o componente Select2.
     * A chave é o nome de referência (o que será usado na rota) e o valor é o namespace completo da model.
     */
    'models' => [
        'user' => \App\Models\User::class,
        'product' => \App\Models\Product::class,
        // Adicione outras models aqui
    ],
];
