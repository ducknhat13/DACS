<?php

namespace App\Http\Controllers;

use App\Models\Poll;
use App\Models\Vote;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

/**
 * StatsController - Controller xử lý trang History/Statistics
 * 
 * Controller này hiển thị thống kê và lịch sử Poll của user:
 * - Summary counts: Tổng số polls đã tạo, đã tham gia, tổng votes nhận được
 * - Top polls: Polls có nhiều votes nhất
 * - Poll list: Danh sách polls (created hoặc joined) với filter và sort
 * - Charts data: 
 *   + Votes by day (line chart) - Votes trên polls của user và votes user đã tham gia
 *   + Type distribution (doughnut chart) - Phân bố theo loại poll (standard/ranking/image)
 * 
 * @author QuickPoll Team
 */
class StatsController extends Controller
{
    /**
     * Hiển thị trang History/Statistics
     * 
     * Parameters:
     * - scope: 'created' (polls user đã tạo) hoặc 'joined' (polls user đã vote)
     * - from/to: Date range filter
     * - sort: 'created_desc' | 'votes_desc' | 'activity_desc'
     * 
     * @param Request $request
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $scope = $request->string('scope', 'created')->toString(); // created|joined
        $from = $request->date('from');
        $to = $request->date('to');

        // Summary counts
        $createdCount = Poll::where('user_id', $user->id)->count();
        $joinedCount = Vote::where('user_id', $user->id)->distinct('poll_id')->count('poll_id');
        $totalVotesReceived = Vote::whereIn('poll_id', function($q) use ($user){
            $q->select('id')->from('polls')->where('user_id', $user->id);
        })->count();

        // Top polls by votes (owned by user)
        $topPolls = Poll::withCount('votes')
            ->where('user_id', $user->id)
            ->orderByDesc('votes_count')
            ->limit(5)
            ->get(['id','title','question','slug']);

        // Poll list by scope
        $sort = $request->string('sort', 'created_desc')->toString(); // created_desc|votes_desc|activity_desc
        $order = match ($sort) {
            'votes_desc' => ['votes_count','desc'],
            'activity_desc' => ['updated_at','desc'],
            default => ['id','desc'],
        };

        if ($scope === 'joined') {
            $pollIds = Vote::where('user_id', $user->id)
                ->when($from, fn($q) => $q->whereDate('created_at', '>=', $from))
                ->when($to, fn($q) => $q->whereDate('created_at', '<=', $to))
                ->distinct('poll_id')
                ->pluck('poll_id');
            $polls = Poll::with(['user'])->withCount('votes')
                ->whereIn('id', $pollIds)
                ->orderBy($order[0], $order[1])
                ->paginate(12);
        } else {
            $polls = Poll::with(['user'])->withCount('votes')
                ->where('user_id', $user->id)
                ->when($from, fn($q) => $q->whereDate('created_at', '>=', $from))
                ->when($to, fn($q) => $q->whereDate('created_at', '<=', $to))
                ->orderBy($order[0], $order[1])
                ->paginate(12);
        }

        // Charts data
        // Votes by day - owned polls ("My polls")
        $ownedVotesQuery = Vote::join('polls', 'votes.poll_id', '=', 'polls.id')
            ->where('polls.user_id', $user->id);
        if ($from) { $ownedVotesQuery->whereDate('votes.created_at', '>=', $from); }
        if ($to) { $ownedVotesQuery->whereDate('votes.created_at', '<=', $to); }
        $ownedByDay = $ownedVotesQuery
            ->select(DB::raw('DATE(votes.created_at) as d'), DB::raw('COUNT(*) as c'))
            ->groupBy('d')
            ->orderBy('d')
            ->pluck('c', 'd')
            ->toArray();

        // Votes by day - joined polls (votes made by current user)
        $joinedVotesQuery = Vote::where('user_id', $user->id);
        if ($from) { $joinedVotesQuery->whereDate('created_at', '>=', $from); }
        if ($to) { $joinedVotesQuery->whereDate('created_at', '<=', $to); }
        $joinedByDay = $joinedVotesQuery
            ->select(DB::raw('DATE(created_at) as d'), DB::raw('COUNT(*) as c'))
            ->groupBy('d')
            ->orderBy('d')
            ->pluck('c', 'd')
            ->toArray();

        // Build unified labels (union of dates)
        $labels = array_values(array_unique(array_merge(array_keys($ownedByDay), array_keys($joinedByDay))));
        sort($labels);
        $myData = array_map(fn($d) => (int)($ownedByDay[$d] ?? 0), $labels);
        $joinedData = array_map(fn($d) => (int)($joinedByDay[$d] ?? 0), $labels);

        // Type distribution
        // Note: poll_type values in database are 'standard', 'ranking', 'image'
        $pollTypeCounts = ['standard' => 0, 'ranking' => 0, 'image' => 0];
        if ($scope === 'joined') {
            // Get poll IDs that user has voted on (respecting date filters)
            $joinedPollIds = Vote::where('user_id', $user->id)
                ->when($from, fn($q) => $q->whereDate('created_at', '>=', $from))
                ->when($to, fn($q) => $q->whereDate('created_at', '<=', $to))
                ->distinct('poll_id')
                ->pluck('poll_id');
            $typeRows = Poll::whereIn('id', $joinedPollIds)
                ->select('poll_type', DB::raw('COUNT(*) as c'))
                ->groupBy('poll_type')
                ->pluck('c', 'poll_type')
                ->toArray();
        } else {
            $typeRows = Poll::where('user_id', $user->id)
                ->when($from, fn($q) => $q->whereDate('created_at', '>=', $from))
                ->when($to, fn($q) => $q->whereDate('created_at', '<=', $to))
                ->select('poll_type', DB::raw('COUNT(*) as c'))
                ->groupBy('poll_type')
                ->pluck('c', 'poll_type')
                ->toArray();
        }
        foreach ($typeRows as $type => $count) {
            if (array_key_exists($type, $pollTypeCounts)) {
                $pollTypeCounts[$type] = (int)$count;
            }
        }

        return view('stats.index', [
            'scope' => $scope,
            'createdCount' => $createdCount,
            'joinedCount' => $joinedCount,
            'totalVotesReceived' => $totalVotesReceived,
            'topPolls' => $topPolls,
            'polls' => $polls,
            'from' => $from?->format('Y-m-d'),
            'to' => $to?->format('Y-m-d'),
            'sort' => $sort,
            'chartLabels' => $labels,
            'chartMyVotes' => $myData,
            'chartJoinedVotes' => $joinedData,
            'chartTypeDistribution' => [
                'standard' => $pollTypeCounts['standard'] ?? 0,
                'ranking' => $pollTypeCounts['ranking'] ?? 0,
                'image' => $pollTypeCounts['image'] ?? 0,
            ],
        ]);
    }
}


