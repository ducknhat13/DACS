<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-headline-large text-on-surface font-semibold">{{ __('Dashboard') }}</h1>
                <p class="text-body-medium text-on-surface-variant mt-1">{{ __('messages.your_polls') }}</p>
            </div>
            <div class="flex items-center gap-3">
                <button class="btn btn-neutral" data-tooltip="Refresh">
                    <i class="fa-solid fa-refresh"></i>
                </button>
                <a href="{{ route('polls.create') }}" class="btn btn-primary">
                    <i class="fa-solid fa-plus"></i>
                    {{ __('messages.create_poll') }}
                </a>
            </div>
        </div>
    </x-slot>

    <div class="pt-4 pb-12 page-transition">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="card card-elevated animate-fade-in-up">
                <div class="text-gray-900 dark:text-gray-100">
                    <!-- Filter Bar with Material Chips -->
                    <form method="GET" class="mb-6">
                        <div class="bg-surface-variant rounded-2xl p-4 mb-6">
                            <div class="flex flex-col lg:flex-row gap-4">
                                <!-- Search Input -->
                                <div class="input-field flex-1">
                                    <input type="text" name="q" value="{{ request('q') }}" placeholder=" " class="bg-surface">
                                    <label>{{ __('messages.search_placeholder') }} / slug</label>
                                </div>
                                
                                <!-- Filter Chips -->
                                <div class="flex flex-wrap gap-2 items-center">
                                    @php $st = request('status','all'); @endphp
                                    <button type="button" class="filter-chip {{ $st === 'all' ? 'active' : '' }}" data-status="all">
                                        <i class="fa-solid fa-list"></i>
                                        All
                                    </button>
                                    <button type="button" class="filter-chip {{ $st === 'open' ? 'active' : '' }}" data-status="open">
                                        <i class="fa-solid fa-play-circle"></i>
                                        Active
                                    </button>
                                    <button type="button" class="filter-chip {{ $st === 'closed' ? 'active' : '' }}" data-status="closed">
                                        <i class="fa-solid fa-stop-circle"></i>
                                        Closed
                                    </button>
                                    
                                    <!-- Sort Options -->
                                    @php $so = request('sort','newest'); @endphp
                                    <button type="button" class="filter-chip {{ $so === 'newest' ? 'active' : '' }}" data-sort="newest">
                                        <i class="fa-solid fa-clock"></i>
                                        Newest
                                    </button>
                                    <button type="button" class="filter-chip {{ $so === 'votes' ? 'active' : '' }}" data-sort="votes">
                                        <i class="fa-solid fa-chart-line"></i>
                                        Most Votes
                                    </button>
                                </div>
                                
                                <!-- Hidden inputs for form submission -->
                                <input type="hidden" name="status" id="status-input" value="{{ $st }}">
                                <input type="hidden" name="sort" id="sort-input" value="{{ $so }}">
                            </div>
                        </div>
                    </form>

                    @php
                        $polls = $polls ?? [];
                    @endphp

                    @if (empty($polls) || count($polls) === 0)
                        <div class="text-center py-12">
                            <div class="w-16 h-16 mx-auto mb-4 rounded-full bg-surface-variant flex items-center justify-center">
                                <i class="fa-solid fa-poll text-2xl text-on-surface-variant"></i>
                            </div>
                            <h3 class="text-title-medium text-on-surface mb-2">{{ __('messages.no_polls') }}</h3>
                            <p class="text-body-medium text-on-surface-variant mb-6">Create your first poll to get started</p>
                            <a href="{{ route('polls.create') }}" class="btn btn-primary">
                                <i class="fa-solid fa-plus"></i>
                                {{ __('messages.create_poll') }}
                            </a>
                        </div>
                    @else
                        <!-- Polls Grid -->
                        <div class="poll-grid grid grid-cols-1 lg:grid-cols-2 xl:grid-cols-3 gap-4">
                            @foreach ($polls as $index => $p)
                                @php
                                    $total = $p->votes_count ?? 0;
                                    $created = $p->created_at ? \Carbon\Carbon::parse($p->created_at)->format('M d, Y') : '';
                                    $deadline = $p->auto_close_at ? \Carbon\Carbon::parse($p->auto_close_at)->format('M d, Y') : null;
                                    $isClosed = isset($p->is_closed) ? (bool)$p->is_closed : false;
                                    $isExpired = $deadline && \Carbon\Carbon::parse($deadline)->isPast();
                                @endphp
                                <div class="poll-card animate-fade-in-up stagger-item" style="animation-delay: {{ $index * 0.1 }}s;">
                                    <!-- Action Menu Button -->
                                    <div class="poll-actions">
                                        <button type="button" class="action-menu-button" onclick="toggleActionMenu('menu{{ $p->id }}')">
                                            <i class="fa-solid fa-ellipsis-vertical"></i>
                                        </button>
                                        <div id="menu{{ $p->id }}" class="action-menu">
                                            <form method="POST" action="{{ route('polls.toggle', $p->slug) }}">
                                                @csrf
                                                <button type="submit" class="action-menu-item">
                                                    <i class="fa-solid {{ $isClosed ? 'fa-play' : 'fa-stop' }}"></i>
                                                    {{ $isClosed ? __('messages.reopen') : __('messages.close_poll') }}
                                                </button>
                                            </form>
                                            <a href="{{ route('polls.export', $p->slug) }}" class="action-menu-item">
                                                <i class="fa-solid fa-download"></i>
                                                {{ __('messages.export_csv') }}
                                            </a>
                                            <form method="POST" action="{{ route('polls.destroy', $p->slug) }}" onsubmit="return confirm('{{ __('messages.delete_confirm') }}');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="action-menu-item danger">
                                                    <i class="fa-solid fa-trash"></i>
                                                    {{ __('messages.delete') }}
                                                </button>
                                            </form>
                                        </div>
                                    </div>

                                    <!-- Poll Title -->
                                    <a href="{{ route('polls.vote', $p->slug) }}" class="poll-title block">
                                        {{ $p->question }}
                                    </a>

                                    <!-- Poll Meta -->
                                    <div class="poll-meta">
                                        <div class="poll-meta-item">
                                            <i class="fa-solid fa-users"></i>
                                            <span>{{ $total }} participants</span>
                                        </div>
                                        @if($deadline)
                                            <div class="poll-meta-item {{ $isExpired ? 'text-error' : '' }}">
                                                <i class="fa-solid fa-clock"></i>
                                                <span>{{ $isExpired ? 'Expired' : 'Ends' }}: {{ $deadline }}</span>
                                            </div>
                                        @endif
                                        <div class="poll-meta-item">
                                            <i class="fa-solid fa-calendar"></i>
                                            <span>Created: {{ $created }}</span>
                                        </div>
                                    </div>

                                    <!-- Poll Status -->
                                    <div class="poll-footer">
                                        <span class="poll-status {{ $isClosed ? 'closed' : 'active' }}">
                                            <i class="fa-solid {{ $isClosed ? 'fa-stop-circle' : 'fa-play-circle' }}"></i>
                                            {{ $isClosed ? 'Closed' : 'Active' }}
                                        </span>
                                        
                                        <a href="{{ route('polls.show', $p->slug) }}" class="text-primary text-sm font-medium hover:underline">
                                            View Results →
                                        </a>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Floating Action Button -->
    <x-fab :href="route('polls.create')" icon="+" />

    <!-- Modal tạo poll -->
    <div id="createPollModal" class="hidden">
        <div>
            <div class="flex items-start justify-between mb-4">
                <div>
                    <h4 class="text-xl font-semibold">{{ __('messages.new_poll') }}</h4>
                </div>
                <button type="button" id="closeCreatePoll" class="btn btn-neutral">✕</button>
            </div>

            <form method="POST" action="{{ route('polls.store') }}" class="space-y-4">
                @if ($errors->any())
                    <div class="bg-red-100 text-red-800 p-3 rounded text-sm">
                        <ul class="list-disc pl-5">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
                @csrf
                <div class="space-y-4">
                <div>
                    <label class="block mb-1 text-sm font-medium">{{ __('messages.question') }}</label>
                    <textarea name="question" rows="3" class="w-full border rounded-lg p-3 dark:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500" placeholder="Ví dụ: Địa điểm bạn muốn đi cuối tuần này?" required></textarea>
                </div>
                
                <div>
                    <label class="block mb-1 text-sm font-medium">{{ __('messages.poll_type') }}</label>
                    <select name="poll_type" id="poll_type" class="w-full border rounded-lg p-3 dark:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500" required>
                        <option value="regular">{{ __('messages.regular_poll') }}</option>
                        <option value="ranking">{{ __('messages.ranking_poll') }}</option>
                    </select>
                </div>
                
                <div>
                    <label class="block mb-1 text-sm font-medium">{{ __('messages.options') }}</label>
                    <div id="modalOptions" class="space-y-2 max-h-64 sm:max-h-72 overflow-y-auto pr-1">
                        <div class="flex items-center gap-2 option-row">
                            <input type="text" name="options[]" class="flex-1 border rounded-lg p-3 dark:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500" placeholder="{{ __('messages.option_placeholder') }} 1" required>
                            <button type="button" class="btn btn-neutral removeOption" aria-label="Remove option">✕</button>
                        </div>
                        <div class="flex items-center gap-2 option-row">
                            <input type="text" name="options[]" class="flex-1 border rounded-lg p-3 dark:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500" placeholder="{{ __('messages.option_placeholder') }} 2" required>
                            <button type="button" class="btn btn-neutral removeOption" aria-label="Remove option">✕</button>
                        </div>
                    </div>
                    <div class="flex items-center justify-between mt-2">
                        <button type="button" id="addModalOption" class="btn btn-neutral">{{ __('messages.add_option') }}</button>
                        <span class="text-xs text-gray-500">Tối đa 10 lựa chọn</span>
                    </div>
                </div>
                
                <div id="regular-options" class="p-3 rounded-lg bg-gray-50 dark:bg-gray-900/40">
                    <label class="inline-flex items-center gap-2">
                        <input type="checkbox" name="allow_multiple" value="1" class="rounded">
                        <span>{{ __('messages.allow_multiple') }}</span>
                    </label>
                </div>
                
                <div id="ranking-info" class="hidden bg-blue-50 dark:bg-blue-900/20 p-3 rounded-lg">
                    <p class="text-sm text-blue-800 dark:text-blue-200">{{ __('messages.ranking_info') }}</p>
                </div>
                
                <div class="mt-2">
                    <label class="inline-flex items-center gap-2">
                        <input type="checkbox" id="is_private" name="is_private" value="1" class="rounded">
                        <span>{{ __('messages.private') }}</span>
                    </label>
                </div>
                <div id="access_key_wrap" class="hidden">
                    <label class="block mb-1 text-sm font-medium mt-2">{{ __('messages.access_key') }}</label>
                    <input type="text" name="access_key" class="w-full border rounded-lg p-3 dark:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500" placeholder="Nhập key (tối đa 64 ký tự)">
                </div>
                </div>
                <div class="flex justify-end gap-2 sticky bottom-0 bg-white dark:bg-gray-800 pt-4">
                    <button type="button" id="cancelCreatePoll" class="btn btn-neutral">{{ __('messages.cancel') }}</button>
                    <button type="submit" class="btn btn-primary">{{ __('messages.create') }}</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        // Filter Chips Functionality
        document.addEventListener('DOMContentLoaded', function() {
            // Handle filter chips
            const filterChips = document.querySelectorAll('.filter-chip');
            const statusInput = document.getElementById('status-input');
            const sortInput = document.getElementById('sort-input');
            
            filterChips.forEach(chip => {
                chip.addEventListener('click', function() {
                    const status = this.dataset.status;
                    const sort = this.dataset.sort;
                    
                    if (status) {
                        // Update status chips
                        document.querySelectorAll('[data-status]').forEach(c => c.classList.remove('active'));
                        this.classList.add('active');
                        statusInput.value = status;
                    }
                    
                    if (sort) {
                        // Update sort chips
                        document.querySelectorAll('[data-sort]').forEach(c => c.classList.remove('active'));
                        this.classList.add('active');
                        sortInput.value = sort;
                    }
                    
                    // Submit form
                    this.closest('form').submit();
                });
            });
        });

        function toggleActionMenu(id) {
            // Close all other menus
            document.querySelectorAll('.action-menu').forEach(function(el){ 
                if (el.id !== id) el.classList.remove('show'); 
            });
            
            // Toggle current menu
            const el = document.getElementById(id);
            if (!el) return;
            el.classList.toggle('show');
        }

        // Close menus when clicking outside
        document.addEventListener('click', function(e){
            const target = e.target;
            const isActionButton = target.closest('.action-menu-button');
            const isMenu = target.closest('.action-menu');
            
            if (!isActionButton && !isMenu) {
                document.querySelectorAll('.action-menu').forEach(function(el){ 
                    el.classList.remove('show'); 
                });
            }
        }, true);

        // Close menus on scroll or resize
        window.addEventListener('scroll', function() {
            document.querySelectorAll('.action-menu.show').forEach(function(el){ 
                el.classList.remove('show'); 
            });
        });

        window.addEventListener('resize', function() {
            document.querySelectorAll('.action-menu.show').forEach(function(el){ 
                el.classList.remove('show'); 
            });
        });

        // Add ripple effect to cards
        document.querySelectorAll('.poll-card').forEach(card => {
            card.addEventListener('click', function(e) {
                if (e.target.closest('.action-menu-button') || e.target.closest('.action-menu')) {
                    return; // Don't add ripple for menu interactions
                }
                
                const ripple = document.createElement('span');
                const rect = this.getBoundingClientRect();
                const size = Math.max(rect.width, rect.height);
                const x = e.clientX - rect.left - size / 2;
                const y = e.clientY - rect.top - size / 2;
                
                ripple.style.width = ripple.style.height = size + 'px';
                ripple.style.left = x + 'px';
                ripple.style.top = y + 'px';
                ripple.classList.add('ripple-effect');
                
                this.appendChild(ripple);
                
                setTimeout(() => {
                    ripple.remove();
                }, 600);
            });
        });

        (function(){
            const modal = document.getElementById('createPollModal');
            const openBtn = document.getElementById('openCreatePoll');
            const closeBtn = document.getElementById('closeCreatePoll');
            const cancelBtn = document.getElementById('cancelCreatePoll');
            const addBtn = document.getElementById('addModalOption');
            const options = document.getElementById('modalOptions');
            const isPrivate = document.getElementById('is_private');
            const accessWrap = document.getElementById('access_key_wrap');
            const pollType = document.getElementById('poll_type');
            const regularOptions = document.getElementById('regular-options');
            const rankingInfo = document.getElementById('ranking-info');

            function open(){ modal.classList.remove('hidden'); modal.classList.add('flex'); }
            function close(){ modal.classList.add('hidden'); modal.classList.remove('flex'); }

            if (openBtn) openBtn.addEventListener('click', open);
            if (closeBtn) closeBtn.addEventListener('click', close);
            if (cancelBtn) cancelBtn.addEventListener('click', close);
            if (modal) modal.addEventListener('click', (e)=>{ if (e.target === modal) close(); });
            if (addBtn) addBtn.addEventListener('click', function(){
                // Giới hạn tối đa 10 lựa chọn
                const current = options.querySelectorAll('.option-row').length;
                if (current >= 10) return;
                const row = document.createElement('div');
                row.className = 'flex items-center gap-2 option-row';
                row.innerHTML = `
                    <input type="text" name="options[]" class="flex-1 border rounded-lg p-3 dark:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500" placeholder="{{ __('messages.option_placeholder') }} ${current+1}" required>
                    <button type="button" class="btn btn-neutral removeOption" aria-label="Remove option">✕</button>
                `;
                options.appendChild(row);
            });
            // Xóa dòng option (event delegation)
            if (options) options.addEventListener('click', function(e){
                const btn = e.target.closest('.removeOption');
                if (!btn) return;
                const row = btn.closest('.option-row');
                if (row) {
                    row.remove();
                    // Cập nhật lại placeholder số thứ tự
                    options.querySelectorAll('.option-row input[type="text"]').forEach((inp, idx)=>{
                        inp.placeholder = `{{ __('messages.option_placeholder') }} ${idx+1}`;
                    });
                }
            });
            if (isPrivate) isPrivate.addEventListener('change', function(){
                accessWrap.classList.toggle('hidden', !this.checked);
            });
            
            // Xử lý thay đổi loại poll
            if (pollType) pollType.addEventListener('change', function() {
                if (this.value === 'ranking') {
                    regularOptions.classList.add('hidden');
                    rankingInfo.classList.remove('hidden');
                } else {
                    regularOptions.classList.remove('hidden');
                    rankingInfo.classList.add('hidden');
                }
            });

            // Tự động mở lại modal nếu có lỗi validate từ server
            @if ($errors->any())
                open();
            @endif
        })();
    </script>
</x-app-layout>
