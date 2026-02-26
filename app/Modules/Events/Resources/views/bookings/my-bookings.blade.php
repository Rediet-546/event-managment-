@extends('layouts.public')

@section('title', 'My Bookings')

@section('content')
<div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-2xl font-semibold text-gray-900">My Bookings</h1>
        <a href="{{ route('events.index') }}" class="text-sm text-blue-600 hover:text-blue-700">Browse events</a>
    </div>

    @if (session('success'))
        <div class="mb-4 p-4 rounded-lg bg-green-50 text-green-700">
            {{ session('success') }}
        </div>
    @endif

    @forelse ($bookings as $booking)
        <div class="bg-white shadow rounded-xl p-5 mb-4">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3">
                <div>
                    <div class="text-lg font-medium text-gray-900">{{ $booking->event?->title }}</div>
                    <div class="text-sm text-gray-600">
                        Ref: <span class="font-mono">{{ $booking->booking_reference }}</span>
                        · Tickets: {{ $booking->tickets_count }}
                        · Status: {{ ucfirst($booking->status) }}
                    </div>
                </div>
                <div class="flex gap-2">
                    <a href="{{ route('events.booking.confirmation', $booking) }}"
                       class="px-4 py-2 rounded-lg border border-gray-300 text-gray-700 hover:bg-gray-50 text-sm">
                        View
                    </a>
                    @if ($booking->status !== 'cancelled')
                        <form method="POST" action="{{ route('events.booking.cancel', $booking) }}">
                            @csrf
                            <button type="submit"
                                    class="px-4 py-2 rounded-lg bg-red-600 text-white hover:bg-red-700 text-sm">
                                Cancel
                            </button>
                        </form>
                    @endif
                </div>
            </div>
        </div>
    @empty
        <div class="bg-white shadow rounded-xl p-8 text-center text-gray-600">
            You don’t have any bookings yet.
        </div>
    @endforelse
</div>
@endsection

