<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div class="flex-1">
                <h1 class="text-headline-large text-on-surface font-semibold">{{ $poll->title ?? $poll->question }}</h1>
                @if($poll->description)
                    <p class="text-body-medium text-on-surface-variant mt-1">{{ $poll->description }}</p>
                @else
                    <p class="text-body-medium text-on-surface-variant mt-1">Poll Results & Analytics</p>
                @endif
            </div>
            <div class="flex items-center gap-3">
                <a href="{{ route('polls.vote', $poll->slug) }}" class="btn btn-primary">
                    <i class="fa-solid fa-vote-yea"></i>
                    Back to Poll
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <!-- Poll Media Section -->
            @if($poll->hasDescriptionMedia())
                <div class="mb-8">
                    <div class="card card-elevated">
                        <div class="card-header">
                            <div class="card-title">
                                <i class="fa-solid fa-images text-primary"></i>
                                Poll Media
                            </div>
                        </div>
                        <div class="card-content">
                            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                                @foreach($poll->getDescriptionMedia() as $mediaItem)
                                    @php
                                        $media = is_string($mediaItem) ? json_decode($mediaItem, true) : $mediaItem;
                                    @endphp
                                    @if($media && isset($media['type']))
                                        <div class="media-display-item bg-gray-50 dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 overflow-hidden hover:shadow-lg transition-shadow duration-300">
                                            @if($media['type'] === 'video')
                                                <div class="aspect-video bg-gray-100 dark:bg-gray-700">
                                                    <video controls class="w-full h-full object-cover">
                                                        <source src="{{ $media['url'] }}" type="video/{{ $media['extension'] ?? 'mp4' }}">
                                                        Your browser does not support the video tag.
                                                    </video>
                                                </div>
                                            @else
                                                <div class="aspect-square bg-gray-100 dark:bg-gray-700 cursor-pointer" onclick="openImageModal('{{ $media['url'] }}', '{{ $media['filename'] ?? 'Media' }}')">
                                                    <img src="{{ $media['url'] }}" alt="{{ $media['filename'] ?? 'Media' }}" class="w-full h-full object-cover hover:scale-105 transition-transform duration-300">
                                                </div>
                                            @endif
                                            @if(isset($media['filename']))
                                                <div class="p-3">
                                                    <p class="text-sm text-gray-600 dark:text-gray-400 truncate">{{ $media['filename'] }}</p>
                                                </div>
                                            @endif
                                        </div>
                                    @endif
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            @endif
            @if ($isOwner)
            <div class="mb-6">
                <div class="material-tabs">
                    <button id="tabResults" class="material-tab active">
                        <i class="fa-solid fa-chart-pie"></i>
                        Results
                    </button>
                    <button id="tabVoters" class="material-tab">
                        <i class="fa-solid fa-users"></i>
                        Voters
                    </button>
                </div>
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
                <div class="mb-8 material-alert error">
                    <div class="alert-content">
                        <i class="fa-solid fa-lock"></i>
                        <div class="alert-text">
                            <span class="alert-title">Poll Closed</span>
                            <span class="alert-description">This poll is no longer accepting votes</span>
                        </div>
                    </div>
                </div>
    @endif

            <!-- Results Section -->
            <div id="panelResults" class="card card-elevated animate-fade-in-up mb-8">
                <div class="card-header">
                    <div class="card-title">
                        <i class="fa-solid fa-chart-bar text-primary"></i>
                        {{ __('messages.poll_results') }}
                    </div>
                    <div class="card-subtitle">
                        Total Votes: <span class="font-semibold text-primary">{{ $totalVotes }}</span>
                    </div>
                </div>
                <div class="card-content">
                    <div id="results-skeleton" class="hidden">
                        <div class="skeleton h-6 w-40 mb-4 rounded"></div>
                        <div class="space-y-3">
                            <div class="skeleton h-10 rounded"></div>
                            <div class="skeleton h-10 rounded"></div>
                            <div class="skeleton h-48 rounded"></div>
                        </div>
                    </div>

        @if ($poll->poll_type === 'ranking')
                        @php
                            $rankings = [];
                            $colorMap = [];
                            $barColors = ['#3B82F6', '#8B5CF6', '#06B6D4', '#10B981', '#F59E0B'];
                            $colorIndex = 0;
                            
                            foreach ($poll->options as $option) {
                                $votes = $option->votes;
                                $totalScore = 0;
                                foreach ($votes as $vote) {
                                    $totalScore += $vote->rank;
                                }
                                $rankings[$option->id] = $totalScore;
                                if ($totalScore > 0) {
                                    $colorMap[$option->id] = $barColors[$colorIndex % count($barColors)];
                                    $colorIndex++;
                                }
                            }
                            $sortedOptions = $poll->options->sortByDesc(function ($option) use ($rankings) {
                                return $rankings[$option->id] ?? 0;
                            });
                        @endphp

                        <div class="space-y-4">
                            @foreach ($sortedOptions as $option)
                                @php
                                    $score = $rankings[$option->id] ?? 0;
                                    $maxScore = $poll->options->count() * $totalVotes;
                                    $percentage = $maxScore > 0 ? ($score / $maxScore) * 100 : 0;
                                @endphp
                                @if($score > 0)
                                <div class="material-progress-item animate-fade-in-up" style="animation-delay: {{ $loop->index * 0.1 }}s;">
                                    <div class="progress-header">
                                        <span class="progress-label">{{ $option->option_text }}</span>
                                        <span class="progress-value">{{ $score }} points</span>
                                    </div>
                                    <div class="material-progress-bar">
                                        <div class="progress-fill" style="width: {{ $percentage }}%; background-color: {{ $colorMap[$option->id] }}"></div>
                                    </div>
                                </div>
                                @endif
                            @endforeach
                            </div>

                        <!-- Ranking Chart -->
                        <div class="chart-container">
                            <div class="chart-header">
                                <h4 class="chart-title">Ranking Distribution</h4>
                                <p class="chart-subtitle">Visual representation of option rankings</p>
                            </div>
                            <div class="chart-wrapper">
                                <canvas id="rankingChart" width="400" height="200"></canvas>
                            </div>
                        </div>
                    @elseif ($poll->poll_type === 'image')
                        @php
                            $counts = [];
                            $colorMap = [];
                            $barColors = ['#3B82F6', '#8B5CF6', '#06B6D4', '#10B981', '#F59E0B'];
                            $colorIndex = 0;
                            
                            foreach ($poll->options as $option) {
                                $counts[$option->id] = $option->votes->count();
                                if ($option->votes->count() > 0) {
                                    $colorMap[$option->id] = $barColors[$colorIndex % count($barColors)];
                                    $colorIndex++;
                                }
                            }
                        @endphp

                        <!-- Image Poll Results Grid -->
                        <div class="results-grid grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
                            @foreach ($poll->options as $option)
                                @php
                                    $count = $counts[$option->id];
                                    $percentage = $totalVotes > 0 ? ($count / $totalVotes) * 100 : 0;
                                @endphp
                                <div class="image-result-card bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 overflow-hidden animate-fade-in-up w-full sm:max-w-xs mx-auto flex flex-col" style="animation-delay: {{ $loop->index * 0.1 }}s;">
                                    <!-- Image -->
                                    <div class="aspect-square overflow-hidden w-full" style="aspect-ratio: 1 / 1;">
                                        @if($option->hasImage())
                                            <img src="{{ $option->image_url }}" 
                                                 alt="{{ $option->getImageAltText() }}" 
                                                 class="w-full h-full object-cover">
                                        @else
                                            <div class="w-full h-full bg-gray-100 dark:bg-gray-700 flex items-center justify-center">
                                                <i class="fa-solid fa-image text-4xl text-gray-400"></i>
                                            </div>
                                        @endif
                                    </div>
                                    
                                    <!-- Results Info with fixed layout -->
                                    <div class="p-4 flex flex-col flex-grow">
                                        <!-- Fixed height title container -->
                                        <div class="h-12 flex items-center justify-center mb-2">
                                            <h4 class="font-medium text-gray-800 dark:text-gray-200 text-center leading-tight">
                                                {{ $option->getDisplayText() }}
                                            </h4>
                                        </div>
                                        
                                        <!-- Vote Count -->
                                        <div class="text-center mb-3">
                                            <span class="text-2xl font-bold text-primary">{{ $count }}</span>
                                            <span class="text-sm text-gray-600 dark:text-gray-400 ml-1">votes</span>
                                        </div>
                                        
                                        <!-- Progress Bar -->
                                        <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2 mb-2">
                                            <div class="h-2 rounded-full transition-all duration-500" 
                                                 style="width: {{ $percentage }}%; background-color: {{ $colorMap[$option->id] ?? '#3B82F6' }}"></div>
                                        </div>
                                        
                                        <!-- Percentage -->
                                        <div class="text-center">
                                            <span class="text-sm font-medium text-gray-600 dark:text-gray-400">
                                                {{ number_format($percentage, 1) }}%
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        
                        <!-- Chart for Image Poll -->
                        @if($totalVotes > 0)
                        <div class="mt-8">
                            <div class="chart-header">
                                <h3 class="chart-title">Voting Distribution</h3>
                                <p class="chart-subtitle">Visual representation of image poll results</p>
                            </div>
                            <div class="chart-wrapper">
                                <canvas id="imageChart" width="400" height="200"></canvas>
                            </div>
                        </div>
                        @endif
                    @else
                        @php
                            $counts = [];
                            $colorMap = [];
                            $barColors = ['#3B82F6', '#8B5CF6', '#06B6D4', '#10B981', '#F59E0B'];
                            $colorIndex = 0;
                            
                            foreach ($poll->options as $option) {
                                $counts[$option->id] = $option->votes->count();
                                if ($option->votes->count() > 0) {
                                    $colorMap[$option->id] = $barColors[$colorIndex % count($barColors)];
                                    $colorIndex++;
                                }
                            }
                        @endphp

                        <div class="space-y-4">
                            @foreach ($poll->options as $option)
                                @php
                                    $count = $counts[$option->id];
                                    $percentage = $totalVotes > 0 ? ($count / $totalVotes) * 100 : 0;
                                @endphp
                                @if($count > 0)
                                <div class="material-progress-item animate-fade-in-up" style="animation-delay: {{ $loop->index * 0.1 }}s;">
                                    <div class="progress-header">
                                        <span class="progress-label">{{ $option->option_text }}</span>
                                        <span class="progress-value">{{ $count }} votes ({{ number_format($percentage, 1) }}%)</span>
                                    </div>
                                    <div class="material-progress-bar">
                                        <div class="progress-fill" style="width: {{ $percentage }}%; background-color: {{ $colorMap[$option->id] }}"></div>
                                    </div>
                                </div>
                                @endif
                            @endforeach
                </div>

                        <!-- Regular Chart -->
                        <div class="chart-container">
                            @if($totalVotes > 0)
                                <div class="chart-header">
                                    <h4 class="chart-title">Vote Distribution</h4>
                                    <p class="chart-subtitle">Visual representation of voting results</p>
                                </div>
                                <div class="chart-wrapper">
                                    <canvas id="regularChart" width="400" height="200"></canvas>
                                </div>
                            @else
                                <div class="empty-state">
                                    <i class="fa-solid fa-chart-pie"></i>
                                    <h4 class="empty-title">No Votes Yet</h4>
                                    <p class="empty-description">{{ __('messages.no_votes_yet') }}</p>
                                </div>
                            @endif
                        </div>
        @endif

                    {{-- Inline Share Section inside Results card for unified UI --}}
                    @if (!$poll->hide_share || $isOwner)
                    <div class="share-section">
                        <div class="share-header">
                            <div class="share-title">
                                <i class="fa-solid fa-share-nodes text-primary"></i>
                                {{ __('messages.share_poll') }}
                            </div>
                            <p class="share-subtitle">Share your poll with others to get more responses</p>
                        </div>

                        <!-- Share Options Tabs -->
                        <div class="share-tabs">
                            <button type="button" id="showCode" class="share-tab active">
                                <i class="fa-solid fa-code"></i>
                                Code
                            </button>
                            <button type="button" id="showUrl" class="share-tab">
                                <i class="fa-solid fa-link"></i>
                                URL
                            </button>
                            <button type="button" id="showQR" class="share-tab">
                                <i class="fa-solid fa-qrcode"></i>
                                QR Code
                            </button>
                        </div>

                        <!-- Share Content -->
                        <div id="shareCode" class="share-content">
                            <div class="input-field">
                                <input id="code" value="{{ $poll->slug }}" readonly>
                                <label for="code">{{ __('messages.poll_code') }}</label>
                            </div>
                            <button type="button" data-target="code" class="btn btn-primary copy">
                                <i class="fa-solid fa-copy"></i>
                                {{ __('messages.copy') }}
                            </button>
                            <p class="share-description">Share this code with others to let them find your poll</p>
                        </div>

                        <div id="shareUrl" class="share-content hidden">
                            <div class="input-field">
                                <input id="share" value="{{ route('polls.vote', $poll->slug) }}" readonly>
                                <label for="share">{{ __('messages.share_link') }}</label>
                            </div>
                            <button type="button" data-target="share" class="btn btn-primary copy">
                                <i class="fa-solid fa-copy"></i>
                                {{ __('messages.copy') }}
                            </button>
                            <p class="share-description">Direct link to your poll</p>
                        </div>

                        <div id="shareQR" class="share-content hidden">
                            <div class="qr-container">
                                <div class="qr-header">
                                    <h5 class="qr-title">{{ __('messages.qr_code') }}</h5>
                                    <p class="qr-subtitle">Scan to access poll</p>
                                </div>
                                <div class="qr-wrapper">
                                    <div id="qrcode"></div>
                                </div>
                                <p class="share-description">Scan QR code to access the poll</p>
                            </div>
                        </div>
                            </div>
            @endif
        </div>
    </div>

            <!-- Voters Section (Owner only) -->
    @if ($isOwner)
            <div id="panelVoters" class="card card-elevated animate-fade-in-up mb-8 hidden">
                <div class="card-header">
                    <div class="card-title">
                        <i class="fa-solid fa-users text-primary"></i>
                        Voter Statistics
                    </div>
                    <div class="card-subtitle">
                        View and filter participant data
                    </div>
                </div>
                <div class="card-content">

                    <!-- Filters -->
                    <div class="filters-container">
                        <form method="GET" class="filters-form">
                            <div class="filter-group">
                                <div class="input-field">
                                    <input type="text" name="q" value="{{ request('q') }}" placeholder=" ">
                                    <label for="q">{{ __('messages.search_placeholder') }}</label>
                                </div>
                                <div class="input-field">
                                    <input type="date" name="from" value="{{ request('from') }}" placeholder=" ">
                                    <label for="from">From Date</label>
                                </div>
                                <div class="input-field">
                                    <input type="date" name="to" value="{{ request('to') }}" placeholder=" ">
                                    <label for="to">To Date</label>
                                </div>
                            </div>
                            <div class="filter-actions">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fa-solid fa-search"></i>
                                    SEARCH
                                </button>
                                <a href="{{ route('polls.show', $poll->slug) }}" class="btn btn-neutral">
                                    <i class="fa-solid fa-times"></i>
                                    CLEAR
                                </a>
                            </div>
                        </form>
                    </div>

                    <!-- Voters Table -->
                    <div class="table-container">
                        <table class="material-table">
                            <thead>
                                <tr>
                                    <th>{{ __('messages.name') }}</th>
                                    <th>{{ __('messages.choice') }}</th>
                                    <th>{{ __('messages.time') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($voters as $vote)
                                <tr class="table-row">
                                    <td class="voter-name">{{ $vote->voter_name ?: 'Anonymous' }}</td>
                                    <td class="voter-choice">
                                        @if ($poll->poll_type === 'ranking')
                                            <span class="choice-rank">Rank {{ $vote->rank }}:</span> {{ $vote->option->option_text }}
                                        @else
                                            {{ $vote->option->option_text }}
                                        @endif
                                    </td>
                                    <td class="voter-time">{{ $vote->created_at->format('d/m/Y H:i') }}</td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="3" class="empty-table">
                                        <div class="empty-state">
                                            <i class="fa-solid fa-users"></i>
                                            <h4 class="empty-title">No Voters Found</h4>
                                            <p class="empty-description">{{ __('messages.no_votes_yet') }}</p>
                                        </div>
                                    </td>
                                </tr>
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
            <div class="card mb-8">
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
                                @if($poll->poll_type === 'ranking')
                                    Ranking
                                @elseif($poll->poll_type === 'image')
                                    Image
                                @else
                                    Regular
                                @endif
                            </span>
        </div>
    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 text-sm">
                        <div class="flex flex-col">
                            <span class="font-medium text-gray-600 dark:text-gray-400 mb-1">Poll Type</span>
                            <span class="text-gray-800 dark:text-gray-200">
                                @if($poll->poll_type === 'ranking')
                                    Ranking Poll
                                @elseif($poll->poll_type === 'image')
                                    Image Poll
                                @else
                                    Regular Poll
                                @endif
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
            // Results/Voters tabs (owner) - Material Design
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

            // Initialize: Hide all content except the first one
            shareContents.forEach((content, index) => {
                if (index !== 0) {
                    content.classList.add('hidden');
                }
            });

            shareTabs.forEach(tab => {
                tab.addEventListener('click', function() {
                    const targetId = this.id.replace('show', 'share');
                    
                    // Remove active class from all tabs
                    shareTabs.forEach(t => t.classList.remove('active'));
                    
                    // Hide all content
                    shareContents.forEach(content => {
                        content.classList.add('hidden');
                    });
                    
                    // Show target content and activate tab
                    const targetContent = document.getElementById(targetId);
                    if (targetContent) {
                        targetContent.classList.remove('hidden');
                    }
                    this.classList.add('active');
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
                        $chartColors = [];
                        foreach ($sortedOptions as $index => $opt) {
                            $score = $rankings[$opt->id] ?? 0;
                            if ($score > 0) {
                                $chartData[] = $score;
                                $chartLabels[] = $opt->option_text;
                                $chartColors[] = $colorMap[$opt->id];
                            }
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
            @elseif ($poll->poll_type === 'image')
                // Image Chart
                const imageCtx = document.getElementById('imageChart');
                if (imageCtx) {
                    @php
                        $chartData = [];
                        $chartLabels = [];
                        $chartColors = [];
                        foreach ($poll->options as $index => $opt) {
                            $count = $opt->votes->count();
                            if ($count > 0) {
                                $chartData[] = $count;
                                $chartLabels[] = $opt->getDisplayText();
                                $chartColors[] = $colorMap[$opt->id] ?? '#3B82F6';
                            }
                        }
                    @endphp
                    
                    new Chart(imageCtx, {
                        type: 'doughnut',
                        data: {
                            labels: @json($chartLabels),
                            datasets: [{
                                data: @json($chartData),
                                backgroundColor: @json($chartColors),
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

    <!-- Custom CSS for Image Poll Aspect Ratio Optimization -->
    <style>
        /* Responsive Grid Layout for Image Results */
        .image-result-card { position: relative; z-index: 1; display: block; width: 100%; isolation: isolate; }
        .aspect-square { width: 100%; aspect-ratio: 1 / 1; height: auto; position: relative; }
        .results-grid { display: grid; gap: 1rem; }
        
        /* Ensure consistent aspect ratio across all image containers */
        .aspect-square img {
            object-fit: cover;
            object-position: center;
        }
        
        /* Smooth transitions for responsive changes */
        .image-result-card {
            transition: all 0.3s ease-in-out;
        }
        
        /* Progress bar animation */
        .progress-fill {
            transition: width 0.8s ease-in-out;
        }
        
        /* Chart responsiveness */
        .chart-wrapper {
            position: relative;
            height: 300px;
        }
        
        @media (max-width: 640px) {
            .chart-wrapper {
                height: 250px;
            }
        }
    </style>

    <!-- Image Modal for Media -->
    <div id="media-lightbox" class="fixed inset-0 bg-black bg-opacity-75 z-50 hidden flex items-center justify-center p-4">
        <div class="relative max-w-4xl max-h-full w-full">
            <!-- Close Button -->
            <button id="media-lightbox-close" class="absolute top-4 right-4 z-10 bg-black bg-opacity-50 text-white rounded-full w-10 h-10 flex items-center justify-center hover:bg-opacity-75 transition-all duration-200">
                <i class="fa-solid fa-times text-sm"></i>
            </button>
            
            <!-- Image -->
            <img id="media-lightbox-image" src="" alt="" class="w-full h-full object-contain rounded-lg">
            
            <!-- Image Title -->
            <div id="media-lightbox-title" class="absolute bottom-4 left-4 right-4 bg-black bg-opacity-50 text-white p-3 rounded-lg text-center"></div>
        </div>
    </div>

    <script>
        function openImageModal(imageUrl, title) {
            const modal = document.getElementById('media-lightbox');
            const image = document.getElementById('media-lightbox-image');
            const titleElement = document.getElementById('media-lightbox-title');
            
            image.src = imageUrl;
            image.alt = title;
            titleElement.textContent = title;
            
            modal.classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        }
        
        function closeMediaModal() {
            const modal = document.getElementById('media-lightbox');
            modal.classList.add('hidden');
            document.body.style.overflow = 'auto';
        }
        
        // Event listeners
        document.getElementById('media-lightbox-close').addEventListener('click', closeMediaModal);
        document.getElementById('media-lightbox').addEventListener('click', function(e) {
            if (e.target === this) {
                closeMediaModal();
            }
        });
        
        // Close on Escape key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeMediaModal();
            }
        });
    </script>
</x-app-layout>
