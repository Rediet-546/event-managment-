<div class="bg-white rounded-lg shadow-sm p-6 mb-8">
    <form method="GET" action="{{ route('events.index') }}" class="space-y-4">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Category</label>
                <select name="category" 
                        class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    <option value="">All Categories</option>
                    @foreach($categories as $category)
                        <option value="{{ $category->id }}" {{ request('category') == $category->id ? 'selected' : '' }}>
                            {{ $category->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">City</label>
                <input type="text" 
                       name="city" 
                       value="{{ request('city') }}"
                       placeholder="Any city"
                       class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Date</label>
                <select name="date_range" 
                        class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    <option value="">Any date</option>
                    <option value="today" {{ request('date_range') == 'today' ? 'selected' : '' }}>Today</option>
                    <option value="tomorrow" {{ request('date_range') == 'tomorrow' ? 'selected' : '' }}>Tomorrow</option>
                    <option value="this_week" {{ request('date_range') == 'this_week' ? 'selected' : '' }}>This Week</option>
                    <option value="this_month" {{ request('date_range') == 'this_month' ? 'selected' : '' }}>This Month</option>
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Price</label>
                <select name="price_range" 
                        class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    <option value="">Any price</option>
                    <option value="free" {{ request('price_range') == 'free' ? 'selected' : '' }}>Free</option>
                    <option value="paid" {{ request('price_range') == 'paid' ? 'selected' : '' }}>Paid</option>
                </select>
            </div>
        </div>

        <div class="flex justify-end gap-2">
            <button type="submit" 
                    class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg font-medium transition duration-150">
                Apply Filters
            </button>
            <a href="{{ route('events.index') }}" 
               class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-6 py-2 rounded-lg font-medium transition duration-150">
                Clear
            </a>
        </div>
    </form>
</div>