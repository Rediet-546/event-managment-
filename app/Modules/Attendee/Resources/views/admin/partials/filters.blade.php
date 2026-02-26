@props(['filters' => [], 'route' => null])

<div class="card card-primary card-outline card-outline-tabs">
    <div class="card-header p-0 border-bottom-0">
        <ul class="nav nav-tabs" id="filter-tabs" role="tablist">
            <li class="nav-item">
                <a class="nav-link active" id="filter-basic-tab" data-toggle="pill" href="#filter-basic" role="tab">
                    <i class="fas fa-filter"></i> Basic Filters
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" id="filter-advanced-tab" data-toggle="pill" href="#filter-advanced" role="tab">
                    <i class="fas fa-sliders-h"></i> Advanced Filters
                </a>
            </li>
        </ul>
    </div>
    
    <div class="card-body">
        <div class="tab-content">
            <!-- Basic Filters -->
            <div class="tab-pane active" id="filter-basic" role="tabpanel">
                <form method="GET" action="{{ $route ?? request()->url() }}" class="form-horizontal">
                    <div class="form-group row">
                        <label for="search" class="col-sm-2 col-form-label">Search</label>
                        <div class="col-sm-10">
                            <input type="text" name="search" id="search" class="form-control" 
                                   placeholder="Search by name, email, booking #..." 
                                   value="{{ request('search') }}">
                        </div>
                    </div>
                    
                    <div class="form-group row">
                        <label for="status" class="col-sm-2 col-form-label">Status</label>
                        <div class="col-sm-10">
                            <select name="status" id="status" class="form-control">
                                <option value="">All Status</option>
                                <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                                <option value="confirmed" {{ request('status') == 'confirmed' ? 'selected' : '' }}>Confirmed</option>
                                <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                                <option value="refunded" {{ request('status') == 'refunded' ? 'selected' : '' }}>Refunded</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="form-group row">
                        <label for="date_range" class="col-sm-2 col-form-label">Date Range</label>
                        <div class="col-sm-10">
                            <div class="input-group">
                                <input type="date" name="date_from" class="form-control" 
                                       value="{{ request('date_from') }}" placeholder="From">
                                <input type="date" name="date_to" class="form-control" 
                                       value="{{ request('date_to') }}" placeholder="To">
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-group row">
                        <div class="col-sm-10 offset-sm-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-search"></i> Apply Filters
                            </button>
                            <a href="{{ $route ?? request()->url() }}" class="btn btn-secondary">
                                <i class="fas fa-undo"></i> Reset
                            </a>
                        </div>
                    </div>
                </form>
            </div>
            
            <!-- Advanced Filters -->
            <div class="tab-pane" id="filter-advanced" role="tabpanel">
                <form method="GET" action="{{ $route ?? request()->url() }}" class="form-horizontal">
                    @foreach($filters as $filter)
                    <div class="form-group row">
                        <label for="{{ $filter['name'] }}" class="col-sm-2 col-form-label">{{ $filter['label'] }}</label>
                        <div class="col-sm-10">
                            @if($filter['type'] == 'select')
                                <select name="{{ $filter['name'] }}" id="{{ $filter['name'] }}" class="form-control">
                                    <option value="">All</option>
                                    @foreach($filter['options'] as $value => $label)
                                        <option value="{{ $value }}" {{ request($filter['name']) == $value ? 'selected' : '' }}>
                                            {{ $label }}
                                        </option>
                                    @endforeach
                                </select>
                            @elseif($filter['type'] == 'number')
                                <input type="number" name="{{ $filter['name'] }}" id="{{ $filter['name'] }}" 
                                       class="form-control" value="{{ request($filter['name']) }}"
                                       placeholder="{{ $filter['placeholder'] ?? '' }}">
                            @else
                                <input type="text" name="{{ $filter['name'] }}" id="{{ $filter['name'] }}" 
                                       class="form-control" value="{{ request($filter['name']) }}"
                                       placeholder="{{ $filter['placeholder'] ?? '' }}">
                            @endif
                        </div>
                    </div>
                    @endforeach
                    
                    <div class="form-group row">
                        <div class="col-sm-10 offset-sm-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-search"></i> Apply Advanced Filters
                            </button>
                            <a href="{{ $route ?? request()->url() }}" class="btn btn-secondary">
                                <i class="fas fa-undo"></i> Reset
                            </a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>