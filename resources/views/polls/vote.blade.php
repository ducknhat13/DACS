<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-3xl text-gray-800 dark:text-gray-200 leading-tight">{{ $poll->question }}</h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <!-- Poll Status -->
            @if ($poll->is_closed)
                <div class="mb-6 p-4 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg">
                    <div class="flex items-center gap-2 text-red-800 dark:text-red-200">
                        <i class="fa-solid fa-lock"></i>
                        <span class="font-semibold">This poll is closed</span>
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


            <!-- Vote Form -->
            @if (!$hasVoted || $isOwner)
            <div class="card mb-6">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-200 mb-4 flex items-center gap-2">
                        <i class="fa-solid fa-vote-yea text-indigo-600" aria-hidden="true"></i>
                        {{ __('messages.cast_your_vote') }}
                    </h3>
                    
                    <form method="POST" action="{{ route('polls.vote.store', $poll->slug) }}" class="space-y-3">
                        @csrf
                        @if ($poll->poll_type === 'ranking')
                            <div class="mb-4">
                                <p class="text-sm text-gray-600 dark:text-gray-300 mb-3">
                                    {{ __('messages.rank_options') }}
                                </p>
                                <div id="sortable-options" class="space-y-2">
                                    @foreach ($poll->options as $option)
                                        <div class="option-item flex items-center gap-3 p-3 bg-gray-50 dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 cursor-move" data-option-id="{{ $option->id }}" draggable="true">
                                            <i class="fa-solid fa-grip-vertical text-gray-400"></i>
                                            <span class="flex-1 text-gray-800 dark:text-gray-200">{{ $option->option_text }}</span>
                                            <span class="rank-badge px-2 py-1 bg-indigo-100 dark:bg-indigo-900 text-indigo-800 dark:text-indigo-200 rounded text-sm font-medium">
                                                Rank <span class="rank-number">-</span>
                                            </span>
                                        </div>
                                    @endforeach
                                </div>
                                <input type="hidden" name="ranking" id="ranking-input">
                            </div>
                        @else
                            <div class="space-y-2">
                                @foreach ($poll->options as $option)
                                    @php $isOtherOpt = strtolower(trim($option->option_text)) === 'other'; @endphp
                                    @if ($isOtherOpt)
                                        @continue
                                    @endif
                                    <label class="flex items-center gap-3 p-3 bg-gray-50 dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 hover:bg-gray-100 dark:hover:bg-gray-700 cursor-pointer transition-colors">
                                        <input type="{{ $poll->allow_multiple ? 'checkbox' : 'radio' }}" 
                                               name="{{ $poll->allow_multiple ? 'options[]' : 'options' }}" 
                                               value="{{ $option->id }}" 
                                               class="option-input w-4 h-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500">
                                        <span class="text-gray-800 dark:text-gray-200">{{ $option->option_text }}</span>
                                    </label>
                                @endforeach
                                @php
                                    $hasOther = $poll->options->contains(function($opt){
                                        return strtolower(trim($opt->option_text)) === 'other';
                                    });
                                @endphp
                                @if ($hasOther)
                                <!-- Other option row behaves like an option with input -->
                                <label class="block p-3 bg-gray-50 dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 cursor-pointer hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                                    <div class="flex items-center gap-3">
                                        <input type="{{ $poll->allow_multiple ? 'checkbox' : 'radio' }}" name="{{ $poll->allow_multiple ? 'options[]' : 'options' }}" value="__other__" class="option-other-check w-4 h-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500">
                                        <span class="text-gray-800 dark:text-gray-200">{{ __('messages.other') }}:</span>
                                        <input type="text" name="other_option" class="flex-1 px-3 py-2 rounded border border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-white focus:ring-indigo-500 focus:border-indigo-500" placeholder="{{ __('messages.type_your_answer') }}">
                                    </div>
                                </label>
                                @endif
                            </div>
                        @endif

                        <div class="hidden md:flex justify-between items-center pt-4">
                            <a href="{{ route('polls.show', $poll->slug) }}" class="btn btn-neutral" aria-label="{{ __('messages.view_results') }}">
                                <i class="fa-solid fa-chart-bar mr-2" aria-hidden="true"></i>
                                {{ __('messages.view_results') }}
                            </a>
                            <button type="submit" class="btn btn-primary" aria-label="{{ __('messages.submit_vote') }}">
                                <i class="fa-solid fa-check mr-2" aria-hidden="true"></i>
                                {{ __('messages.submit_vote') }}
                            </button>
                        </div>
                        <!-- Mobile submit bar -->
                        <div class="md:hidden submit-bar bg-white/85 dark:bg-gray-800/85 border-t border-gray-200 dark:border-gray-700 mt-4">
                            <div class="submit-bar-inner">
                                <button type="submit" class="btn btn-primary w-full" aria-label="{{ __('messages.submit_vote') }}">
                                    <i class="fa-solid fa-check mr-2" aria-hidden="true"></i>
                                    {{ __('messages.submit_vote') }}
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            @else
            <!-- Show View Results button for guests who have voted -->
            <div class="card mb-6">
                <div class="p-6 text-center">
                    <div class="mb-4">
                        <i class="fa-solid fa-check-circle text-4xl text-green-600 mb-3"></i>
                        <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-200 mb-2">
                            Thank you for voting!
                        </h3>
                        <p class="text-gray-600 dark:text-gray-300">
                            Your vote has been recorded. You can view the results below.
                        </p>
                    </div>
                    <a href="{{ route('polls.show', $poll->slug) }}" class="btn btn-primary">
                        <i class="fa-solid fa-chart-bar mr-2"></i>
                        View Results
                    </a>
                </div>
            </div>
            @endif

            <!-- Comments Section -->
            @if ($poll->allow_comments)
            <div class="card">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-200 mb-4 flex items-center gap-2">
                        <i class="fa-solid fa-comments text-green-600"></i>
                        Comments
                    </h3>

                    <!-- Add Comment Form -->
                    <form method="POST" action="{{ route('polls.comment', $poll->slug) }}" class="mb-6">
                        @csrf
                        <div class="flex gap-3">
                            <input type="text" name="content" placeholder="Add a comment..." 
                                   class="flex-1 px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent dark:bg-gray-800 dark:text-white" required>
                            <button type="submit" class="btn btn-primary">
                                <i class="fa-solid fa-paper-plane mr-2"></i>
                                Post
                            </button>
                        </div>
                    </form>

                    <!-- Comments List -->
                    <div class="space-y-3">
                        @forelse ($comments as $comment)
                            <div class="p-3 bg-gray-50 dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700">
                                <div class="flex items-center gap-2 mb-2">
                                    <span class="font-medium text-gray-800 dark:text-gray-200">
                                        {{ $comment->user ? $comment->user->name : $comment->voter_name }}
                                    </span>
                                    <span class="text-xs text-gray-500 dark:text-gray-400">
                                        {{ $comment->created_at->diffForHumans() }}
                                    </span>
                                </div>
                                <p class="text-gray-700 dark:text-gray-300">{{ $comment->content }}</p>
                            </div>
                        @empty
                            <p class="text-gray-500 dark:text-gray-400 text-center py-4">No comments yet. Be the first to comment!</p>
                        @endforelse
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Handle Other behaviour for both regular and ranking
            (function(){
                const otherInput = document.querySelector('input[name="other_option"]');
                const otherCheck = document.querySelector('.option-other-check');
                const optionInputs = document.querySelectorAll('.option-input');
                const isMultiple = {{ $poll->allow_multiple ? 'true' : 'false' }};
                if (!otherInput || !otherCheck) return;
                otherInput.addEventListener('input', function(){
                    const hasText = this.value.trim() !== '';
                    if (hasText) {
                        otherCheck.checked = true;
                        if (!isMultiple) {
                            optionInputs.forEach(i=> i.checked = false);
                        }
                    } else if (!isMultiple) {
                        otherCheck.checked = false;
                    }
                });
                otherCheck.addEventListener('change', function(){
                    if (!this.checked) {
                        otherInput.value = '';
                    } else if (!isMultiple) {
                        optionInputs.forEach(i=> i.checked = false);
                    }
                });
            })();
            @if ($poll->poll_type === 'ranking')
            const sortableContainer = document.getElementById('sortable-options');
            const rankingInput = document.getElementById('ranking-input');
            
            // Make options sortable
            let draggedElement = null;
            
            sortableContainer.addEventListener('dragstart', function(e) {
                draggedElement = e.target.closest('.option-item');
                if (!draggedElement) return;
                draggedElement.style.opacity = '0.5';
            });
            
            sortableContainer.addEventListener('dragend', function(e) {
                const el = e.target.closest('.option-item') || draggedElement;
                if (el) el.style.opacity = '1';
                draggedElement = null;
                updateRankings();
            });
            
            sortableContainer.addEventListener('dragover', function(e) {
                e.preventDefault();
            });
            
            sortableContainer.addEventListener('drop', function(e) {
                e.preventDefault();
                const targetElement = e.target.closest('.option-item');
                if (draggedElement && targetElement) {
                    if (draggedElement !== targetElement) {
                        sortableContainer.insertBefore(draggedElement, targetElement);
                        updateRankings();
                    }
                }
            });
            
            function updateRankings() {
                const options = sortableContainer.querySelectorAll('.option-item');
                const ranking = {};
                
                options.forEach((option, index) => {
                    const optionId = option.dataset.optionId;
                    const rank = index + 1;
                    ranking[optionId] = rank;
                    
                    const rankNumber = option.querySelector('.rank-number');
                    rankNumber.textContent = rank;
                });
                
                rankingInput.value = JSON.stringify(ranking);
            }
            
            // Initialize rankings
            updateRankings();
            @endif
        });
    </script>
</x-app-layout>
