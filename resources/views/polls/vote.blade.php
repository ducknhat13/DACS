{{--
    Page: polls/vote
    - Trang bỏ phiếu: hiển thị danh sách lựa chọn, cho phép chọn theo cấu hình poll.
    - Frontend: xử lý disabled nếu poll closed/expired, hiển thị lỗi validate.
--}}
{{--
    Vote Poll Page - polls/vote.blade.php
    
    Trang vote cho poll với Material Design 3 UI.
    
    Features:
    - Poll media display: Images/videos trong description
    - Poll status banner: Hiển thị nếu poll đã đóng
    - Vote form: Khác nhau tùy poll type (standard/ranking/image)
    - Results view: Hiển thị kết quả sau khi vote
    - Comments section: Cho phép comment nếu poll.allow_comments = true
    
    Poll Types:
    - Standard: Radio buttons hoặc checkboxes
    - Ranking: Drag & drop để rank options
    - Image: Image cards với checkbox/radio
    
    JavaScript:
    - Ranking drag & drop: Sortable.js hoặc HTML5 drag API
    - Image fullscreen modal: Click để xem image full size
    - Form validation: Client-side validation trước khi submit
    - Vote submission: AJAX hoặc form post với loading state
    
    Data từ Controller:
    - $poll: Poll model với relationships (options, votes)
    - $hasVoted: Boolean, đã vote chưa
    - $isOwner: Boolean, có phải owner không
    
    @author QuickPoll Team
--}}
<x-app-layout>
    <x-slot name="header">
        <div>
            <h2 class="font-semibold text-3xl text-gray-800 dark:text-gray-200 leading-tight">{{ $poll->title ?? $poll->question }}</h2>
            @if($poll->description)
                <p class="text-body-medium text-on-surface-variant mt-2">{{ $poll->description }}</p>
            @endif
        </div>
    </x-slot>

    <div class="py-6 page-transition">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            {{-- Poll Media Section: Hiển thị media trong mô tả poll (nếu có) --}}
            @if($poll->hasDescriptionMedia())
                <div class="mb-8">
                    <div class="card card-elevated">
                        <div class="card-header">
                            <div class="card-title">
                                <i class="fa-solid fa-images text-primary"></i>
                                {{ __('messages.media_preview') }}
                            </div>
                        </div>
                        <div class="card-content">
                            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                                @foreach($poll->getDescriptionMedia() as $mediaItem)
                                    @php
                                        $media = is_string($mediaItem) ? json_decode($mediaItem, true) : $mediaItem;
                                    @endphp
                                    @if($media && isset($media['type']))
                                        <div class="media-display-item bg-[var(--surface-variant)] rounded-xl border border-[color:var(--outline)] overflow-hidden hover:shadow-lg transition-shadow duration-300">
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
            {{-- Poll Status Banner: thông báo poll đã đóng, không thể vote --}}
            @if ($poll->is_closed)
                <div class="mb-6 p-4 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg">
                    <div class="flex items-center gap-2 text-red-800 dark:text-red-200">
                        <i class="fa-solid fa-lock"></i>
                        <span class="font-semibold">{{ __('messages.poll_is_closed') }}</span>
                    </div>
                </div>
            @endif

            {{-- Flash Messages: hiển thị thông báo thành công/lỗi từ session --}}
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


            {{-- Vote Form: Chỉ hiển thị nếu chưa vote hoặc là owner --}}
            @if (!$hasVoted || $isOwner)
            <div class="card card-elevated mb-6 animate-fade-in-up">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-200 mb-4 flex items-center gap-2">
                        <i class="fa-solid fa-vote-yea text-indigo-600" aria-hidden="true"></i>
                        {{ __('messages.cast_your_vote') }}
                    </h3>
                    
                    {{-- Vote Form: submit đến VoteController@store --}}
                    <form method="POST" action="{{ route('polls.vote.store', $poll->slug) }}" class="space-y-3">
                        @csrf
                        
                        {{-- Ranking Poll: Drag & drop để rank options --}}
                        @if ($poll->poll_type === 'ranking')
                            <div class="mb-4">
                                {{-- Sortable container: dùng HTML5 drag API để sắp xếp --}}
                                <div id="sortable-options" class="space-y-2">
                                    @foreach ($poll->options as $option)
                                        <div class="option-item flex items-center gap-3 p-3 bg-[var(--surface-variant)] rounded-lg border border-[color:var(--outline)] cursor-move" data-option-id="{{ $option->id }}" draggable="true">
                                            <i class="fa-solid fa-grip-vertical text-gray-400"></i>
                                            <span class="flex-1 text-gray-800 dark:text-gray-200">{{ $option->option_text }}</span>
                                            <span class="rank-badge px-2 py-1 bg-indigo-100 dark:bg-indigo-900 text-indigo-800 dark:text-indigo-200 rounded text-sm font-medium">
                                                Rank <span class="rank-number">-</span>
                                            </span>
                                        </div>
                                    @endforeach
                                </div>
                                {{-- Input ẩn để submit thứ hạng (JSON) --}}
                                <input type="hidden" name="ranking" id="ranking-input">
                            </div>
                        {{-- Image Poll: Image cards với checkbox/radio --}}
                        @elseif ($poll->poll_type === 'image')
                            {{-- Image Poll Options Grid --}}
                            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
                                @foreach ($poll->options as $option)
                                    <label class="image-option-card bg-[var(--surface)] text-[color:var(--on-surface)] rounded-xl border border-[color:var(--outline)] hover:shadow-lg transition-all duration-200 cursor-pointer group w-full sm:max-w-xs mx-auto">
                                        <div class="relative">
                                            <!-- Image -->
                                            <div class="aspect-square overflow-hidden rounded-t-xl">
                                                @if($option->hasImage())
                                                    <img src="{{ $option->image_url }}" 
                                                         alt="{{ $option->getImageAltText() }}" 
                                                         class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-200 select-none">
                                                    <!-- Click target to open fullscreen (prevents toggling input) -->
                                                    <button type="button"
                                                            class="image-click-target absolute inset-0 cursor-zoom-in"
                                                            data-image-src="{{ $option->image_url }}"
                                                            data-image-title="{{ $option->getDisplayText() }}"
                                                            aria-label="View image fullscreen"></button>
                                                @else
                                                    <div class="w-full h-full bg-gray-100 dark:bg-gray-700 flex items-center justify-center">
                                                        <i class="fa-solid fa-image text-4xl text-gray-400"></i>
                                                    </div>
                                                @endif
                                            </div>
                                            
                                            <!-- Checkbox/Radio Button -->
                                            <div class="absolute top-2 left-2">
                                                <input type="{{ $poll->allow_multiple ? 'checkbox' : 'radio' }}" 
                                                       name="{{ $poll->allow_multiple ? 'options[]' : 'options' }}" 
                                                       value="{{ $option->id }}" 
                                                       class="option-input w-5 h-5 text-primary border-gray-300 rounded focus:ring-primary">
                                            </div>
                                        </div>
                                        
                                        <!-- Title -->
                                        <div class="p-4">
                                            <h4 class="font-medium text-gray-800 dark:text-gray-200 text-center">
                                                {{ $option->getDisplayText() }}
                                            </h4>
                                        </div>
                                    </label>
                                @endforeach
                            </div>
                            
                            <!-- Max Selections Info -->
                            @if($poll->max_image_selections)
                                <div class="mt-4 p-3 bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg">
                                    <div class="flex items-center gap-2 text-blue-800 dark:text-blue-200">
                                        <i class="fa-solid fa-info-circle"></i>
                                        <span class="text-sm">{{ __('messages.max_image_selections') }}: {{ $poll->max_image_selections }}</span>
                                    </div>
                                </div>
                            @endif
                        @else
                            <!-- Regular Poll Options -->
                            <div class="space-y-2">
                                @foreach ($poll->options as $option)
                                    @php $isOtherOpt = strtolower(trim($option->option_text)) === 'other'; @endphp
                                    @if ($isOtherOpt)
                                        @continue
                                    @endif
                                    <label class="flex items-center gap-3 p-3 bg-[var(--surface-variant)] rounded-lg border border-[color:var(--outline)] hover:bg-[var(--surface)] cursor-pointer transition-colors">
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
                                <label class="block p-3 bg-[var(--surface-variant)] rounded-lg border border-[color:var(--outline)] cursor-pointer hover:bg-[var(--surface)] transition-colors">
                                    <div class="flex items-center gap-3">
                                        <input type="{{ $poll->allow_multiple ? 'checkbox' : 'radio' }}" name="{{ $poll->allow_multiple ? 'options[]' : 'options' }}" value="__other__" class="option-other-check w-4 h-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500">
                                        <span class="text-gray-800 dark:text-gray-200">{{ __('messages.other') }}:</span>
                                        <input type="text" name="other_option" class="flex-1 px-3 py-2 rounded border border-[color:var(--outline)] bg-[var(--surface)] text-[color:var(--on-surface)] focus:ring-primary-500 focus:border-primary-500" placeholder="{{ __('messages.type_your_answer') }}">
                                    </div>
                                </label>
                                @endif
                            </div>
                            
                            <!-- Max Choices Info (for standard polls with multiple selection) -->
                            @if($poll->poll_type === 'standard' && $poll->allow_multiple && $poll->max_image_selections)
                                <div class="mt-4 p-3 bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg">
                                    <div class="flex items-center gap-2 text-blue-800 dark:text-blue-200">
                                        <i class="fa-solid fa-info-circle"></i>
                                        <span class="text-sm">{{ __('messages.max_choices') }}: {{ $poll->max_image_selections }}</span>
                                    </div>
                                </div>
                            @endif
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
                        <div class="md:hidden submit-bar bg-[var(--surface)]/85 border-t border-[color:var(--outline)] mt-4">
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
                            {{ __('messages.thank_you') }}
                        </h3>
                        <p class="text-gray-600 dark:text-gray-300">
                            {{ __('messages.view_results') }}
                        </p>
                    </div>
                    <a href="{{ route('polls.show', $poll->slug) }}" class="btn btn-primary">
                        <i class="fa-solid fa-chart-bar mr-2"></i>
                        {{ __('messages.view_results') }}
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
                        {{ __('messages.comments') }}
                    </h3>

                    <!-- Add Comment Form -->
                    <form method="POST" action="{{ route('polls.comment', $poll->slug) }}" class="mb-6">
                        @csrf
                        <div class="flex gap-3">
                            <input type="text" name="content" placeholder="{{ __('messages.add_a_comment') }}" 
                                   class="flex-1 px-4 py-2 border border-[color:var(--outline)] rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent bg-[var(--surface)] text-[color:var(--on-surface)]" required>
                            <button type="submit" class="btn btn-primary">
                                <i class="fa-solid fa-paper-plane mr-2"></i>
                                {{ __('messages.post_comment') }}
                            </button>
                        </div>
                    </form>

                    <!-- Comments List -->
                    <div class="space-y-3">
                        @forelse ($comments as $comment)
                            <div class="p-3 bg-[var(--surface-variant)] rounded-lg border border-[color:var(--outline)]">
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
                            <p class="text-gray-500 dark:text-gray-400 text-center py-4">{{ __('messages.no_comments_yet') }}</p>
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
            
            @if ($poll->poll_type === 'image' && $poll->max_image_selections)
            // Handle max image selections
            const maxSelections = {{ $poll->max_image_selections }};
            const imageOptions = document.querySelectorAll('.image-option-card input[type="checkbox"]');
            
            imageOptions.forEach(checkbox => {
                checkbox.addEventListener('change', function() {
                    const checkedBoxes = document.querySelectorAll('.image-option-card input[type="checkbox"]:checked');
                    
                    if (checkedBoxes.length > maxSelections) {
                        this.checked = false;
                        alert(`You can only select up to ${maxSelections} image(s).`);
                    }
                });
            });
            @endif
        });
    </script>

    <!-- Custom CSS for Image Poll Aspect Ratio Optimization -->
    <style>
        /* Responsive Grid Layout for Image Options */
        .image-option-card {
            position: relative;
            z-index: 1;
            display: block;
            width: 100%;
            isolation: isolate;
        }

        /* Use native aspect-ratio to prevent distortion when width shrinks */
        .aspect-square {
            width: 100%;
            aspect-ratio: 1 / 1;
            height: auto;
            position: relative;
        }
        
        /* Ensure consistent aspect ratio across all image containers */
        .aspect-square img {
            object-fit: cover;
            object-position: center;
        }
        
        /* Smooth transitions for responsive changes */
        .image-option-card {
            transition: all 0.3s ease-in-out;
            isolation: isolate;
        }
        
        /* Prevent overlapping in grid */
        .grid {
            display: grid;
            gap: 1rem;
        }
        
        .image-option-card {
            display: block;
            width: 100%;
        }
        
        /* Selection indicator positioning */
        .selection-indicator {
            transition: all 0.2s ease-in-out;
        }
        
        .image-option-card:hover .selection-indicator {
            transform: scale(1.1);
        }
        
        /* -------- Image Lightbox (Modal) ---------- */
        .modal-overlay {
            position: fixed;
            inset: 0;
            background: rgba(0,0,0,0.7);
            display: none;
            align-items: center;
            justify-content: center;
            z-index: 50;
        }
        .modal-overlay.active { display: flex; }
        .modal-content {
            background: rgba(255,255,255,0.98);
            color: #111827;
            border-radius: 0.75rem;
            max-width: 90vw;
            width: 100%;
            max-height: 90vh;
            padding: 1rem;
            position: relative;
            transform: scale(0.98);
            opacity: 0;
            transition: opacity .18s ease, transform .18s ease;
        }
        .dark .modal-content { background: rgba(31,41,55,0.98); color: #e5e7eb; }
        .modal-overlay.active .modal-content { opacity: 1; transform: scale(1); }
        .modal-image {
            max-height: 76vh;
            width: 100%;
            object-fit: contain;
            border-radius: 0.5rem;
            user-select: none;
        }
        .modal-close {
            position: absolute; top: .5rem; right: .75rem;
            background: rgba(0,0,0,0.5); color: #fff; border: 0; width: 36px; height: 36px; border-radius: 9999px;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .modal-nav {
            position: absolute; top: 50%; transform: translateY(-50%);
            width: 40px; height: 40px; border-radius: 9999px; border: 0;
            background: rgba(0,0,0,0.45); color: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .modal-prev { left: .5rem; }
        .modal-next { right: .5rem; }
        .modal-title { margin-top: .5rem; text-align: center; font-weight: 500; }
        @media (max-width: 640px) {
            .modal-content { max-width: 100vw; max-height: 100vh; border-radius: 0; height: 100vh; }
            .modal-image { max-height: calc(100vh - 7rem); }
            .modal-prev, .modal-next { width: 36px; height: 36px; }
        }
    </style>

    <!-- Lightbox Modal for viewing full image -->
    <div id="image-lightbox" class="modal-overlay">
        <div class="modal-content">
            <button type="button" id="lightbox-close" class="modal-close" aria-label="Close">
                <i class="fa-solid fa-xmark"></i>
            </button>
            <button type="button" id="lightbox-prev" class="modal-nav modal-prev" aria-label="Previous">
                <i class="fa-solid fa-chevron-left"></i>
            </button>
            <button type="button" id="lightbox-next" class="modal-nav modal-next" aria-label="Next">
                <i class="fa-solid fa-chevron-right"></i>
            </button>
            <img id="lightbox-image" src="" alt="Full image" class="modal-image">
            <div id="lightbox-title" class="modal-title"></div>
        </div>
    </div>

    <script>
        // Fullscreen image viewer logic
        document.addEventListener('DOMContentLoaded', function () {
            const overlay = document.getElementById('image-lightbox');
            const imgEl = document.getElementById('lightbox-image');
            const titleEl = document.getElementById('lightbox-title');
            const btnClose = document.getElementById('lightbox-close');
            const btnPrev = document.getElementById('lightbox-prev');
            const btnNext = document.getElementById('lightbox-next');

            // Collect all image items (src + title)
            const items = Array.from(document.querySelectorAll('.image-click-target')).map((el) => ({
                src: el.getAttribute('data-image-src'),
                title: el.getAttribute('data-image-title') || ''
            }));
            let current = -1;

            function openAt(index) {
                if (index < 0 || index >= items.length) return;
                current = index;
                const it = items[index];
                imgEl.src = it.src;
                titleEl.textContent = it.title || '';
                overlay.classList.add('active');
                document.body.style.overflow = 'hidden';
                // toggle nav visibility
                const multi = items.length > 1;
                btnPrev.style.display = multi ? 'inline-flex' : 'none';
                btnNext.style.display = multi ? 'inline-flex' : 'none';
            }
            function closeLightbox() {
                overlay.classList.remove('active');
                document.body.style.overflow = '';
                imgEl.src = '';
            }
            function prev() { if (items.length) openAt((current - 1 + items.length) % items.length); }
            function next() { if (items.length) openAt((current + 1) % items.length); }

            // Open on click (prevent label toggling)
            document.body.addEventListener('click', function (e) {
                const btn = e.target.closest('.image-click-target');
                if (btn) {
                    e.preventDefault();
                    e.stopPropagation();
                    const idx = items.indexOf(items.find(it => it.src === btn.getAttribute('data-image-src')));
                    openAt(idx);
                }
            });
            // Close behaviors
            overlay.addEventListener('click', (e) => { if (e.target === overlay) closeLightbox(); });
            btnClose.addEventListener('click', closeLightbox);
            // Navigation
            btnPrev.addEventListener('click', (e) => { e.stopPropagation(); prev(); });
            btnNext.addEventListener('click', (e) => { e.stopPropagation(); next(); });
            // Keyboard support
            document.addEventListener('keydown', (e) => {
                if (!overlay.classList.contains('active')) return;
                if (e.key === 'Escape') closeLightbox();
                if (e.key === 'ArrowLeft') prev();
                if (e.key === 'ArrowRight') next();
            });
        });
    </script>

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
