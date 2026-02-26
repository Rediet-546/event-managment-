@extends('layouts.public')

@section('title', 'Pending Creators')

@section('content')
<div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
    <h1 class="text-2xl font-semibold text-gray-900 mb-6">Pending Creator Approvals</h1>

    @if (session('success'))
        <div class="mb-4 p-4 rounded-lg bg-green-50 text-green-700">
            {{ session('success') }}
        </div>
    @endif

    <div class="bg-white shadow rounded-xl overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Name</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Email</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Created</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @forelse ($creators as $creator)
                    <tr>
                        <td class="px-4 py-3 text-sm text-gray-900">{{ $creator->name }}</td>
                        <td class="px-4 py-3 text-sm text-gray-700">{{ $creator->email }}</td>
                        <td class="px-4 py-3 text-sm text-gray-700">{{ $creator->created_at?->toDateTimeString() }}</td>
                        <td class="px-4 py-3 text-sm">
                            <div class="flex gap-2">
                                <form method="POST" action="{{ route('admin.creators.approve', $creator) }}">
                                    @csrf
                                    <button type="submit" class="px-3 py-1 rounded bg-green-600 text-white hover:bg-green-700">
                                        Approve
                                    </button>
                                </form>
                                <form method="POST" action="{{ route('admin.creators.reject', $creator) }}">
                                    @csrf
                                    <button type="submit" class="px-3 py-1 rounded bg-red-600 text-white hover:bg-red-700">
                                        Reject
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="px-4 py-10 text-center text-gray-600">No pending creators.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-6">
        {{ $creators->links() }}
    </div>
</div>
@endsection

