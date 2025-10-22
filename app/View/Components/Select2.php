<?php

namespace App\View\Components;

use Illuminate\View\Component;
use App\Contracts\HasSelect2List;
use Illuminate\Contracts\View\View;

class Select2 extends Component
{
    public array $selectedOptions = [];
    public ?string $validClass = null;

    public function __construct(
        public string $name,
        public string $model,
        public ?string $label = null,
        public ?string $placeholder = null,
        public bool $multiple = false,
        public mixed $selected = null,
        public ?string $dependent = null,
        public ?string $dependentValue = null,

        // --- MELHORIA AQUI: A nova flag ---
        // Controla se o campo deve ser desabilitado quando o "pai" está vazio.
        public bool $disableOnEmptyParent = false
    ) {
        if ($selected) {
            $modelClass = config('select2.models.' . $model);

            if ($modelClass && class_exists($modelClass) && is_subclass_of($modelClass, HasSelect2List::class)) {

                $selectedIds = is_array($selected) ? $selected : [$selected];
                $selectedIds = array_filter($selectedIds);

                if (!empty($selectedIds)) {
                    $items = $modelClass::find($selectedIds);

                    if ($items) {
                        $items = $items instanceof \Illuminate\Database\Eloquent\Collection ? $items : collect([$items]);

                        foreach ($items as $item) {
                            $this->selectedOptions[] = [
                                'id' => $item->id,
                                'text' => $item->name,
                            ];
                        }
                    }
                }
            }
        }
    }

    public function render(): View
    {
        $errors = session()->get('errors');
        $errorName = str_replace(['[', ']'], '', $this->name);

        if ($errors && $errors->has($errorName)) {
            $this->validClass = 'is-invalid';
        }

        if (old($errorName) && !$errors->has($errorName)) {
            $this->validClass = 'is-valid';
        }

        return view('components.select2');
    }
}
