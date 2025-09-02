<?php

namespace App\View\Components;

use Illuminate\View\Component;
use App\Contracts\HasSelect2List;
use Illuminate\Contracts\View\View;

class Select2 extends Component
{
    public mixed $selectedOption = null;

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
                $item = $modelClass::find($selected);
                if ($item) {
                    $this->selectedOption = [
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
