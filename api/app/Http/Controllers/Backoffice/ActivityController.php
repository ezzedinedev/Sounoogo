<?php

namespace App\Http\Controllers\Backoffice;

use App\Http\Controllers\Controller;
use Illuminate\View\View;
use Spatie\Activitylog\Models\Activity;

class ActivityController extends Controller
{
    public function __invoke(): View
    {
        return view('backoffice.activity.index', [
            'activities' => Activity::with('causer')->latest()->paginate(50),
        ]);
    }
}
