<?php

namespace App\View\Components;

use Illuminate\View\Component;
use Illuminate\View\View;

class AppLayout extends Component
{
    public bool $withoutSidebar;

    public function __construct(bool $withoutSidebar = false)
    {
        $this->withoutSidebar = $withoutSidebar;
    }

    /**
     * Get the view / contents that represents the component.
     */
    public function render(): View
    {
        return view('layouts.app');
    }
}
