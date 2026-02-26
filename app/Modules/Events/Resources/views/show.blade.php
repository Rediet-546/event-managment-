@extends('layouts.public')

@section('title', $event->title)

@section('content')
<!-- Event Header with Hero -->
<div class="relative h-96 bg-gray-900">
    @if($event->primaryMedia)
        <img src="{{ $event->primaryMedia->url }}" 
             alt="{{ $event->title }}"
             class="w-full h-full object-cover opacity-50">
    @else
        <div class="w-full h-full bg-gradient-to-r from-blue-600 to-purple-600"></div>
    @endif
    <div class="absolute inset-0 flex items-center">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-white">
            <div class="flex items-center gap-2 text-sm mb-4">
                <a href="{{ route('events.index') }}" class="hover:underline">Events</a>
                <span>/</span>
                <a href="{{ route('events.categories.show', $event->category->slug) }}" 
                   class="hover:underline">{{ $event->category->name }}</a>
            </div>
            <h1 class="text-4xl md:text-5xl font-bold mb-4">{{ $event->title }}</h1>
            <p class="text-xl opacity-90 mb-4">Hosted by {{ $event->organizer->name }}</p>
            @include('events::partials.capacity-badge', ['event' => $event])
        </div>
    </div>
</div>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Main Content -->
        <div class="lg:col-span-2">
            <!-- Description -->
            <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
                <h2 class="text-2xl font-bold text-gray-900 mb-4">About This Event</h2>
                <div class="prose max-w-none text-gray-600">
                    {!! nl2br(e($event->description)) !!}
                </div>
            </div>

            <!-- Event Gallery -->
            @if($event->media->count() > 1)
                <div class="bg-white rounded-lg shadow-sm p-6">
                    <h2 class="text-2xl font-bold text-gray-900 mb-4">Gallery</h2>
                    <div class="grid grid-cols-4 gap-4">
                        @foreach($event->media->skip(1) as $media)
                            <img src="{{ $media->url }}" 
                                 alt="{{ $event->title }}"
                                 class="w-full h-24 object-cover rounded-lg cursor-pointer hover:opacity-75 transition">
                        @endforeach
                    </div>
                </div>
            @endif
        </div>

        <!-- Sidebar -->
        <div class="lg:col-span-1">
            <!-- Booking Card -->
            <div class="bg-white rounded-lg shadow-sm p-6 sticky top-24">
                <div class="text-3xl font-bold text-gray-900 mb-4">
                    {{ $event->formatted_price }}
                </div>

                <div class="space-y-4 text-sm mb-6">
                    <div class="flex items-start">
                        <svg class="w-5 h-5 text-gray-400 mr-3 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                        <div>
                            <div class="font-medium text-gray-900">Date & Time</div>
                            <div class="text-gray-600">{{ $event->start_date->format('l, F j, Y') }}</div>
                            <div class="text-gray-600">{{ $event->start_date->format('g:i A') }} - {{ $event->end_date->format('g:i A') }}</div>
                        </div>
                    </div>

                    <div class="flex items-start">
                        <svg class="w-5 h-5 text-gray-400 mr-3 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                        <div>
                            <div class="font-medium text-gray-900">Location</div>
                            <div class="text-gray-600">{{ $event->venue }}</div>
                            <div class="text-gray-600">{{ $event->address }}</div>
                            <div class="text-gray-600">{{ $event->city }}, {{ $event->country }}</div>
                        </div>
                    </div>
                </div>

                @if($event->isBookable())
                    <a href="{{ route('registration.create', $event) }}" 
                       class="block w-full bg-blue-600 hover:bg-blue-700 text-white text-center font-medium py-3 px-4 rounded-lg transition duration-150 mb-3">
                        Book Now
                    </a>
                @elseif($event->is_full)
                    <button disabled 
                            class="w-full bg-gray-400 text-white font-medium py-3 px-4 rounded-lg cursor-not-allowed">
                        Sold Out
                    </button>
                @else
                    <button disabled 
                            class="w-full bg-gray-400 text-white font-medium py-3 px-4 rounded-lg cursor-not-allowed">
                        {{ ucfirst($event->status) }}
                    </button>
                @endif

                <!-- Share -->
                <div class="mt-6 pt-6 border-t">
                    <p class="text-sm font-medium text-gray-700 mb-3">Share this event</p>
                    <div class="flex gap-3">
                        <a href="#" class="text-gray-400 hover:text-blue-600 transition">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M22 12c0-5.523-4.477-10-10-10S2 6.477 2 12c0 4.991 3.657 9.128 8.438 9.878v-6.987h-2.54V12h2.54V9.797c0-2.506 1.492-3.89 3.777-3.89 1.094 0 2.238.195 2.238.195v2.46h-1.26c-1.243 0-1.63.771-1.63 1.562V12h2.773l-.443 2.89h-2.33v6.988C18.343 21.128 22 16.991 22 12z"/>
                            </svg>
                        </a>
                        <a href="#" class="text-gray-400 hover:text-blue-400 transition">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M8.29 20.251c7.547 0 11.675-6.253 11.675-11.675 0-.178 0-.355-.012-.53A8.348 8.348 0 0022 5.92a8.19 8.19 0 01-2.357.646 4.118 4.118 0 001.804-2.27 8.224 8.224 0 01-2.605.996 4.107 4.107 0 00-6.993 3.743 11.65 11.65 0 01-8.457-4.287 4.106 4.106 0 001.27 5.477A4.072 4.072 0 012.8 9.713v.052a4.105 4.105 0 003.292 4.022 4.095 4.095 0 01-1.853.07 4.108 4.108 0 003.834 2.85A8.233 8.233 0 012 18.407a11.616 11.616 0 006.29 1.84"/>
                            </svg>
                        </a>
                        <a href="#" class="text-gray-400 hover:text-blue-800 transition">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M19 0h-14c-2.761 0-5 2.239-5 5v14c0 2.761 2.239 5 5 5h14c2.762 0 5-2.239 5-5v-14c0-2.761-2.238-5-5-5zm-11 19h-3v-11h3v11zm-1.5-12.268c-.966 0-1.75-.79-1.75-1.764s.784-1.764 1.75-1.764 1.75.79 1.75 1.764-.783 1.764-1.75 1.764zm13.5 12.268h-3v-5.604c0-3.368-4-3.113-4 0v5.604h-3v-11h3v1.765c1.396-2.586 7-2.777 7 2.476v6.759z"/>
                            </svg>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Related Events -->
    @if($relatedEvents->isNotEmpty())
        <div class="mt-12">
            <h2 class="text-2xl font-bold text-gray-900 mb-6">You Might Also Like</h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                @foreach($relatedEvents as $relatedEvent)
                    @include('events::partials.event-card', ['event' => $relatedEvent])
                @endforeach
            </div>
        </div>
    @endif
</div>
@endsection