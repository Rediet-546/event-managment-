<?php

namespace App\Modules\Registration\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Modules\Registration\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;

class CreatorApprovalController extends Controller
{
    public function pending()
    {
        $creators = User::query()
            ->where('user_type', 'event_creator')
            ->where('is_approved', false)
            ->orderByDesc('created_at')
            ->paginate(25);

        return view('registration::admin.creators.pending', compact('creators'));
    }

    public function approve(User $user)
    {
        $user->update([
            'is_approved' => true,
            'approved_at' => now(),
            'approved_by' => auth()->id(),
        ]);

        try {
            $user->assignRole('event_creator');
        } catch (\Throwable $e) {
            // ignore if role table not ready
        }

        return back()->with('success', 'Creator approved.');
    }

    public function reject(User $user)
    {
        $user->update([
            'is_approved' => false,
            'approved_at' => null,
            'approved_by' => null,
        ]);

        return back()->with('success', 'Creator rejected.');
    }

    public function bulkApprove(Request $request)
    {
        $validated = $request->validate([
            'user_ids' => ['required', 'array', 'min:1'],
            'user_ids.*' => ['integer'],
        ]);

        DB::transaction(function () use ($validated) {
            User::whereIn('id', $validated['user_ids'])
                ->update([
                    'is_approved' => true,
                    'approved_at' => now(),
                    'approved_by' => auth()->id(),
                ]);
        });

        return back()->with('success', 'Selected creators approved.');
    }

    public function export(): Response
    {
        $rows = User::query()
            ->where('user_type', 'event_creator')
            ->orderByDesc('created_at')
            ->limit(5000)
            ->get(['id', 'name', 'email', 'is_approved', 'approved_at', 'created_at']);

        $csv = "id,name,email,is_approved,approved_at,created_at\n";
        foreach ($rows as $u) {
            $csv .= '"' . implode('","', [
                (string) $u->id,
                str_replace('"', '""', (string) $u->name),
                str_replace('"', '""', (string) $u->email),
                $u->is_approved ? '1' : '0',
                (string) $u->approved_at,
                (string) $u->created_at,
            ]) . "\"\n";
        }

        return response($csv, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename=\"creators.csv\"',
        ]);
    }
}

