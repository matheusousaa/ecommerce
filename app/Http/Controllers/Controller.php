<?php

namespace App\Http\Controllers;

use Inertia\Inertia;

abstract class Controller
{
    public function render($component, $props = []) {
        return Inertia::render($component, $props);
    }
}
