@extends('layouts.public')

@section('title', 'Manage Bookings')

@section('content')
<div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-semibold text-gray-900">Manage Bookings</h1>
            <p class="text-gray-600">{{ $event->title }}</p>
        </div>
        <a href="{{ route('events.show', $event->slug) }}" class="text-sm text-blue-600 hover:text-blue-700">
            View event
        </a>
    </div>

    @if (session('success'))
        <div class="mb-4 p-4 rounded-lg bg-green-50 text-green-700">
            {{ session('success') }}
        </div>
    @endif

    <div class="bg-white shadow rounded-xl overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Ref</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">User</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Tickets</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Status</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Check-in</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @forelse ($bookings as $booking)
                    <tr>
                        <td class="px-4 py-3 font-mono text-sm text-gray-900">{{ $booking->booking_reference }}</td>
                        <td class="px-4 py-3 text-sm text-gray-700">{{ $booking->user?->name ?? '—' }}</td>
                        <td class="px-4 py-3 text-sm text-gray-700">{{ $booking->tickets_count }}</td>
                        <td class="px-4 py-3 text-sm text-gray-700">{{ ucfirst($booking->status) }}</td>
                        <td class="px-4 py-3 text-sm">
                            @if ($booking->checked_in_at)
                                <span class="text-green-700">Checked in</span>
                            @else
                                <form method="POST" action="{{ route('events.check-in', ['event' => $event->slug, 'booking' => $booking->id]) }}">
                                    @csrf
                                    <button type="submit" class="text-blue-600 hover:text-blue-700">
                                        Check in
                                    </button>
                                </form>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-4 py-10 text-center text-gray-600">No bookings yet.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection

