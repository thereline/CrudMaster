@extends('layouts.app')

@section('content')
    <div class="container mx-auto p-4">
        <h1 class="text-2xl font-bold mb-4">Create New {{ $resource }}</h1>
        <form action="{{ route('resource.store') }}" method="POST">
            @csrf
            <!-- Add your form fields here -->
            <button type="submit" class="mt-4 bg-blue-500 text-white px-4 py-2 rounded">Create</button>
        </form>
    </div>
@endsection
