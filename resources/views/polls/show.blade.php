<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-3xl text-gray-800 dark:text-gray-200 leading-tight">{{ $poll->question }}</h2>
            <a href="{{ route('polls.vote', $poll->slug) }}" class="btn btn-primary">
                <i class="fa-solid fa-vote-yea mr-2"></i>
                Back to Poll
            </a>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            @if ($isOwner)
            <div class="mb-4 flex items-center gap-2">
                <button id="tabResults" class="tab-button active px-4 py-2">Results</button>
                <button id="tabVoters" class="tab-button px-4 py-2">Voters</button>
            </div>
            @endif
            <!-- Success/Error Messages -->
    @if (session('success'))
                <div class="mb-6 bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-200 p-4 rounded-lg border border-green-200 dark:border-green-700 flex items-center gap-2">
                    <i class="fa-solid fa-check-circle"></i>
                    {{ session('success') }}
                </div>
    @endif
    @if (session('error'))
                <div class="mb-6 bg-red-100 dark:bg-red-900 text-red-800 dark:text-red-200 p-4 rounded-lg border border-red-200 dark:border-red-700 flex items-center gap-2">
                    <i class="fa-solid fa-exclamation-circle"></i>
                    {{ session('error') }}
                </div>
            @endif

            <!-- Poll Status Banner -->
            @if ($poll->is_closed)
                <div class="mb-6 p-4 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg">
                    <div class="flex items-center gap-2 text-red-800 dark:text-red-200">
                        <i class="fa-solid fa-lock"></i>
                        <span class="font-semibold">This poll is closed</span>
                    </div>
                </div>
    @endif

            <!-- Results Section -->
            <div id="panelResults" class="card mb-6">
                <div class="p-6">
                    <div id="results-skeleton" class="hidden">
                        <div class="skeleton h-6 w-40 mb-4 rounded"></div>
                        <div class="space-y-3">
                            <div class="skeleton h-10 rounded"></div>
                            <div class="skeleton h-10 rounded"></div>
                            <div class="skeleton h-48 rounded"></div>
                        </div>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-200 mb-4 flex items-center gap-2">
                        <i class="fa-solid fa-chart-bar text-indigo-600" aria-hidden="true"></i>
                        {{ __('messages.poll_results') }}
                    </h3>

        @if ($poll->poll_type === 'ranking')
                        @php
                            $rankings = [];
                            foreach ($poll->options as $option) {
                                $votes = $option->votes;
                                $totalScore = 0;
                                foreach ($votes as $vote) {
                                    $totalScore += $vote->rank;
                                }
                                $rankings[$option->id] = $totalScore;
                            }
                            $sortedOptions = $poll->options->sortByDesc(function ($option) use ($rankings) {
                                return $rankings[$option->id] ?? 0;
                            });
                        @endphp

                        <div class="space-y-4">
                            @php $barColors = ['#8B5CF6', '#06B6D4', '#10B981', '#F59E0B', '#EF4444']; @endphp
                            @foreach ($sortedOptions as $option)
                                @php
                                    $score = $rankings[$option->id] ?? 0;
                                    $maxScore = $poll->options->count() * $totalVotes;
                                    $percentage = $maxScore > 0 ? ($score / $maxScore) * 100 : 0;
                                @endphp
                                <div class="flex items-center gap-4 p-4 bg-gray-50 dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700">
                                    <div class="flex-1">
                                        <div class="flex items-center justify-between mb-2">
                                            <span class="font-medium text-gray-800 dark:text-gray-200">{{ $option->option_text }}</span>
                                            <span class="text-sm font-semibold text-indigo-600 dark:text-indigo-400">{{ $score }} points</span>
                                        </div>
                                        <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2">
                                            <div class="h-2 rounded-full transition-all duration-300" style="width: {{ $percentage }}%; background-color: {{ $barColors[$loop->index % count($barColors)] }}"></div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                            </div>

                        <!-- Ranking Chart -->
                        <div class="mt-6">
                            <canvas id="rankingChart" width="400" height="200"></canvas>
                        </div>
                    @else
                        @php
                            $counts = [];
                            foreach ($poll->options as $option) {
                                $counts[$option->id] = $option->votes->count();
                            }
                        @endphp

                        <div class="space-y-4">
                            @php $barColors = ['#8B5CF6', '#06B6D4', '#10B981', '#F59E0B', '#EF4444']; @endphp
                            @foreach ($poll->options as $option)
                                @php
                                    $count = $counts[$option->id];
                                    $percentage = $totalVotes > 0 ? ($count / $totalVotes) * 100 : 0;
                                @endphp
                                <div class="flex items-center gap-4 p-4 bg-gray-50 dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700">
                                    <div class="flex-1">
                                        <div class="flex items-center justify-between mb-2">
                                            <span class="font-medium text-gray-800 dark:text-gray-200">{{ $option->option_text }}</span>
                                            <span class="text-sm font-semibold text-indigo-600 dark:text-indigo-400">{{ $count }} votes ({{ number_format($percentage, 1) }}%)</span>
                                        </div>
                                        <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2">
                                            <div class="h-2 rounded-full transition-all duration-300" style="width: {{ $percentage }}%; background-color: {{ $barColors[$loop->index % count($barColors)] }}"></div>
                                        </div>
                                    </div>
                        </div>
                    @endforeach
                </div>

                        <!-- Regular Chart -->
                        <div class="mt-6">
                            <canvas id="regularChart" width="400" height="200"></canvas>
            </div>
        @endif

                    {{-- Inline Share Section inside Results card for unified UI --}}
                    @if (!$poll->hide_share || $isOwner)
                    <div class="mt-8 pt-6 border-t border-gray-200 dark:border-gray-700">
                        <div class="flex items-center gap-2 mb-4">
                            <i class="fa-solid fa-share-nodes text-green-600"></i>
                            <h4 class="text-lg font-semibold text-gray-800 dark:text-gray-200">{{ __('messages.share_poll') }}</h4>
                        </div>

                        <!-- Share Options Tabs -->
                        <div class="flex flex-wrap gap-2 mb-4">
                            <button type="button" id="showCode" class="share-tab active px-4 py-2 bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-300 rounded-lg text-sm font-medium transition-colors">
                                <i class="fa-solid fa-code mr-2"></i>Code
                            </button>
                            <button type="button" id="showUrl" class="share-tab px-4 py-2 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-lg text-sm font-medium transition-colors">
                                <i class="fa-solid fa-link mr-2"></i>URL
                            </button>
                            <button type="button" id="showQR" class="share-tab px-4 py-2 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-lg text-sm font-medium transition-colors">
                                <i class="fa-solid fa-qrcode mr-2"></i>QR Code
                            </button>
        </div>

                        <!-- Share Content -->
                        <div id="shareCode" class="share-content">
                            <div class="space-y-2">
                                <label class="text-sm font-medium text-gray-600 dark:text-gray-400">{{ __('messages.poll_code') }}</label>
                                <div class="flex gap-2">
                                    <input id="code" class="flex-1 px-3 py-2 bg-gray-50 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg text-sm font-mono" value="{{ $poll->slug }}" readonly>
                                    <button type="button" data-target="code" class="btn-copy copy px-4 py-2 tooltip" data-tooltip="{{ __('messages.copy') }}" aria-label="{{ __('messages.copy') }}">
                                        <i class="fa-solid fa-copy mr-1" aria-hidden="true"></i>{{ __('messages.copy') }}
                                    </button>
                                </div>
                                <p class="text-xs text-gray-500 dark:text-gray-400">Share this code with others to let them find your poll</p>
                            </div>
                        </div>

                        <div id="shareUrl" class="share-content hidden">
                            <div class="space-y-2">
                                <label class="text-sm font-medium text-gray-600 dark:text-gray-400">{{ __('messages.share_link') }}</label>
                                <div class="flex gap-2">
                                    <input id="share" class="flex-1 px-3 py-2 bg-gray-50 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg text-sm" value="{{ route('polls.vote', $poll->slug) }}" readonly>
                                    <button type="button" data-target="share" class="btn-copy copy px-4 py-2 tooltip" data-tooltip="{{ __('messages.copy') }}" aria-label="{{ __('messages.copy') }}">
                                        <i class="fa-solid fa-copy mr-1" aria-hidden="true"></i>{{ __('messages.copy') }}
                                    </button>
                                </div>
                                <p class="text-xs text-gray-500 dark:text-gray-400">Direct link to your poll</p>
                            </div>
                        </div>

                        <div id="shareQR" class="share-content hidden">
                            <div class="space-y-2">
                                <label class="text-sm font-medium text-gray-600 dark:text-gray-400">{{ __('messages.qr_code') }}</label>
                                <div class="flex justify-center p-4 bg-white dark:bg-gray-700 rounded-lg border border-gray-200 dark:border-gray-600">
                                    <div id="qrcode"></div>
                                </div>
                                <p class="text-xs text-gray-500 dark:text-gray-400 text-center">Scan QR code to access the poll</p>
                            </div>
                        </div>
                            </div>
            @endif
        </div>
    </div>

            <!-- Voters Section (Owner only) -->
    @if ($isOwner)
            <div id="panelVoters" class="card mb-6 hidden">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-200 mb-4 flex items-center gap-2">
                        <i class="fa-solid fa-users text-green-600" aria-hidden="true"></i>
                        Voter Statistics
                    </h3>

                    <!-- Sticky Filters -->
                    <div class="sticky top-20 z-10 mb-3 p-3 bg-white/80 dark:bg-gray-800/80 backdrop-blur rounded border border-gray-200 dark:border-gray-700">
                        <form method="GET" class="flex flex-wrap gap-3 items-center">
                            <input type="text" name="q" value="{{ request('q') }}" placeholder="{{ __('messages.search_placeholder') }}" class="flex-1 min-w-56 px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent dark:bg-gray-700 dark:text-white">
                            <input type="date" name="from" value="{{ request('from') }}" class="px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent dark:bg-gray-700 dark:text-white">
                            <input type="date" name="to" value="{{ request('to') }}" class="px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent dark:bg-gray-700 dark:text-white">
                            <button type="submit" class="btn btn-primary" aria-label="Search"><i class="fa-solid fa-search mr-1" aria-hidden="true"></i>{{ __('messages.search') ?? 'Search' }}</button>
                            <a href="{{ route('polls.show', $poll->slug) }}" class="btn btn-neutral" aria-label="Clear filters"><i class="fa-solid fa-times mr-1" aria-hidden="true"></i>{{ __('messages.clear') }}</a>
                        </form>
                    </div>

                    <!-- Voters Table -->
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead>
                                <tr class="text-left text-gray-600 dark:text-gray-300">
                                    <th class="py-2 pr-4">{{ __('messages.name') }}</th>
                                    <th class="py-2 pr-4">{{ __('messages.choice') }}</th>
                                    <th class="py-2 pr-4">{{ __('messages.time') }}</th>
                    </tr>
                </thead>
                <tbody>
                                @forelse ($voters as $vote)
                        <tr class="border-t border-gray-200 dark:border-gray-700">
                                    <td class="py-2 pr-4 text-gray-800 dark:text-gray-200">{{ $vote->voter_name ?: 'Anonymous' }}</td>
                                    <td class="py-2 pr-4 text-gray-700 dark:text-gray-300">
                                        @if ($poll->poll_type === 'ranking')
                                            Rank {{ $vote->rank }}: {{ $vote->option->option_text }}
                                        @else
                                            {{ $vote->option->option_text }}
                                        @endif
                                    </td>
                                    <td class="py-2 pr-4 text-gray-500">{{ $vote->created_at->format('d/m/Y H:i') }}</td>
                        </tr>
                                @empty
                                <tr><td colspan="3" class="py-6 text-center text-gray-500 dark:text-gray-400">{{ __('messages.no_votes_yet') }}</td></tr>
                                @endforelse
                </tbody>
            </table>
                    </div>

                    @if ($votersPaginator && $votersPaginator->hasPages())
                        <div class="mt-4">
                            {{ $votersPaginator->links() }}
                        </div>
                    @endif
                </div>
            </div>
            @endif

            <!-- Poll Information (for owner only) -->
            @if ($isOwner)
            <div class="card">
                <div class="p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-200 flex items-center gap-2">
                            <i class="fa-solid fa-info-circle text-blue-600"></i>
                            Poll Information
                        </h3>
                        <div class="flex items-center gap-2">
                            <span class="px-2 py-1 text-xs font-medium rounded-full {{ $poll->is_closed ? 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200' : 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200' }}">
                                {{ $poll->is_closed ? 'Closed' : 'Open' }}
                            </span>
                            <span class="px-2 py-1 text-xs font-medium rounded-full bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-200">
                                {{ $poll->poll_type === 'ranking' ? 'Ranking' : 'Regular' }}
                            </span>
        </div>
    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 text-sm">
                        <div class="flex flex-col">
                            <span class="font-medium text-gray-600 dark:text-gray-400 mb-1">Poll Type</span>
                            <span class="text-gray-800 dark:text-gray-200">
                                {{ $poll->poll_type === 'ranking' ? 'Ranking Poll' : 'Regular Poll' }}
                            </span>
                        </div>
                        <div class="flex flex-col">
                            <span class="font-medium text-gray-600 dark:text-gray-400 mb-1">Allow Multiple</span>
                            <span class="text-gray-800 dark:text-gray-200">
                                {{ $poll->allow_multiple ? 'Yes' : 'No' }}
                            </span>
                        </div>
                        <div class="flex flex-col">
                            <span class="font-medium text-gray-600 dark:text-gray-400 mb-1">Voting Security</span>
                            <span class="text-gray-800 dark:text-gray-200">
                                {{ $poll->voting_security === 'private' ? 'Private' : 'Session-based' }}
                            </span>
                        </div>
                        <div class="flex flex-col">
                            <span class="font-medium text-gray-600 dark:text-gray-400 mb-1">Total Votes</span>
                            <span class="text-gray-800 dark:text-gray-200">{{ $totalVotes }}</span>
                        </div>
                        <div class="flex flex-col">
                            <span class="font-medium text-gray-600 dark:text-gray-400 mb-1">Options</span>
                            <span class="text-gray-800 dark:text-gray-200">{{ $poll->options->count() }}</span>
                        </div>
                        <div class="flex flex-col">
                            <span class="font-medium text-gray-600 dark:text-gray-400 mb-1">Created</span>
                            <span class="text-gray-800 dark:text-gray-200">{{ $poll->created_at->format('d/m/Y H:i') }}</span>
                        </div>
                        @if ($poll->auto_close_at)
                        <div class="flex flex-col">
                            <span class="font-medium text-gray-600 dark:text-gray-400 mb-1">Auto Close</span>
                            <span class="text-gray-800 dark:text-gray-200">
                                {{ \Carbon\Carbon::parse($poll->auto_close_at)->format('d/m/Y H:i') }}
                            </span>
                        </div>
                        @endif
                    </div>

                    @if ($poll->is_private)
                    <div class="mt-4 pt-4 border-t border-gray-200 dark:border-gray-700">
                        <div class="flex flex-col">
                            <span class="font-medium text-gray-600 dark:text-gray-400 mb-2">Access Key</span>
                            <div class="flex items-center gap-2">
                                @if ($poll->access_key)
                                    <code class="flex-1 px-3 py-2 bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-200 rounded text-sm font-mono border border-gray-300 dark:border-gray-600">
                                        {{ $poll->access_key }}
                                    </code>
                                    <button type="button" onclick="copyToClipboard('{{ $poll->access_key }}', this)" 
                                            class="btn-copy px-3 py-2 bg-blue-600 text-white rounded text-sm hover:bg-blue-700 transition-colors flex items-center gap-1">
                                        <i class="fa-solid fa-copy"></i> Copy
                                    </button>
                                @else
                                    <span class="px-3 py-2 bg-yellow-100 dark:bg-yellow-900 text-yellow-800 dark:text-yellow-200 rounded text-sm border border-yellow-300 dark:border-yellow-700 flex items-center gap-2">
                                        <i class="fa-solid fa-key"></i> No access key required
                                    </span>
                                @endif
                            </div>
                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-2">
                                Share this key with people you want to give access to this poll
                            </p>
                        </div>
                    </div>
                    @endif

                    <!-- End owner card -->
        </div>
    </div>
    @endif

            {{-- Removed duplicate Share card at bottom (now inline in results card) --}}
        </div>
    </div>

    <!-- Chart.js CDN -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <script>
        // Copy to clipboard function
        function copyToClipboard(text, buttonElement) {
            navigator.clipboard.writeText(text).then(function() {
                const originalText = buttonElement.innerHTML;
                buttonElement.innerHTML = '<i class="fa-solid fa-check mr-1"></i>Copied!';
                buttonElement.classList.add('bg-green-600', 'hover:bg-green-700');
                buttonElement.classList.remove('bg-blue-600', 'hover:bg-blue-700');
                
                setTimeout(function() {
                    buttonElement.innerHTML = originalText;
                    buttonElement.classList.remove('bg-green-600', 'hover:bg-green-700');
                    buttonElement.classList.add('bg-blue-600', 'hover:bg-blue-700');
                }, 2000);
            }).catch(function(err) {
                console.error('Could not copy text: ', err);
                alert('Failed to copy to clipboard');
            });
        }

        // Share tab functionality
        document.addEventListener('DOMContentLoaded', function() {
            // Results/Voters tabs (owner)
            const tabResults = document.getElementById('tabResults');
            const tabVoters = document.getElementById('tabVoters');
            const panelResults = document.getElementById('panelResults');
            const panelVoters = document.getElementById('panelVoters');
            if (tabResults && tabVoters && panelResults && panelVoters) {
                tabResults.addEventListener('click', function() {
                    tabResults.classList.add('active');
                    tabVoters.classList.remove('active');
                    panelResults.classList.remove('hidden');
                    panelVoters.classList.add('hidden');
                });
                tabVoters.addEventListener('click', function() {
                    tabVoters.classList.add('active');
                    tabResults.classList.remove('active');
                    panelVoters.classList.remove('hidden');
                    panelResults.classList.add('hidden');
                });
            }
            const shareTabs = document.querySelectorAll('.share-tab');
            const shareContents = document.querySelectorAll('.share-content');
            const copyButtons = document.querySelectorAll('.copy');

            shareTabs.forEach(tab => {
                tab.addEventListener('click', function() {
                    const targetId = this.id.replace('show', 'share');
                    
                    // Remove active class from all tabs
                    shareTabs.forEach(t => t.classList.remove('active', 'bg-blue-100', 'dark:bg-blue-900/30', 'text-blue-700', 'dark:text-blue-300'));
                    shareTabs.forEach(t => t.classList.add('bg-gray-100', 'dark:bg-gray-700', 'text-gray-700', 'dark:text-gray-300'));
                    
                    // Hide all content
                    shareContents.forEach(content => content.classList.add('hidden'));
                    
                    // Show target content and activate tab
                    document.getElementById(targetId).classList.remove('hidden');
                    this.classList.remove('bg-gray-100', 'dark:bg-gray-700', 'text-gray-700', 'dark:text-gray-300');
                    this.classList.add('active', 'bg-blue-100', 'dark:bg-blue-900/30', 'text-blue-700', 'dark:text-blue-300');
                });
            });

            // Copy button functionality + add ARIA and live region
            copyButtons.forEach(btn => btn.setAttribute('aria-label','Copy'));
            let live = document.getElementById('sr-live');
            if (!live) { live = document.createElement('div'); live.id='sr-live'; live.className='sr-only'; live.setAttribute('aria-live','polite'); document.body.appendChild(live); }
            copyButtons.forEach(button => {
                button.addEventListener('click', function() {
                    const targetId = this.getAttribute('data-target');
                    const input = document.getElementById(targetId);
                    copyToClipboard(input.value, this);
                    live.textContent = 'Copied to clipboard';
                    let toast = document.getElementById('global-toast');
                    if (!toast) {
                        toast = document.createElement('div');
                        toast.id = 'global-toast';
                        toast.className = 'toast';
                        document.body.appendChild(toast);
                    }
                    toast.textContent = 'Copied!';
                    toast.classList.add('show');
                    setTimeout(()=> toast.classList.remove('show'), 1200);
                });
            });

            // QR Code generation (lazy load when opening tab)
            const qrButton = document.getElementById('showQR');
            if (qrButton) {
                qrButton.addEventListener('click', function() {
                    const qrContainer = document.getElementById('qrcode');
                    if (qrContainer && !qrContainer.hasChildNodes()) {
                        const qrUrl = 'https://quickchart.io/qr?text=' + encodeURIComponent(window.location.href);
                        const img = document.createElement('img');
                        img.src = qrUrl;
                        img.alt = 'QR Code';
                        img.className = 'w-32 h-32';
                        qrContainer.appendChild(img);
                    }
                });
            }

            // Removed URL shortening feature
        });

        // Initialize charts
        function initializeCharts() {
            @if ($poll->poll_type === 'ranking')
                // Ranking Chart
                const rankingCtx = document.getElementById('rankingChart');
                if (rankingCtx) {
                    @php
                        $chartData = [];
                        $chartLabels = [];
                        $chartColors = ['#8B5CF6', '#06B6D4', '#10B981', '#F59E0B', '#EF4444'];
                        foreach ($sortedOptions as $index => $opt) {
                            $score = $rankings[$opt->id] ?? 0;
                            $chartData[] = $score;
                            $chartLabels[] = $opt->option_text;
                        }
                    @endphp
                    
                    new Chart(rankingCtx, {
                        type: 'doughnut',
                        data: {
                            labels: {!! json_encode($chartLabels) !!},
                            datasets: [{
                                data: {!! json_encode($chartData) !!},
                                backgroundColor: {!! json_encode($chartColors) !!},
                                borderWidth: 2,
                                borderColor: '#ffffff'
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                legend: {
                                    position: 'bottom',
                                    labels: {
                                        padding: 20,
                                        usePointStyle: true
                                    }
                                },
                                tooltip: {
                                    callbacks: {
                                        label: function(context) {
                                            return context.label + ': ' + context.parsed + ' points';
                                        }
                                    }
                                }
                            }
                        }
                    });
                }
            @else
                // Regular Chart
                const regularCtx = document.getElementById('regularChart');
                if (regularCtx) {
                    @php
                        $chartData = [];
                        $chartLabels = [];
                        $chartColors = ['#8B5CF6', '#06B6D4', '#10B981', '#F59E0B', '#EF4444'];
                        foreach ($poll->options as $index => $opt) {
                            $c = $counts[$opt->id];
                            $chartData[] = $c;
                            $chartLabels[] = $opt->option_text;
                        }
                    @endphp
                    
                    new Chart(regularCtx, {
                        type: 'doughnut',
                        data: {
                            labels: {!! json_encode($chartLabels) !!},
                            datasets: [{
                                data: {!! json_encode($chartData) !!},
                                backgroundColor: {!! json_encode($chartColors) !!},
                                borderWidth: 2,
                                borderColor: '#ffffff'
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                legend: {
                                    position: 'bottom',
                                    labels: {
                                        padding: 20,
                                        usePointStyle: true
                                    }
                                },
                                tooltip: {
                                    callbacks: {
                                        label: function(context) {
                                            const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                            const percentage = ((context.parsed / total) * 100).toFixed(1);
                                            return context.label + ': ' + context.parsed + ' votes (' + percentage + '%)';
                                        }
                                    }
                                }
                            }
                        }
                    });
        }
        @endif
        }

        // Initialize charts when page loads
        document.addEventListener('DOMContentLoaded', initializeCharts);
    </script>
</x-app-layout>