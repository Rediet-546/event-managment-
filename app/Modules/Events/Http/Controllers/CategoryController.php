<?php

namespace App\Modules\Events\Http\Controllers;

use App\Modules\Core\Base\BaseController;
use App\Modules\Events\Models\EventCategory;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CategoryController extends BaseController
{
    /**
     * Display all categories.
     */
    public function index(): View
    {
        $categories = EventCategory::active()
            ->ordered()
            ->withCount('events')
            ->get();

        return view('events::categories.index', compact('categories'));
    }

    /**
     * Display events in a category.
     */
    public function show(EventCategory $category, Request $request): View
    {
        $events = $category->events()
            ->published()
            ->upcoming()
            ->with(['organizer', 'primaryMedia'])
            ->paginate(12);

        return view('events::categories.show', compact('category', 'events'));
    }
}