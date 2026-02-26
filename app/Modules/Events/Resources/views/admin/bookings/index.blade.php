@extends('layouts.public')

@section('title', 'Admin - Bookings')

@section('content')
<div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
    <h1 class="text-2xl font-semibold text-gray-900 mb-6">Bookings</h1>

    <div class="bg-white shadow rounded-xl overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Ref</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Event</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">User</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Status</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Created</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @foreach ($bookings as $booking)
                    <tr>
                        <td class="px-4 py-3 font-mono text-sm">{{ $booking->booking_reference }}</td>
                        <td class="px-4 py-3 text-sm">{{ $booking->event?->title }}</td>
                        <td class="px-4 py-3 text-sm">{{ $booking->user?->email }}</td>
                        <td class="px-4 py-3 text-sm">{{ ucfirst($booking->status) }}</td>
                        <td class="px-4 py-3 text-sm">{{ $booking->created_at?->toDateTimeString() }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="mt-6">
        {{ $bookings->links() }}
    </div>
</div>
@endsection

