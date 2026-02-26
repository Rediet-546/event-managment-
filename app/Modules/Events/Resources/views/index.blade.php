@extends('layouts.public')

@section('title', 'Discover Amazing Events')

@section('content')
<!-- Hero Section -->
<div class="hero-gradient text-white py-24 min-h-[60vh] flex items-center" style="background: url('{{ asset('images/hero.jpg') }}') center/cover no-repeat;">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center">
            <h1 class="text-4xl md:text-5xl font-bold mb-4">Discover Amazing Events</h1>
            <p class="text-xl mb-8 opacity-90">Find and book the best events in your city</p>
            
            <!-- Search Bar -->
            <div class="max-w-2xl mx-auto">
                <form method="GET" action="{{ route('events.index') }}" class="flex gap-2">
                    <input type="text" 
                           name="search" 
                           value="{{ request('search') }}"
                           placeholder="Search events by title, description, or location..."
                           class="flex-1 px-4 py-3 rounded-lg text-gray-900 placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <button type="submit" 
                            class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg font-medium transition duration-150">
                        Search
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12" id="events">
    <!-- Filters -->
    @include('events::partials.filters', ['categories' => $categories])

    <!-- Events Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse($events as $event)
            @include('events::partials.event-card', ['event' => $event])
        @empty
            <div class="col-span-full text-center py-12">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                </svg>
                <h3 class="mt-2 text-sm font-medium text-gray-900">No events found</h3>
                <p class="mt-1 text-sm text-gray-500">Try adjusting your search or filter criteria.</p>
            </div>
        @endforelse
    </div>

    <!-- Pagination -->
    <div class="mt-8">
        {{ $events->links() }}
    </div>
</div>
@endsection