@extends('layouts.app')

@section('content')
    <div class="container mx-auto p-4">
        <h1 class="text-2xl font-bold mb-4">List of {{$resource}}</h1>
        <table class="min-w-full leading-normal">
            <thead>
            <tr>
                <!-- Add your table headers here -->
            </tr>
            </thead>
            <tbody>
            @foreach($items as $item)
                <tr>
                    <!-- Add your table data here -->
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
@endsection
