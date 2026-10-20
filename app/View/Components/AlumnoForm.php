<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class AlumnoForm extends Component
{
    public $alumno;

    public function __construct($alumno = null)
    {
        $this->alumno = $alumno;
    }

    public function render()
    {
        return view('components.alumno-form');
    }
}
