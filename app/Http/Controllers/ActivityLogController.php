<?php

namespace App\Http\Controllers;

use App\Models\ClaimReview;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ActivityLogController extends Controller
{
    public function index(Request $request): View
    {
        $q = trim((string) $request->get('q', ''));
        $actionType = trim((string) $request->get('action_type', ''));
        $reviewerRole = trim((string) $request->get('reviewer_role', ''));

        $query = ClaimReview::query()
            ->with([
                'episode:id,episode_no,patient_name,mrn,service_unit',
            ])
            ->latest();

        if ($q !== '') {
            $query->where(function ($sub) use ($q) {
                $sub->where('reviewer_name', 'like', "%{$q}%")
                    ->orWhere('reviewer_role', 'like', "%{$q}%")
                    ->orWhere('action_type', 'like', "%{$q}%")
                    ->orWhere('notes', 'like', "%{$q}%")
                    ->orWhereHas('episode', function ($episodeQuery) use ($q) {
                        $episodeQuery->where('episode_no', 'like', "%{$q}%")
                            ->orWhere('patient_name', 'like', "%{$q}%")
                            ->orWhere('mrn', 'like', "%{$q}%")
                            ->orWhere('service_unit', 'like', "%{$q}%");
                    });
            });
        }

        if ($actionType !== '') {
            $query->where('action_type', $actionType);
        }

        if ($reviewerRole !== '') {
            $query->where('reviewer_role', $reviewerRole);
        }

        $logs = $query->paginate(15)->withQueryString();

        $stats = [
            'total' => ClaimReview::count(),
            'today' => ClaimReview::whereDate('created_at', today())->count(),
            'verification_updates' => ClaimReview::where('action_type', 'verification_item_updated')->count(),
            'followup_updates' => ClaimReview::whereIn('action_type', ['follow_up_created', 'follow_up_updated'])->count(),
            'workflow_updates' => ClaimReview::whereIn('action_type', ['workflow_updated', 'dashboard_status_update', 'test_status_update'])->count(),
            'ai_updates' => ClaimReview::whereIn('action_type', ['ai_result_saved', 'audit_flags_saved'])->count(),
        ];

        $actionTypes = ClaimReview::query()
            ->select('action_type')
            ->whereNotNull('action_type')
            ->distinct()
            ->orderBy('action_type')
            ->pluck('action_type');

        $reviewerRoles = ClaimReview::query()
            ->select('reviewer_role')
            ->whereNotNull('reviewer_role')
            ->distinct()
            ->orderBy('reviewer_role')
            ->pluck('reviewer_role');

        return view('activity.index', compact(
            'logs',
            'stats',
            'q',
            'actionType',
            'reviewerRole',
            'actionTypes',
            'reviewerRoles'
        ));
    }
}