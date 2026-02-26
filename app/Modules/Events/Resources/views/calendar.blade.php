@extends('layouts.app')

@section('title', 'Events Calendar')

@section('content')
<div class="container mx-auto px-4 py-8">
    <!-- Header -->
    <div class="flex justify-between items-center mb-8">
        <h1 class="text-3xl font-bold text-gray-900">Events Calendar</h1>
        <div class="flex gap-2">
            <a href="{{ route('events.calendar', ['month' => $month-1, 'year' => $year]) }}" 
               class="px-4 py-2 bg-gray-200 rounded-md hover:bg-gray-300 transition">
                &larr; Previous
            </a>
            <h2 class="px-4 py-2 bg-blue-600 text-white rounded-md">
                {{ DateTime::createFromFormat('!m', $month)->format('F') }} {{ $year }}
            </h2>
            <a href="{{ route('events.calendar', ['month' => $month+1, 'year' => $year]) }}" 
               class="px-4 py-2 bg-gray-200 rounded-md hover:bg-gray-300 transition">
                Next &rarr;
            </a>
        </div>
    </div>

    <!-- Calendar Grid -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <!-- Day Names -->
        <div class="grid grid-cols-7 bg-gray-50 border-b">
            @foreach(['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'] as $day)
                <div class="py-3 text-center text-sm font-semibold text-gray-700">{{ $day }}</div>
            @endforeach
        </div>

        <!-- Calendar Days -->
        @php
            $date = Carbon\Carbon::createFromDate($year, $month, 1);
            $startOfCalendar = $date->copy()->startOfWeek();
            $endOfCalendar = $date->copy()->endOfMonth()->endOfWeek();
            
            $eventsByDate = $events->groupBy(function($event) {
                return $event->start_date->format('Y-m-d');
            });
        @endphp

        <div class="grid grid-cols-7 divide-x divide-y">
            @for($day = $startOfCalendar; $day <= $endOfCalendar; $day->addDay())
                @php
                    $isCurrentMonth = $day->month == $month;
                    $dateKey = $day->format('Y-m-d');
                    $dayEvents = $eventsByDate->get($dateKey, collect());
                    $isToday = $day->isToday();
                @endphp

                <div class="min-h-32 p-2 {{ $isCurrentMonth ? 'bg-white' : 'bg-gray-50 text-gray-400' }} {{ $isToday ? 'bg-blue-50' : '' }}">
                    <!-- Day Number -->
                    <div class="flex justify-between items-start mb-1">
                        <span class="text-sm {{ $isToday ? 'bg-blue-600 text-white w-6 h-6 flex items-center justify-center rounded-full' : '' }}">
                            {{ $day->format('j') }}
                        </span>
                        @if($dayEvents->count() > 0)
                            <span class="text-xs bg-blue-100 text-blue-800 px-2 py-1 rounded-full">
                                {{ $dayEvents->count() }}
                            </span>
                        @endif
                    </div>

                    <!-- Events -->
                    <div class="space-y-1 max-h-32 overflow-y-auto">
                        @foreach($dayEvents->take(3) as $event)
                            <a href="{{ route('events.show', $event) }}" 
                               class="block p-1 text-xs rounded {{ $event->is_featured ? 'bg-yellow-100 hover:bg-yellow-200' : 'bg-blue-50 hover:bg-blue-100' }} transition">
                                <div class="font-medium truncate">{{ $event->title }}</div>
                                <div class="text-gray-600 text-xs">{{ $event->start_date->format('g:i A') }}</div>
                            </a>
                        @endforeach
                        
                        @if($dayEvents->count() > 3)
                            <div class="text-xs text-gray-500 text-center">
                                +{{ $dayEvents->count() - 3 }} more
                            </div>
                        @endif
                    </div>
                </div>
            @endfor
        </div>
    </div>

    <!-- Legend -->
    <div class="mt-6 flex items-center gap-6 text-sm">
        <div class="flex items-center">
            <span class="w-3 h-3 bg-blue-50 border border-blue-200 rounded mr-2"></span>
            <span>Regular Event</span>
        </div>
        <div class="flex items-center">
            <span class="w-3 h-3 bg-yellow-100 border border-yellow-200 rounded mr-2"></span>
            <span>Featured Event</span>
        </div>
        <div class="flex items-center">
            <span class="w-3 h-3 bg-blue-600 text-white flex items-center justify-center rounded-full mr-2">21</span>
            <span>Today</span>
        </div>
    </div>

    <!-- Upcoming Events List -->
    @if($events->isNotEmpty())
    <div class="mt-12">
        <h2 class="text-2xl font-bold mb-6">Events This Month</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($events as $event)
                @include('events::partials.event-card', ['event' => $event])
            @endforeach
        </div>
    </div>
    @endif
</div>
@endsection

@push('styles')
<style>
    .min-h-32 {
        min-height: 8rem;
    }
</style>
@endpush