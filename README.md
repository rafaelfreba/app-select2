```
======================================================
 GUIA DE USO: COMPONENTE SELECT2 DINÂMICO (LARAVEL)
======================================================

Este guia explica como usar o componente Blade <x-select2> para criar caixas de seleção dinâmicas (com busca AJAX), suporte a cascata e validação em seu projeto Laravel.

Sumário:
1. Arquivos Principais do Sistema
2. Como Adicionar um NOVO Select (Passo a Passo)
3. Exemplos de Uso (Simples, Múltiplo, Cascata, Edição)
4. Referência de Atributos do Componente


--------------------------------------
1. ARQUIVOS PRINCIPAIS DO SISTEMA
--------------------------------------
Para o componente funcionar, os seguintes arquivos devem estar presentes no projeto:

- [Config]    config/select2.php
- [Rota]      routes/web.php (Deve conter a rota 'select2.list')
- [Contrato]  App/Contracts/HasSelect2List.php
- [Controller] App/Http/Controllers/Select2Controller.php
- [Request]   App/Http/Requests/Select2Request.php
- [Resource]  App/Http/Resources/Select2Resource.php
- [Component] App/View/Components/Select2.php
- [View]      resources/views/components/select2.blade.php
- [Models]    App/Models/User.php, Product.php, Category.php (e outras)
- [Frontend]  Seu layout principal deve carregar:
              1. jQuery
              2. Bootstrap CSS/JS
              3. Select2 CSS/JS
              4. (O @stack('scripts') DEVE VIR DEPOIS do jQuery e Select2)


--------------------------------------
2. COMO ADICIONAR UM NOVO SELECT (PASSO A PASSO)
--------------------------------------
Vamos supor que você precise adicionar um select de "Tags".

PASSO 1: O MODELO (App\Models\Tag.php)
O seu modelo DEVE implementar o contrato `HasSelect2List`.

<?php
namespace App\Models;

use App\Contracts\HasSelect2List; // 1. Importar o contrato
use App\Http\Resources\Select2Resource;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class Tag extends Model implements HasSelect2List // 2. Implementar o contrato
{
    // ... (código normal do modelo) ...

    /**
     * 3. Adicionar o método getSelectList.
     * Este é o "coração" da busca.
     *
     * @param array $options Contém ['search' => 'texto', 'cascade' => 'valor_pai']
     */
    public static function getSelectList(array $options = []): AnonymousResourceCollection
    {
        $query = static::query();

        $search = $options['search'] ?? null;
        $cascadeValue = $options['cascade'] ?? null;

        if ($search) {
            $query->where('name', 'like', "%{$search}%");
        }
        
        // Se este modelo dependesse de outro (ex: Post):
        // if ($cascadeValue) {
        //     $query->where('post_id', $cascadeValue);
        // }

        $items = $query->paginate(15);

        return Select2Resource::collection($items);
    }
}


PASSO 2: REGISTRAR O MODELO (config/select2.php)
Adicione seu novo modelo ao array 'models'. A "chave" (ex: 'tag') é o nome que você usará no Blade.

<?php
return [
    'models' => [
        'user' => \App\Models\User::class,
        'product' => \App\Models\Product::class,
        'category' => \App\Models\Category::class,
        
        'tag' => \App\Models\Tag::class, // <-- ADICIONADO AQUI
    ],
];


PASSO 3: USAR NO BLADE (.blade.php)
Agora você pode chamar o componente na sua view usando a chave 'tag' que você registrou.

<div class="form-group">
    <x-select2 
        name="tag_id" 
        model="tag" 
        label="Selecione uma Tag"
        placeholder="Digite o nome da tag" 
        :selected="old('tag_id', $tag_id ?? '')"
    />
</div>


--------------------------------------
3. EXEMPLOS DE USO
--------------------------------------

---
EXEMPLO 1: SELECT SIMPLES (Ex: Usuário)
---
Uso mais básico. Busca usuários pelo nome.

<x-select2 
    name="user_id" 
    model="user" 
    label="Selecione um usuário"
    placeholder="Digite o nome do usuário" 
    :selected="old('user_id', $user_id ?? '')" 
/>


---
EXEMPLO 2: SELECT MÚLTIPLO (Ex: Produtos)
---
Adicione a flag `:multiple="true"`. O componente já trata de colocar `[]` no nome do campo.

<x-select2 
    name="product_id" 
    model="product" 
    label="Selecione o(s) produto(s)"
    placeholder="Selecione..."
    :multiple="true" 
    :selected="old('product_id', $produtos_selecionados ?? [])" 
/>


---
EXEMPLO 3: SELECTS EM CASCATA (PAI E FILHO)
---
Um select (Filho) que depende de outro (Pai).

<div class="form-group">
    <x-select2 
        name="category_id" 
        model="category" 
        label="Categoria (PAI)"
        placeholder="Selecione uma categoria"
        :selected="old('category_id', $category_id ?? '')" 
    />
</div>

<div class="form-group">
    <x-select2 
        name="product_id" 
        model="product" 
        label="Produto (FILHO)"
        placeholder="Selecione uma categoria primeiro"
        :multiple="true" 
        :selected="old('product_id', $product_id ?? [])" 
        
        {{-- Diz ao JS qual campo PAI ele deve observar. (DEVE ser o 'name' do PAI) --}}
        dependent="category_id" 
        
        {{-- Desabilita este campo se o PAI estiver vazio (padrão: false) --}}
        :disableOnEmptyParent="true" 
    />
</div>


---
EXEMPLO 4: FORMULÁRIOS DE EDIÇÃO (COM DADOS SELECIONADOS)
---
O segredo é usar `:selected` para o valor do próprio campo e `:dependent-value` para passar o valor *inicial do PAI* para o FILHO.

<div class="form-group">
    <x-select2 
        name="category_id" 
        model="category" 
        label="Categoria (PAI)"
        placeholder="Selecione uma categoria"
        
        {{-- :selected pré-seleciona o valor do PAI --}}
        :selected="old('category_id', $category_id ?? null)" 
    />
</div>

<div class="form-group">
    <x-select2 
        name="product_id" 
        model="product" 
        label="Produto (FILHO)"
        placeholder="Selecione uma categoria primeiro"
        :multiple="true" 
        
        {{-- :selected pré-seleciona o(s) valor(es) do FILHO --}}
        :selected="old('product_id', $product_id ?? [])" 
        
        {{-- O 'dependent' continua o mesmo --}}
        dependent="category_id" 
        
        {{-- CRÍTICO: Passa o valor inicial do PAI para o FILHO --}}
        :dependent-value="old('category_id', $category_id ?? null)" 
    />
</div>


--------------------------------------
4. REFERÊNCIA DE ATRIBUTOS DO COMPONENTE
--------------------------------------

Estes são os atributos que você pode passar para o `<x-select2 ... />`

- name (string, OBRIGATÓRIO)
  O atributo `name` do <select>. (ex: "user_id")

- model (string, OBRIGATÓRIO)
  A "chave" de referência definida em `config/select2.php`. (ex: "user")

- label (string, Opcional)
  O texto a ser exibido no `<label>`. Se omitido, usa o `name` do campo.

- placeholder (string, Opcional)
  O texto de placeholder do Select2.

- multiple (bool, Opcional)
  Defina como `true` para um select de múltipla escolha. Padrão: `false`.

- selected (mixed, Opcional)
  Usado para formulários de edição. Passe o ID (para select simples) ou um array de IDs (para select múltiplo) que devem vir pré-selecionados.

- dependent (string, Opcional)
  O `name` (ou `id` do HTML) do campo PAI do qual este select depende. (ex: "category_id")

- dependent-value (mixed, Opcional)
  Usado para formulários de edição em cascata. Passe o valor INICIAL do campo PAI.

- disable-on-empty-parent (bool, Opcional)
  Se `true`, o campo será desabilitado (`disabled`) automaticamente se o campo PAI estiver vazio. Padrão: `true`.
  Use `:disable-on-empty-parent="false"` para desativar este comportamento.

- class (string, Opcional)
  Permite adicionar classes CSS customizadas diretamente ao elemento `<select>`.
```

