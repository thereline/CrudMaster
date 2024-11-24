@extends('layouts.app')

@section('content')
    <div class="container mx-auto p-4">
        <h1 class="text-2xl font-bold mb-4">Edit {{ $resource }}</h1>
        <form action="{{ route('resource.update', $item->id) }}" method="POST">
            @csrf
            @method('PUT')
            <!-- Add your form fields here -->
            <button type="submit" class="mt-4 bg-blue-500 text-white px-4 py-2 rounded">Update</button>
        </form>
    </div>
@endsection
