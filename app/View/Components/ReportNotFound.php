<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class ReportNotFound extends Component
{
    /**
     * Create a new component instance.
     *
     */
    public $className, $id;
    public function __construct(string $className, string $id = 'report')
    {
        $this->className = $className;
        $this->id = $id;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View | Closure | string
    {
        return view('components.report-not-found');
    }
}
