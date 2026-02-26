@extends('layouts.public')

@section('title', 'Event Categories')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    <h1 class="text-3xl font-bold mb-6">Event Categories</h1>
    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-6">
        @foreach($categories as $category)
            <a href="{{ route('events.categories.show', $category) }}" class="block p-6 bg-white rounded-lg shadow hover:shadow-md transition">
                <h2 class="text-xl font-semibold">{{ $category->name }}</h2>
                <p class="text-gray-500">{{ $category->events_count ?? 0 }} events</p>
            </a>
        @endforeach
    </div>
</div>
@endsection
