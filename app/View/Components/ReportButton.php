<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class ReportButton extends Component
{
    /**
     * Create a new component instance.
     */
    public function __construct(
        public string $printRoute,
        public string $goToRoute,
        public string $goto,
        public string $className,
        public string $idForPrintRoute = 'report'
    ) {}

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View | Closure | string
    {
        return view('components.report-button');
    }
}
