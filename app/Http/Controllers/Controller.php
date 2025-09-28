<?php

namespace App\Http\Controllers;

use App\Models\Poll;
use Illuminate\Support\Facades\Auth;

abstract class Controller
{
    protected function dashboardData(): array
    {
        if (!Auth::check()) {
            return [];
        }

        return [
            'polls' => Poll::withCount('votes')
                ->where('user_id', Auth::id())
                ->latest('id')
                ->take(20)
                ->get(),
        ];
    }
}
