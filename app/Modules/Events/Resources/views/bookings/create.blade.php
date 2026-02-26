@extends('layouts.public')

@section('title', 'Book Ticket')

@section('content')
<div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
    <div class="bg-white shadow rounded-xl p-6">
        <h1 class="text-2xl font-semibold text-gray-900 mb-2">Book Ticket</h1>
        <p class="text-gray-600 mb-6">{{ $event->title }}</p>

        @if ($errors->any())
            <div class="mb-4 p-4 rounded-lg bg-red-50 text-red-700">
                <ul class="list-disc pl-5 space-y-1">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('events.process-booking', $event->slug) }}" class="space-y-5">
            @csrf

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Number of tickets</label>
                <input type="number" name="tickets" min="1" value="{{ old('tickets', 1) }}"
                       class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500" />
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Attendees</label>
                <div class="space-y-3">
                    @for ($i = 0; $i < 5; $i++)
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                            <input type="text" name="attendees[{{ $i }}][name]" value="{{ old("attendees.$i.name") }}"
                                   placeholder="Name"
                                   class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500" />
                            <input type="email" name="attendees[{{ $i }}][email]" value="{{ old("attendees.$i.email") }}"
                                   placeholder="Email"
                                   class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500" />
                        </div>
                    @endfor
                </div>
                <p class="text-xs text-gray-500 mt-2">Fill in as many attendees as tickets you’re booking.</p>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Payment method (optional)</label>
                <input type="text" name="payment_method" value="{{ old('payment_method') }}" placeholder="e.g. stripe"
                       class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500" />
            </div>

            <div class="flex items-start gap-2">
                <input type="checkbox" name="terms_accepted" value="1" {{ old('terms_accepted') ? 'checked' : '' }}
                       class="mt-1 rounded border-gray-300 text-blue-600 focus:ring-blue-500" />
                <label class="text-sm text-gray-700">I accept the terms and conditions</label>
            </div>

            <div class="flex justify-end">
                <button type="submit"
                        class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-2 rounded-lg font-medium transition">
                    Confirm Booking
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

