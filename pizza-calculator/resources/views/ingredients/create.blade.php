@extends('layouts.app')

@section('content')
<div class="bg-white rounded-lg shadow-lg p-6">
    <div class="mb-6">
        <h1 class="text-2xl font-semibold text-gray-800">
            <i class="fas fa-plus text-indigo-600 mr-2"></i>Add New Ingredient
        </h1>
        <p class="text-gray-600 mt-1">Create a new ingredient for your pizzas.</p>
    </div>

    <form action="{{ route('ingredients.store') }}" method="POST">
        @include('ingredients.form')
    </form>
</div>
@endsection