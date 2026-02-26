@extends('layouts.public')

@section('title', 'Admin - Booking Analytics')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
    <h1 class="text-2xl font-semibold text-gray-900 mb-6">Booking Analytics</h1>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div class="bg-white shadow rounded-xl p-6">
            <div class="text-sm text-gray-500">Total bookings</div>
            <div class="text-2xl font-semibold text-gray-900">{{ $stats['total'] }}</div>
        </div>
        <div class="bg-white shadow rounded-xl p-6">
            <div class="text-sm text-gray-500">Confirmed</div>
            <div class="text-2xl font-semibold text-gray-900">{{ $stats['confirmed'] }}</div>
        </div>
        <div class="bg-white shadow rounded-xl p-6">
            <div class="text-sm text-gray-500">Cancelled</div>
            <div class="text-2xl font-semibold text-gray-900">{{ $stats['cancelled'] }}</div>
        </div>
        <div class="bg-white shadow rounded-xl p-6">
            <div class="text-sm text-gray-500">Checked in</div>
            <div class="text-2xl font-semibold text-gray-900">{{ $stats['checked_in'] }}</div>
        </div>
        <div class="bg-white shadow rounded-xl p-6 md:col-span-2">
            <div class="text-sm text-gray-500">Revenue (confirmed)</div>
            <div class="text-2xl font-semibold text-gray-900">{{ number_format($stats['revenue'], 2) }}</div>
        </div>
    </div>
</div>
@endsection

