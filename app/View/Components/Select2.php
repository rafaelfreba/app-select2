<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Select2 extends Component
{
    public function __construct(
        public string $name,
        public string $model,
        public ?string $label = null,
        public ?string $placeholder = null,
        public bool $multiple = false
    ) {
    }

    public function render(): View|Closure|string
    {
        return view('components.select2');
    }
}
