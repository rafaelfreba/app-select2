<?php

namespace App\View\Components;

use Illuminate\View\Component;
use App\Contracts\HasSelect2List;
use Illuminate\Contracts\View\View;

class Select2 extends Component
{
    public array $selectedOptions = [];

    public function __construct(
        public string $name,
        public string $model,
        public ?string $label = null,
        public ?string $placeholder = null,
        public bool $multiple = false,
        public mixed $selected = null,
        public ?string $validClass = null
    ) {
        if ($selected) {
            $modelClass = config('select2.models.' . $model);

            if ($modelClass && class_exists($modelClass) && is_subclass_of($modelClass, HasSelect2List::class)) {
                // Converte o valor selecionado para um array, se ainda não for
                $selectedIds = is_array($selected) ? $selected : [$selected];

                $items = $modelClass::find($selectedIds);

                // Mapeia os itens encontrados para o formato de exibição
                foreach ($items as $item) {
                    $this->selectedOptions[] = [
                        'id' => $item->id,
                        'text' => $item->name,
                    ];
                }
            }
        }
    }

    public function render(): View
    {
        $errors = session()->get('errors');

        if ($errors && $errors->has($this->name)) {
            $this->validClass = 'is-invalid';
        }

        if (old($this->name) && !$errors->has($this->name)) {
            $this->validClass = 'is-valid';
        }

        return view('components.select2');
    }
}
