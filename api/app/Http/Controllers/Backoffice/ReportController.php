<?php

namespace App\Http\Controllers\Backoffice;

use App\Domain\Listening\Enums\ReportStatus;
use App\Domain\Listening\Models\Report;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class ReportController extends Controller
{
    public const RESOLUTIONS = [
        'dismissed' => 'Classer sans suite',
        'warned' => 'Avertissement envoyé',
        'suspended' => 'Suspendre le compte signalé',
    ];

    public function index(Request $request): View
    {
        $status = ReportStatus::tryFrom((string) $request->query('statut')) ?? ReportStatus::Pending;

        return view('backoffice.reports.index', [
            'reports' => Report::query()
                ->with(['reporter', 'reportedUser', 'message', 'moderator'])
                ->where('status', $status)
                ->latest()
                ->paginate(25)
                ->withQueryString(),
            'status' => $status,
        ]);
    }

    public function resolve(Request $request, Report $report): RedirectResponse
    {
        $data = $request->validate(['resolution' => ['required', Rule::in(array_keys(self::RESOLUTIONS))]]);

        if ($data['resolution'] === 'suspended' && $report->reportedUser) {
            $report->reportedUser->forceFill(['suspended_at' => now(), 'is_available' => false])->save();
            $report->reportedUser->tokens()->delete();
        }

        $report->update([
            'status' => $data['resolution'] === 'dismissed' ? ReportStatus::Dismissed : ReportStatus::Resolved,
            'resolution' => $data['resolution'],
            'moderator_id' => $request->user()->id,
            'resolved_at' => now(),
        ]);

        activity('moderation')->performedOn($report)->withProperties($data)->log('Signalement traité');

        return back()->with('status', 'Signalement traité.');
    }
}
