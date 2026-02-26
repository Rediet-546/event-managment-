@extends('layouts.app')

@section('title', 'Edit Event - ' . $event->title)

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-3xl mx-auto">
        <div class="flex items-center justify-between mb-8">
            <h1 class="text-3xl font-bold text-gray-900">Edit Event</h1>
            <span class="px-3 py-1 text-sm rounded-full 
                @if($event->status === 'published') bg-green-100 text-green-800
                @elseif($event->status === 'draft') bg-gray-100 text-gray-800
                @elseif($event->status === 'cancelled') bg-red-100 text-red-800
                @endif">
                {{ ucfirst($event->status) }}
            </span>
        </div>

        <form action="{{ route('events.update', $event) }}" method="POST" enctype="multipart/form-data" class="space-y-6">
            @csrf
            @method('PUT')

            <!-- Basic Information -->
            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-xl font-semibold mb-4">Basic Information</h2>
                
                <div class="grid grid-cols-1 gap-6">
                    <!-- Title -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Event Title *</label>
                        <input type="text" name="title" value="{{ old('title', $event->title) }}" 
                               class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                               required>
                        @error('title')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Category -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Category *</label>
                        <select name="category_id" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" required>
                            <option value="">Select Category</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}" {{ old('category_id', $event->category_id) == $category->id ? 'selected' : '' }}>
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('category_id')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Description -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Description *</label>
                        <textarea name="description" rows="6" 
                                  class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                  required>{{ old('description', $event->description) }}</textarea>
                        @error('description')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Short Description -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Short Description</label>
                        <textarea name="short_description" rows="2" 
                                  class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">{{ old('short_description', $event->short_description) }}</textarea>
                        <p class="mt-1 text-sm text-gray-500">Brief summary of your event (max 500 characters)</p>
                    </div>
                </div>
            </div>

            <!-- Date & Time -->
            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-xl font-semibold mb-4">Date & Time</h2>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Start Date & Time *</label>
                        <input type="datetime-local" name="start_date" 
                               value="{{ old('start_date', $event->start_date->format('Y-m-d\TH:i')) }}" 
                               class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                               required>
                        @error('start_date')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">End Date & Time *</label>
                        <input type="datetime-local" name="end_date" 
                               value="{{ old('end_date', $event->end_date->format('Y-m-d\TH:i')) }}" 
                               class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                               required>
                        @error('end_date')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Registration Deadline</label>
                        <input type="datetime-local" name="registration_deadline" 
                               value="{{ old('registration_deadline', $event->registration_deadline?->format('Y-m-d\TH:i')) }}" 
                               class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    </div>
                </div>
            </div>

            <!-- Location -->
            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-xl font-semibold mb-4">Location</h2>
                
                <div class="grid grid-cols-1 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Venue *</label>
                        <input type="text" name="venue" value="{{ old('venue', $event->venue) }}" 
                               class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                               required>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Address *</label>
                        <input type="text" name="address" value="{{ old('address', $event->address) }}" 
                               class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                               required>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">City *</label>
                            <input type="text" name="city" value="{{ old('city', $event->city) }}" 
                                   class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                   required>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Country *</label>
                            <input type="text" name="country" value="{{ old('country', $event->country) }}" 
                                   class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                   required>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Capacity & Pricing -->
            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-xl font-semibold mb-4">Capacity & Pricing</h2>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Maximum Attendees</label>
                        <input type="number" name="max_attendees" value="{{ old('max_attendees', $event->max_attendees) }}" 
                               class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                               min="1">
                        <p class="mt-1 text-sm text-gray-500">Leave empty for unlimited</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Price *</label>
                        <div class="flex gap-2">
                            <input type="number" name="price" value="{{ old('price', $event->price) }}" 
                                   class="flex-1 rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                   step="0.01" min="0" required>
                            <select name="currency" class="w-24 rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                <option value="USD" {{ $event->currency == 'USD' ? 'selected' : '' }}>USD</option>
                                <option value="EUR" {{ $event->currency == 'EUR' ? 'selected' : '' }}>EUR</option>
                                <option value="GBP" {{ $event->currency == 'GBP' ? 'selected' : '' }}>GBP</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="mt-4">
                    <label class="flex items-center">
                        <input type="checkbox" name="is_virtual" value="1" {{ old('is_virtual', $event->is_virtual) ? 'checked' : '' }}
                               class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        <span class="ml-2 text-sm text-gray-700">This is a virtual event</span>
                    </label>
                </div>

                <div id="virtual_link_container" class="mt-4 {{ old('is_virtual', $event->is_virtual) ? '' : 'hidden' }}">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Virtual Meeting Link</label>
                    <input type="url" name="virtual_link" value="{{ old('virtual_link', $event->virtual_link) }}" 
                           class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                           placeholder="https://zoom.us/j/...">
                </div>
            </div>

            <!-- Current Media -->
            @if($event->media->count() > 0)
            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-xl font-semibold mb-4">Current Images</h2>
                <div class="grid grid-cols-4 gap-4">
                    @foreach($event->media as $media)
                    <div class="relative group">
                        <img src="{{ $media->url }}" class="w-full h-24 object-cover rounded-lg">
                        @if($media->is_primary)
                        <span class="absolute top-1 left-1 bg-blue-600 text-white text-xs px-2 py-1 rounded">Primary</span>
                        @endif
                        <div class="absolute inset-0 bg-black bg-opacity-50 opacity-0 group-hover:opacity-100 transition flex items-center justify-center rounded-lg">
                            <button type="button" 
                                    onclick="deleteMedia({{ $media->id }})"
                                    class="bg-red-600 text-white p-2 rounded-full hover:bg-red-700">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                </svg>
                            </button>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif

            <!-- Upload New Media -->
            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-xl font-semibold mb-4">Add More Images</h2>
                <div>
                    <input type="file" name="media[]" multiple accept="image/*"
                           class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                </div>
            </div>

            <!-- Status Update (for admins/organizers) -->
            @can('publish', $event)
            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-xl font-semibold mb-4">Event Status</h2>
                <div class="flex gap-4">
                    @if($event->status === 'draft')
                    <button type="button" onclick="publishEvent()"
                            class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 transition">
                        Publish Event
                    </button>
                    @endif
                    
                    @if($event->status === 'published')
                    <button type="button" onclick="cancelEvent()"
                            class="px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700 transition">
                        Cancel Event
                    </button>
                    @endif
                </div>
            </div>
            @endcan

            <!-- Submit Buttons -->
            <div class="flex justify-end gap-4">
                <a href="{{ route('events.show', $event) }}" 
                   class="px-6 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50 transition">
                    Cancel
                </a>
                <button type="submit" 
                        class="px-6 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition">
                    Update Event
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    document.querySelector('input[name="is_virtual"]').addEventListener('change', function() {
        document.getElementById('virtual_link_container').classList.toggle('hidden');
    });

    function publishEvent() {
        if (confirm('Are you sure you want to publish this event? It will be visible to the public.')) {
            document.getElementById('publish-form').submit();
        }
    }

    function cancelEvent() {
        let reason = prompt('Please provide a reason for cancellation:');
        if (reason !== null) {
            document.getElementById('cancel-reason').value = reason;
            document.getElementById('cancel-form').submit();
        }
    }

    function deleteMedia(mediaId) {
        if (confirm('Are you sure you want to delete this image?')) {
            fetch(`/events/media/${mediaId}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            }).then(() => window.location.reload());
        }
    }
</script>
@endpush

<!-- Hidden forms for actions -->
@can('publish', $event)
    @if($event->status === 'draft')
    <form id="publish-form" action="{{ route('events.publish', $event) }}" method="POST" class="hidden">
        @csrf
    </form>
    @endif
    
    @if($event->status === 'published')
    <form id="cancel-form" action="{{ route('events.cancel', $event) }}" method="POST" class="hidden">
        @csrf
        <input type="hidden" id="cancel-reason" name="reason">
    </form>
    @endif
@endcan
@endsection