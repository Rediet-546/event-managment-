<div class="bg-white rounded-lg shadow-sm overflow-hidden hover:shadow-md transition duration-150">
    <a href="{{ route('events.show', $event) }}" class="block">
        @if($event->primaryMedia)
            <img src="{{ $event->primaryMedia->url }}" 
                 alt="{{ $event->title }}"
                 class="w-full h-48 object-cover">
        @else
            <div class="w-full h-48 bg-gradient-to-r from-blue-600 to-purple-600 flex items-center justify-center">
                <svg class="h-12 w-12 text-white opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                </svg>
            </div>
        @endif
    </a>

    <div class="p-4">
        <div class="flex items-center justify-between mb-2">
            <span class="text-sm text-blue-600 font-medium">{{ $event->category->name }}</span>
            @include('events::partials.capacity-badge', ['event' => $event])
        </div>

        <a href="{{ route('events.show', $event) }}" class="block">
            <h3 class="text-lg font-semibold text-gray-900 mb-2 hover:text-blue-600 transition">{{ $event->title }}</h3>
        </a>

        <p class="text-gray-600 text-sm mb-4 line-clamp-2">{{ $event->short_description ?? Str::limit($event->description, 100) }}</p>

        <div class="space-y-2 text-sm text-gray-500">
            <div class="flex items-center">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                </svg>
                <span>{{ $event->start_date->format('M d, Y · g:i A') }}</span>
            </div>
            <div class="flex items-center">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                </svg>
                <span>{{ $event->city }}, {{ $event->country }}</span>
            </div>
        </div>

        <div class="mt-4 pt-4 border-t flex items-center justify-between">
            <span class="text-xl font-bold text-gray-900">{{ $event->formatted_price }}</span>
            
            <a href="{{ route('events.show', $event) }}" 
               class="bg-blue-600 text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-blue-700 transition duration-150">
                View Details
            </a>
        </div>
    </div>
</div>