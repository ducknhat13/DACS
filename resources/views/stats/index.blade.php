{{--
    Page: stats/index
    - Trang thống kê lịch sử polls: bảng/list polls và thao tác xoá đơn lẻ.
    - Đã dùng modal confirm xoá (singleDeleteModal) thay cho confirm() native.
    - Frontend: đồng nhất cùng dashboard về style modal và JS openDeleteModal.
--}}
{{--
    History/Stats Page - stats/index.blade.php
    
    Trang thống kê và lịch sử polls của user với Material Design 3 UI.
    
    Features:
    - Overview Cards: Summary counts (created, joined, votes received, top polls)
    - Charts: Chart.js charts (votes by day, type distribution)
    - Filters: Search, date range, scope (created/joined)
    - Poll List: Table/list view với pagination và sorting
    - Export: Export to CSV functionality
    
    Charts:
    - Votes by Day: Line chart với 2 datasets (My polls, Joined polls)
    - Type Distribution: Doughnut chart (Standard, Ranking, Image)
    
    Data từ StatsController:
    - $createdCount, $joinedCount, $totalVotesReceived
    - $topPolls: Top 5 polls by votes
    - $polls: Paginated poll list
    - $chartLabels, $chartMyVotes, $chartJoinedVotes: Chart data
    - $chartTypeDistribution: Type distribution data
    
    JavaScript:
    - Chart.js initialization: Render charts với dynamic data
    - Filter chips: Dynamic filter với Material Design chips
    
    @author QuickPoll Team
--}}
<x-app-layout>
    <x-slot name="header">
        <div class="container-material">
            <div class="flex-between">
                <div>
                    <h1 class="text-headline-large text-[color:var(--on-surface)]">{{ __('messages.history_title') }}</h1>
                    <p class="text-body-medium text-[color:var(--on-surface-variant)] mt-1">{{ __('messages.history_subtitle') }}</p>
                </div>
                <div class="flex items-center gap-2">
                    {{-- Nút Export CSV theo scope hiện tại --}}
                    <a href="{{ request()->fullUrlWithQuery(['scope' => $scope, 'export' => 'csv']) }}" class="btn btn-neutral" title="{{ __('messages.export_to_csv') }}"><i class="fa-solid fa-file-export"></i></a>
                    {{-- Nút Refresh danh sách --}}
                    <a href="{{ route('stats.index', ['scope' => $scope]) }}" class="btn btn-neutral" title="{{ __('messages.refresh') }}"><i class="fa-solid fa-rotate-right"></i></a>
                </div>
            </div>
        </div>
    </x-slot>

    <div class="section-padding-sm">
        <div class="container-material space-y-6">
            {{-- Filters: Search, date range, scope --}}
            <div class="card p-4">
                <form method="GET" class="flex flex-col lg:flex-row gap-3 items-start lg:items-center">
                    <input type="hidden" name="scope" value="{{ $scope }}">
                    <div class="input-field min-w-[260px]">
                        <input type="text" name="q" value="{{ request('q') }}" placeholder=" ">
                        <label>{{ __('messages.search_title_slug') }}</label>
                    </div>
                    <div class="flex items-center gap-2 flex-wrap">
                        <div class="input-field">
                            <input type="date" name="from" value="{{ $from }}" placeholder=" ">
                            <label>{{ __('messages.from_date') }}</label>
                        </div>
                        <div class="input-field">
                            <input type="date" name="to" value="{{ $to }}" placeholder=" ">
                            <label>{{ __('messages.to_date') }}</label>
                        </div>
                        {{-- Chip: lọc phạm vi Created --}}
                        <button class="filter-chip {{ $scope==='created' ? 'active' : '' }}" name="scope" value="created" type="submit">{{ __('messages.scope_created') }}</button>
                        {{-- Chip: lọc phạm vi Joined --}}
                        <button class="filter-chip {{ $scope==='joined' ? 'active' : '' }}" name="scope" value="joined" type="submit">{{ __('messages.scope_joined') }}</button>
                        {{-- Xoá toàn bộ filter --}}
                        <a href="{{ route('stats.index') }}" class="assist-chip">{{ __('messages.clear_filters') }}</a>
                    </div>
                    <div class="ml-auto">
                        <button type="submit" class="btn btn-primary"><i class="fa-solid fa-filter"></i> {{ __('messages.filter') }}</button>
                    </div>
                </form>
            </div>

            {{-- Overview Cards: Summary statistics --}}
            <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-4 gap-4">
                {{-- Total Created Polls --}}
                <div class="card p-4">
                    <div class="text-title-small text-[color:var(--on-surface-variant)]">{{ __('messages.total_created_polls') }}</div>
                    <div class="text-display-small">{{ $createdCount }}</div>
                </div>
                {{-- Total Joined Polls --}}
                <div class="card p-4">
                    <div class="text-title-small text-[color:var(--on-surface-variant)]">{{ __('messages.total_joined_polls') }}</div>
                    <div class="text-display-small">{{ $joinedCount }}</div>
                </div>
                {{-- Total Votes Received --}}
                <div class="card p-4">
                    <div class="text-title-small text-[color:var(--on-surface-variant)]">{{ __('messages.total_votes_received') }}</div>
                    <div class="text-display-small">{{ $totalVotesReceived }}</div>
                </div>
                {{-- Top Polls by Votes --}}
                <div class="card p-4">
                    <div class="text-title-small text-[color:var(--on-surface-variant)]">{{ __('messages.top_interactions') }}</div>
                    <ul class="mt-2 space-y-1 text-body-medium">
                        @foreach($topPolls as $tp)
                            <li class="flex justify-between gap-3"><span class="truncate">{{ $tp->title ?? $tp->question }}</span><span class="font-semibold">{{ $tp->votes_count }}</span></li>
                        @endforeach
                        @if($topPolls->isEmpty())
                            <li class="text-[color:var(--on-surface-variant)]">{{ __('messages.no_data') }}</li>
                        @endif
                    </ul>
                </div>
            </div>

            {{-- Charts: Chart.js charts với dynamic data từ controller --}}
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
                {{-- Votes by Day: Line chart --}}
                <div class="card p-4 lg:col-span-2">
                    <div class="text-title-small mb-3">{{ __('messages.votes_by_day') }}</div>
                    <div class="relative" style="height:300px"><canvas id="chartVotesByDay"></canvas></div>
                </div>
                {{-- Type Distribution: Doughnut chart --}}
                <div class="card p-4">
                    <div class="text-title-small mb-3">{{ __('messages.type_distribution') }}</div>
                    <div class="relative" style="height:300px"><canvas id="chartTypeDistribution"></canvas></div>
                </div>
            </div>

            {{-- Tabs: Switch giữa "Created" và "Joined" scope --}}
            <div class="material-tabs">
                <a class="material-tab {{ $scope==='created' ? 'active' : '' }}" href="{{ route('stats.index', ['scope'=>'created'] + request()->except('page')) }}">{{ __('messages.scope_created') }}</a>
                <a class="material-tab {{ $scope==='joined' ? 'active' : '' }}" href="{{ route('stats.index', ['scope'=>'joined'] + request()->except('page')) }}">{{ __('messages.scope_joined') }}</a>
            </div>

            <!-- Poll Row-List -->
            <div class="card p-0 overflow-visible">
                <!-- Header row with sort -->
                <div class="hidden md:grid grid-cols-12 items-center px-3 py-2 border-b border-[color:var(--outline)] bg-[color:var(--surface-variant)]/30 text-sm text-[color:var(--on-surface-variant)] gap-3">
                    <div class="col-span-4 min-w-0">{{ __('messages.poll') }}</div>
                    <div class="col-span-1">{{ __('messages.poll_type') }}</div>
                    <div class="col-span-1">{{ __('messages.status') }}</div>
                    <div class="col-span-3 grid grid-cols-6 gap-4 min-w-0">
                        <a class="link block col-span-1" href="{{ request()->fullUrlWithQuery(['sort'=>'votes_desc']) }}">{{ __('messages.votes') }}</a>
                        <a class="link block col-span-2" href="{{ request()->fullUrlWithQuery(['sort'=>'created_desc']) }}">{{ __('messages.created') }}</a>
                        <a class="link block col-span-3" href="{{ request()->fullUrlWithQuery(['sort'=>'activity_desc']) }}">{{ __('messages.last_activity') }}</a>
                    </div>
                    <div class="col-span-1 text-right">{{ $scope==='joined' ? __('messages.owner') : '' }}</div>
                    <div class="col-span-2 text-right">&nbsp;</div>
                </div>
                @forelse($polls as $p)
                    @php $isClosed = (bool)($p->is_closed ?? false); @endphp
                    <div class="px-4 py-3 border-b border-[color:var(--outline)] hover:bg-[color:var(--surface-variant)]/30">
                        <div class="grid grid-cols-1 md:grid-cols-12 md:items-center gap-2 md:gap-3">
                        <!-- Primary cell -->
                        <a class="min-w-0 flex items-center gap-3 group md:col-span-4" href="{{ route('polls.show', $p->slug) }}" title="{{ $p->title ?? $p->question }}">
                            <i class="fa-solid fa-poll text-[color:var(--on-surface-variant)]"></i>
                            <div class="min-w-0">
                                <div class="font-medium poll-title-truncate">{{ $p->title ?? $p->question }}</div>
                                <div class="text-sm text-[color:var(--on-surface-variant)] truncate">/{{ $p->slug }}</div>
                            </div>
                        </a>

                        <!-- Type cell -->
                        <div class="hidden md:flex md:col-span-1 text-sm text-[color:var(--on-surface-variant)]">
                            {{ $p->poll_type === 'ranking' ? __('messages.poll_type_ranking') : ($p->poll_type === 'image' ? __('messages.poll_type_image') : __('messages.poll_type_standard')) }}
                        </div>
                        <!-- Status cell -->
                        <div class="hidden md:flex md:col-span-1 text-sm text-[color:var(--on-surface-variant)]">
                            <span class="px-2 py-0.5 rounded-full border border-[color:var(--outline)]">{{ $isClosed ? __('messages.closed') : __('messages.active') }}</span>
                        </div>

                        <!-- Meta cell -->
                        <div class="hidden md:grid grid-cols-6 items-center text-sm text-[color:var(--on-surface-variant)] md:col-span-3 min-w-0 gap-4">
                            <div class="flex items-center gap-2 col-span-1" title="{{ __('messages.votes') }}"><i class="fa-solid fa-chart-column"></i><span>{{ $p->votes_count }}</span></div>
                            <div class="flex items-center gap-2 col-span-2" title="{{ __('messages.created') }}"><i class="fa-solid fa-calendar"></i><span class="truncate">{{ optional($p->created_at)->format('d/m/Y') }}</span></div>
                            <div class="flex items-center gap-2 col-span-2" title="{{ optional($p->updated_at)->diffForHumans() }}">
                                <i class="fa-solid fa-bolt"></i>
                                @php
                                    $abbr = '-';
                                    if (!empty($p->updated_at)) {
                                        $updated = \Carbon\Carbon::parse($p->updated_at);
                                        $diff = $updated->diff(now());
                                        if ($diff->y > 0) { $abbr = $diff->y . 'y'; }
                                        elseif ($diff->m > 0) { $abbr = $diff->m . 'mo'; }
                                        elseif ($diff->d >= 7) { $abbr = intdiv($diff->d, 7) . 'w'; }
                                        elseif ($diff->d > 0) { $abbr = $diff->d . 'd'; }
                                        elseif ($diff->h > 0) { $abbr = $diff->h . 'h'; }
                                        elseif ($diff->i > 0) { $abbr = $diff->i . 'm'; }
                                        else { $abbr = $diff->s . 's'; }
                                        $abbr .= ' ago';
                                    }
                                @endphp
                                <span class="truncate">{{ $abbr }}</span>
                            </div>
                        </div>

                        <div class="hidden md:flex items-center gap-2 md:col-span-1 text-sm text-[color:var(--on-surface-variant)] truncate justify-end" title="{{ $p->user?->name }}">
                            @if($scope==='joined')
                                <i class="fa-solid fa-user"></i><span class="truncate">{{ $p->user?->name }}</span>
                            @endif
                        </div>
                        <!-- Actions -->
                        <div class="mt-2 md:mt-0 md:col-span-2 flex items-center gap-2 justify-end whitespace-nowrap justify-self-end">
                            <a href="{{ route('polls.show', $p->slug) }}" class="btn btn-neutral btn-sm">{{ __('messages.results') }}</a>
                            <div class="relative">
                                {{-- Nút mở menu hành động cho dòng poll --}}
                                <button class="icon-button action-menu-trigger" aria-haspopup="true" aria-expanded="false"><i class="fa-solid fa-ellipsis-vertical"></i></button>
                                <div class="menu dropdown action-menu">
                                    {{-- Toggle đóng/mở poll --}}
                                    <form method="POST" action="{{ route('polls.toggle', $p->slug) }}">@csrf
                                        <button type="submit" class="dropdown-item"><i class="fa-solid {{ $isClosed ? 'fa-play' : 'fa-stop' }} mr-2"></i>{{ $isClosed ? __('messages.reopen') : __('messages.close_poll') }}</button>
                                    </form>
                                    {{-- Export CSV kết quả poll --}}
                                    <a class="dropdown-item" href="{{ route('polls.export', $p->slug) }}"><i class="fa-solid fa-file-export mr-2"></i>{{ __('messages.export_to_csv') }}</a>
                                    {{-- Form ẩn để submit xoá khi xác nhận modal --}}
                                    <form id="deleteForm{{ $p->id }}" method="POST" action="{{ route('polls.destroy', $p->slug) }}" style="display:none;">@csrf @method('DELETE')</form>
                                    {{-- Mở modal xác nhận xoá poll đơn lẻ --}}
                                    <button type="button" class="dropdown-item text-error-600" onclick="openDeleteModal('{{ $p->slug }}', '{{ $p->id }}', '{{ addslashes($p->title ?? $p->question) }}')"><i class="fa-solid fa-trash mr-2"></i>{{ __('messages.delete') }}</button>
                                </div>
                            </div>
                        </div>
                        </div>
                    </div>
                @empty
                    <div class="p-6 text-center text-[color:var(--on-surface-variant)]">{{ __('messages.no_results_match') }}</div>
                @endforelse
            </div>

            <div>
                {{ $polls->appends(request()->except('page'))->links() }}
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
    document.addEventListener('DOMContentLoaded', function(){
        // Dropdown actions for row menus
        document.addEventListener('click', function(e){
            const trigger = e.target.closest('.action-menu-trigger');
            const anyMenu = document.querySelectorAll('.action-menu.show');
            if (!trigger) {
                // click outside -> close all
                anyMenu.forEach(m => m.classList.remove('show'));
                return;
            }
            e.stopPropagation();
            // close others first
            anyMenu.forEach(m => m.classList.remove('show'));
            const menu = trigger.parentElement.querySelector('.action-menu');
            if (menu) menu.classList.toggle('show');
        });

        const votesCtx = document.getElementById('chartVotesByDay');
        const typeCtx = document.getElementById('chartTypeDistribution');
        const chartLabels = @json($chartLabels ?? []);
        const chartMyVotes = @json($chartMyVotes ?? []);
        const chartJoinedVotes = @json($chartJoinedVotes ?? []);
        const typeDist = @json($chartTypeDistribution ?? ['standard'=>0,'ranking'=>0,'image'=>0]);
        if (votesCtx) {
            new Chart(votesCtx, {
                type: 'line',
                data: { labels: chartLabels, datasets: [{ label: 'My polls', data: chartMyVotes, borderColor: '#3B82F6' }, { label: 'Joined', data: chartJoinedVotes, borderColor: '#10B981' }] },
                options: { responsive: true, maintainAspectRatio: false }
            });
        }
        if (typeCtx) {
            new Chart(typeCtx, {
                type: 'doughnut',
                data: { labels: [@json(__('messages.poll_type_standard')),@json(__('messages.poll_type_ranking')),@json(__('messages.poll_type_image'))], datasets: [{ data: [typeDist.standard||0, typeDist.ranking||0, typeDist.image||0], backgroundColor: ['#3B82F6','#8B5CF6','#10B981'] }] },
                options: { responsive: true, maintainAspectRatio: false }
            });
        }
    });
    </script>

    <!-- Single Poll Delete Confirm Modal -->
    <div id="singleDeleteModal" class="hidden fixed inset-0 z-[100] flex items-center justify-center p-4 bg-black/50 backdrop-blur-sm" style="backdrop-filter: blur(4px);">
        <div class="card" style="max-width:520px;width:100%;">
            <div class="material-alert error">
                <div class="alert-content">
                    <i class="fa-solid fa-triangle-exclamation"></i>
                    <div class="alert-text">
                        <div class="alert-title">{{ __('messages.confirm_delete') }}</div>
                        <div class="alert-description">
                            <span id="singleDeleteMessage">{{ __('messages.delete_confirm') }}</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="flex justify-end gap-2">
                <button id="singleDeleteCancel" class="btn btn-neutral">{{ __('messages.cancel') }}</button>
                <button id="singleDeleteConfirm" class="btn btn-primary">{{ __('messages.delete') }}</button>
            </div>
        </div>
    </div>

    <script>
    // Single poll delete modal
    function openDeleteModal(slug, pollId, pollTitle) {
        const modal = document.getElementById('singleDeleteModal');
        const message = document.getElementById('singleDeleteMessage');
        const confirmBtn = document.getElementById('singleDeleteConfirm');
        
        // Update message with poll title if available
        if (pollTitle) {
            message.textContent = `{{ __('messages.delete_confirm') }} "${pollTitle}"?`;
        } else {
            message.textContent = '{{ __('messages.delete_confirm') }}';
        }
        
        // Remove previous event listeners by cloning
        const newConfirmBtn = confirmBtn.cloneNode(true);
        confirmBtn.parentNode.replaceChild(newConfirmBtn, confirmBtn);
        
        // Set up confirm button
        newConfirmBtn.addEventListener('click', function() {
            const form = document.getElementById('deleteForm' + pollId);
            if (form) {
                form.submit();
            }
        });
        
        modal.classList.remove('hidden');
        modal.classList.add('flex');
    }
    
    // Single delete modal handlers
    document.addEventListener('DOMContentLoaded', function(){
        const singleDeleteModal = document.getElementById('singleDeleteModal');
        const singleDeleteCancel = document.getElementById('singleDeleteCancel');
        
        singleDeleteCancel?.addEventListener('click', function() {
            singleDeleteModal.classList.add('hidden');
            singleDeleteModal.classList.remove('flex');
        });
        
        // Close modal when clicking outside
        singleDeleteModal?.addEventListener('click', function(e) {
            if (e.target === singleDeleteModal) {
                singleDeleteModal.classList.add('hidden');
                singleDeleteModal.classList.remove('flex');
            }
        });
    });
    </script>
</x-app-layout>


