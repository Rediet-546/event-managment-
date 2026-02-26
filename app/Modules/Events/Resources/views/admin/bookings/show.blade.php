@extends('layouts.public')

@section('title', 'Admin - Booking')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
    <h1 class="text-2xl font-semibold text-gray-900 mb-2">Booking</h1>
    <p class="text-gray-600 mb-6 font-mono">{{ $booking->booking_reference }}</p>

    <div class="bg-white shadow rounded-xl p-6 space-y-3">
        <div><span class="text-gray-500 text-sm">Event:</span> {{ $booking->event?->title }}</div>
        <div><span class="text-gray-500 text-sm">User:</span> {{ $booking->user?->email }}</div>
        <div><span class="text-gray-500 text-sm">Tickets:</span> {{ $booking->tickets_count }}</div>
        <div><span class="text-gray-500 text-sm">Amount:</span> {{ $booking->amount_paid }}</div>
        <div><span class="text-gray-500 text-sm">Status:</span> {{ ucfirst($booking->status) }}</div>
        <div><span class="text-gray-500 text-sm">Checked in:</span> {{ $booking->checked_in_at ? 'Yes' : 'No' }}</div>
    </div>
</div>
@endsection

