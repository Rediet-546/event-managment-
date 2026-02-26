@extends('layouts.admin')

@section('title', 'Edit Event - ' . $event->title)

@section('content')
<div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
    <div class="p-6 bg-white border-b border-gray-200">
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-2xl font-bold text-gray-800">Edit Event: {{ $event->title }}</h2>
            <div class="flex items-center gap-3">
                <span class="px-3 py-1 text-sm rounded-full 
                    @if($event->status === 'published') bg-green-100 text-green-800
                    @elseif($event->status === 'draft') bg-gray-100 text-gray-800
                    @elseif($event->status === 'cancelled') bg-red-100 text-red-800
                    @endif">
                    {{ ucfirst($event->status) }}
                </span>
                <a href="{{ route('admin.events.index') }}" 
                   class="text-gray-600 hover:text-gray-900 flex items-center">
                    <svg class="w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    Back to Events
                </a>
            </div>
        </div>

        @if(session('success'))
            <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6" role="alert">
                <p>{{ session('success') }}</p>
            </div>
        @endif

        @if($errors->any())
            <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6" role="alert">
                <p class="font-bold">Please fix the following errors:</p>
                <ul class="list-disc list-inside">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('admin.events.update', $event->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <!-- Basic Information -->
            <div class="bg-white rounded-lg border border-gray-200 p-6 mb-6">
                <h2 class="text-lg font-semibold mb-4">Basic Information</h2>
                
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
            <div class="bg-white rounded-lg border border-gray-200 p-6 mb-6">
                <h2 class="text-lg font-semibold mb-4">Date & Time</h2>
                
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
            <div class="bg-white rounded-lg border border-gray-200 p-6 mb-6">
                <h2 class="text-lg font-semibold mb-4">Location</h2>
                
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
            <div class="bg-white rounded-lg border border-gray-200 p-6 mb-6">
                <h2 class="text-lg font-semibold mb-4">Capacity & Pricing</h2>
                
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

            <!-- Status and Featured -->
            <div class="bg-white rounded-lg border border-gray-200 p-6 mb-6">
                <h2 class="text-lg font-semibold mb-4">Publishing Options</h2>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                        <select name="status" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            <option value="draft" {{ $event->status == 'draft' ? 'selected' : '' }}>Draft</option>
                            <option value="published" {{ $event->status == 'published' ? 'selected' : '' }}>Published</option>
                            <option value="cancelled" {{ $event->status == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Featured Event</label>
                        <div class="flex items-center h-10">
                            <input type="checkbox" name="is_featured" value="1" {{ $event->is_featured ? 'checked' : '' }}
                                   class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            <span class="ml-2 text-sm text-gray-700">Show this event in featured sections</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Submit Buttons -->
            <div class="flex justify-end space-x-3 pt-6 border-t">
                <a href="{{ route('admin.events.index') }}" 
                   class="px-6 py-2.5 border border-gray-300 rounded-md text-gray-700 bg-white hover:bg-gray-50 transition">
                    Cancel
                </a>
                <button type="submit" 
                        class="px-6 py-2.5 bg-blue-600 border border-transparent rounded-md text-white hover:bg-blue-700 transition">
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
</script>
@endpush
@endsection