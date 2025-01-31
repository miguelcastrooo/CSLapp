@extends('layouts.app')

@section('content')
    <div class="container d-flex align-items-center justify-content-center" style="height: 80vh;">
        <div class="text-center">
            <h1 class="display-4 mb-4">¡Bienvenido, {{ Auth::user()->name }}!</h1>
        </div>
    </div>
@endsection
