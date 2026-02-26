@extends('layouts.public')

@section('title', $category->name)

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    <h1 class="text-3xl font-bold mb-6">{{ $category->name }}</h1>

    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-6">
        @foreach($events as $event)
            <a href="{{ route('events.show', $event) }}" class="block p-6 bg-white rounded-lg shadow hover:shadow-md transition">
                <h2 class="text-xl font-semibold">{{ $event->title }}</h2>
                <p class="text-gray-500">{{ $event->start_date->toFormattedDateString() }}</p>
            </a>
        @endforeach
    </div>

    <div class="mt-8">
        {{ $events->links() }}
    </div>
</div>
@endsection
