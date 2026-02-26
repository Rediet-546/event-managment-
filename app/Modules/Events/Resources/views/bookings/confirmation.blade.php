@extends('layouts.public')

@section('title', 'Booking Confirmation')

@section('content')
<div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
    <div class="bg-white shadow rounded-xl p-6">
        <h1 class="text-2xl font-semibold text-gray-900 mb-2">Booking Confirmation</h1>
        <p class="text-gray-600 mb-6">
            {{ $booking->event?->title }}
        </p>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
            <div class="p-4 rounded-lg bg-gray-50">
                <div class="text-xs text-gray-500">Reference</div>
                <div class="font-mono text-gray-900">{{ $booking->booking_reference }}</div>
            </div>
            <div class="p-4 rounded-lg bg-gray-50">
                <div class="text-xs text-gray-500">Tickets</div>
                <div class="text-gray-900">{{ $booking->tickets_count }}</div>
            </div>
        </div>

        @if ($booking->guests && $booking->guests->count())
            <h2 class="text-sm font-semibold text-gray-900 mb-2">Attendees</h2>
            <ul class="mb-6 space-y-2">
                @foreach ($booking->guests as $guest)
                    <li class="text-sm text-gray-700">
                        {{ $guest->name }} <span class="text-gray-400">·</span> {{ $guest->email }}
                    </li>
                @endforeach
            </ul>
        @endif

        <div class="flex items-center justify-between">
            <a href="{{ route('events.my-bookings') }}" class="text-sm text-blue-600 hover:text-blue-700">
                Back to My Bookings
            </a>

            @if ($booking->status !== 'cancelled')
                <form method="POST" action="{{ route('events.booking.cancel', $booking) }}">
                    @csrf
                    <button type="submit"
                            class="px-4 py-2 rounded-lg bg-red-600 text-white hover:bg-red-700 text-sm">
                        Cancel booking
                    </button>
                </form>
            @endif
        </div>
    </div>
</div>
@endsection

