<!--
    Dashboard Page
    - Hiển thị danh sách poll của người dùng, bộ lọc, tìm kiếm, bulk actions.
    - Có modal xác nhận xóa hàng loạt và modal xác nhận xóa đơn lẻ (singleDeleteModal).
    - Logic hiển thị trạng thái poll: dựa trên is_closed và auto_close_at.
    - JS: openDeleteModal(...) thay thế confirm() native cho xóa đơn lẻ.
-->
{{--
    Dashboard Page - dashboard.blade.php
    
    Trang Dashboard hiển thị tất cả polls của user với Material Design 3 UI.
    
    Features:
    - Poll list với grid layout (responsive: 1-3 columns)
    - Filter & Search: Tìm kiếm theo tên/slug, lọc theo status (all/open/closed), sort (newest/votes)
    - Bulk Actions: Chọn nhiều polls để đóng/mở/xóa/export cùng lúc
    - Poll Cards: Hiển thị poll info với action menu
    - Empty State: Hiển thị khi chưa có polls
    
    JavaScript Functionality:
    - Bulk selection: Checkbox selection với bulk action bar
    - Filter chips: Dynamic filter với Material Design chips
    - Poll actions: Toggle, export, delete từ action menu
    - Poll card interactions: Hover effects, click to view
    
    Data từ Controller:
    - $polls: Collection of Poll models với relationships (options, votes)
    - Filters: request('q'), request('status'), request('sort')
    
    @author QuickPoll Team
--}}
<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-headline-large text-on-surface font-semibold">{{ __('messages.dashboard') }}</h1>
                <p class="text-body-medium text-on-surface-variant mt-1">{{ __('messages.create_poll_help') }}</p>
            </div>
            <div class="flex items-center gap-3">
                {{-- Nút làm mới danh sách polls --}}
                <button class="btn btn-neutral" data-tooltip="{{ __('messages.refresh') }}">
                    <i class="fa-solid fa-refresh"></i>
                </button>
                {{-- Link tới trang thống kê/lịch sử --}}
                <a href="{{ route('stats.index') }}" class="material-nav-link">
                    <i class="fa-solid fa-chart-line"></i>
                    {{ __('messages.history') }}
                </a>
                {{-- Nút tạo poll mới (đi tới form tạo) --}}
                <a href="{{ route('polls.create') }}" class="btn btn-primary">
                    <i class="fa-solid fa-plus"></i>
                    {{ __('messages.create_poll') }}
                </a>
            </div>
        </div>
    </x-slot>

    <div class="pt-4 pb-12 page-transition">
        {{-- Bulk Action Bar: Hiển thị khi có polls được chọn --}}
        <div id="bulkBar" class="bulk-bar">
            <div class="bulk-left">
                <button id="bulkExit" class="bulk-exit tooltip" aria-label="{{ __('messages.exit_selection') }}" data-tooltip="{{ __('messages.exit_bulk_mode') }}">
                    <span class="material-symbols-rounded">close</span>
                </button>
                <div id="bulkCount" class="bulk-count">0 {{ __('messages.selected') }}</div>
            </div>
            <div class="bulk-actions">
                <button id="bulkClose" class="bulk-btn tooltip" data-tooltip="{{ __('messages.close_selected_polls') }}" disabled>
                    <span class="material-symbols-rounded">lock</span>
                </button>
                <button id="bulkReopen" class="bulk-btn tooltip" data-tooltip="{{ __('messages.reopen_selected_polls') }}" disabled>
                    <span class="material-symbols-rounded">lock_open</span>
                </button>
                <button id="bulkExport" class="bulk-btn tooltip" data-tooltip="{{ __('messages.export_to_csv') }}" disabled>
                    <span class="material-symbols-rounded">file_download</span>
                </button>
                <button id="bulkDelete" class="bulk-btn danger tooltip" data-tooltip="{{ __('messages.delete_selected_polls') }}" disabled>
                    <span class="material-symbols-rounded">delete</span>
                </button>
            </div>
        </div>
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="card card-elevated animate-fade-in-up">
                <div class="text-gray-900 dark:text-gray-100">
                    {{--
                        Filter & Search Bar
                        - Input tìm kiếm: name="q"; giữ giá trị với request('q')
                        - Chips Status: all/open/closed → set input hidden #status-input
                        - Chips Sort: newest/votes → set input hidden #sort-input
                        - JS: on click chip -> toggle lớp 'active' + submit form
                    --}}
                    <form method="GET" class="mb-6">
                        <div class="bg-surface-variant rounded-2xl p-4 mb-6">
                            <div class="flex flex-col lg:flex-row gap-4">
                                {{-- Ô tìm kiếm theo tên poll hoặc slug --}}
                                <div class="input-field flex-1">
                                    <input type="text" name="q" value="{{ request('q') }}" placeholder=" " class="bg-surface">
                                    <label>{{ __('messages.search_placeholder') }} / {{ __('messages.slug') }}</label>
                                </div>
                                
                                {{-- Các chip lọc: trạng thái và sắp xếp --}}
                                <div class="flex flex-wrap gap-2 items-center">
                                    @php $st = request('status','all'); @endphp
                                    {{-- Chip trạng thái: tất cả --}}
                                    <button type="button" class="filter-chip {{ $st === 'all' ? 'active' : '' }}" data-status="all">
                                        <i class="fa-solid fa-list"></i>
                                        {{ __('messages.all') }}
                                    </button>
                                    {{-- Chip trạng thái: đang mở --}}
                                    <button type="button" class="filter-chip {{ $st === 'open' ? 'active' : '' }}" data-status="open">
                                        <i class="fa-solid fa-play-circle"></i>
                                        {{ __('messages.active') }}
                                    </button>
                                    {{-- Chip trạng thái: đã đóng --}}
                                    <button type="button" class="filter-chip {{ $st === 'closed' ? 'active' : '' }}" data-status="closed">
                                        <i class="fa-solid fa-stop-circle"></i>
                                        {{ __('messages.closed') }}
                                    </button>
                                    
                                    {{-- Chip sắp xếp: mới nhất --}}
                                    @php $so = request('sort','newest'); @endphp
                                    <button type="button" class="filter-chip {{ $so === 'newest' ? 'active' : '' }}" data-sort="newest">
                                        <i class="fa-solid fa-clock"></i>
                                        {{ __('messages.newest') }}
                                    </button>
                                    {{-- Chip sắp xếp: nhiều vote nhất --}}
                                    <button type="button" class="filter-chip {{ $so === 'votes' ? 'active' : '' }}" data-sort="votes">
                                        <i class="fa-solid fa-chart-line"></i>
                                        {{ __('messages.most_votes') }}
                                    </button>
                                </div>
                                
                                {{-- Hidden inputs: lưu giá trị filter khi submit form --}}
                                <input type="hidden" name="status" id="status-input" value="{{ $st }}">
                                <input type="hidden" name="sort" id="sort-input" value="{{ $so }}">
                            </div>
                        </div>
                    </form>

                    @php
                        $polls = $polls ?? [];
                    @endphp

                    {{--
                        Empty State
                        - Hiển thị khi không có polls
                        - CTA: nút tạo poll mới (route('polls.create'))
                    --}}
                    @if (empty($polls) || count($polls) === 0)
                        <div class="text-center py-12">
                            <div class="w-16 h-16 mx-auto mb-4 rounded-full bg-surface-variant flex items-center justify-center">
                                <i class="fa-solid fa-poll text-xl text-on-surface-variant"></i>
                            </div>
                            <h3 class="text-title-medium text-on-surface mb-2">{{ __('messages.no_polls') }}</h3>
                            <p class="text-body-medium text-on-surface-variant mb-6">{{ __('messages.get_started_hint') }}</p>
                            <a href="{{ route('polls.create') }}" class="btn btn-primary">
                                <i class="fa-solid fa-plus"></i>
                                {{ __('messages.create_poll') }}
                            </a>
                        </div>
                    @else
                        {{--
                            Polls Grid
                            - Responsive grid 1-3 cột
                            - Mỗi thẻ .poll-card chứa:
                              + Menu hành động (toggle/reopen, export csv, delete)
                              + Tiêu đề (link sang trang vote)
                              + Meta (participants, deadline, created)
                              + Trạng thái (active/closed)
                              + Checkbox ẩn cho bulk select (.bulk-check)
                            - Data attributes:
                              + data-slug: slug poll (dùng cho bulk và delete)
                              + data-closed: '1' nếu closed, '0' nếu active
                        --}}
                        <div class="poll-grid grid grid-cols-1 lg:grid-cols-2 xl:grid-cols-3 gap-4">
                            @foreach ($polls as $index => $p)
                                @php
                                    // Use participants_count for accurate participant count (handles ranking polls correctly)
                                    $total = $p->participants_count ?? $p->votes_count ?? 0;
                                    $created = $p->created_at ? \Carbon\Carbon::parse($p->created_at)->format('M d, Y') : '';
                                    $deadline = $p->auto_close_at ? \Carbon\Carbon::parse($p->auto_close_at)->format('M d, Y') : null;
                                    // Trạng thái đóng hiệu lực: cờ is_closed hoặc đã quá hạn auto_close_at
                                    $isClosed = (isset($p->is_closed) ? (bool)$p->is_closed : false) || ($p->auto_close_at && now()->greaterThanOrEqualTo($p->auto_close_at));
                                    $isExpired = $deadline && \Carbon\Carbon::parse($deadline)->isPast();
                                @endphp
                                <div class="poll-card animate-fade-in-up stagger-item" style="animation-delay: {{ $index * 0.1 }}s;" data-slug="{{ $p->slug }}" data-closed="{{ $isClosed ? '1' : '0' }}">
                                    <input type="checkbox" class="bulk-check hidden" value="{{ $p->slug }}">
                                    <!-- Nút mở menu hành động cho từng poll -->
                                    <div class="poll-actions">
                                        {{--
                                            Action Menu
                                            - Button ba chấm mở/đóng menu (#menu{{ $p->id }})
                                            - Toggle/Reopen: POST route('polls.toggle', slug)
                                            - Export CSV: GET route('polls.export', slug)
                                            - Delete: mở modal confirm đơn lẻ (singleDeleteModal)
                                        --}}
                                        <button type="button" class="action-menu-button" onclick="toggleActionMenu('menu{{ $p->id }}')">
                                            <i class="fa-solid fa-ellipsis-vertical"></i>
                                        </button>
                                        <div id="menu{{ $p->id }}" class="action-menu">
                                            {{-- Form toggle: đóng/mở poll --}}
                                            <form method="POST" action="{{ route('polls.toggle', $p->slug) }}">
                                                @csrf
                                                <button type="submit" class="action-menu-item">
                                                    <i class="fa-solid {{ $isClosed ? 'fa-play' : 'fa-stop' }}"></i>
                                                    {{ $isClosed ? __('messages.reopen') : __('messages.close_poll') }}
                                                </button>
                                            </form>
                                            {{-- Link export CSV kết quả poll --}}
                                            <a href="{{ route('polls.export', $p->slug) }}" class="action-menu-item">
                                                <i class="fa-solid fa-download"></i>
                                                {{ __('messages.export_csv') }}
                                            </a>
                                            {{-- Form ẩn xoá poll (được submit khi xác nhận trong modal) --}}
                                            <form id="deleteForm{{ $p->id }}" method="POST" action="{{ route('polls.destroy', $p->slug) }}" style="display:none;">
                                                @csrf
                                                @method('DELETE')
                                            </form>
                                            {{-- Nút mở modal xác nhận xoá poll đơn lẻ --}}
                                            <button type="button" class="action-menu-item danger" onclick="openDeleteModal('{{ $p->slug }}', '{{ $p->id }}', '{{ addslashes($p->title ?? $p->question) }}')">
                                                <i class="fa-solid fa-trash"></i>
                                                {{ __('messages.delete') }}
                                            </button>
                                        </div>
                                    </div>

                                    <!-- Tiêu đề Poll: link tới trang bình chọn (vote) -->
                                    <a href="{{ route('polls.vote', $p->slug) }}" class="poll-title block bulk-link poll-title-truncate" title="{{ $p->title ?? $p->question }}">
                                        {{ $p->title ?? $p->question }}
                                    </a>

                                    <!-- Meta Poll: số người tham gia, hạn kết thúc, ngày tạo -->
                                    <div class="poll-meta">
                                        <div class="poll-meta-item">
                                            <i class="fa-solid fa-users"></i>
                                            <span>{{ $total }} {{ __('messages.participants') }}</span>
                                        </div>
                                        @if($deadline)
                                            <div class="poll-meta-item {{ $isExpired ? 'text-error' : '' }}">
                                                <i class="fa-solid fa-clock"></i>
                                                <span>{{ $isExpired ? __('messages.expired') : __('messages.ends') }}: {{ $deadline }}</span>
                                            </div>
                                        @endif
                                        <div class="poll-meta-item">
                                            <i class="fa-solid fa-calendar"></i>
                                            <span>{{ __('messages.created') }}: {{ $created }}</span>
                                        </div>
                                    </div>

                                    <!-- Footer Poll: badge trạng thái và link xem kết quả -->
                                    <div class="poll-footer">
                                        <span class="poll-status {{ $isClosed ? 'closed' : 'active' }}">
                                            <i class="fa-solid {{ $isClosed ? 'fa-stop-circle' : 'fa-play-circle' }}"></i>
                                            {{ $isClosed ? __('messages.closed') : __('messages.active') }}
                                        </span>
                                        
                                        {{-- Link tới trang kết quả của poll --}}
                                        <a href="{{ route('polls.show', $p->slug) }}" class="text-primary text-sm font-medium hover:underline bulk-link">
                                            {{ __('messages.view_results') }} →
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

    <!-- Extended FAB with menu: menu nổi truy cập nhanh các hành động -->
    <div id="fabMenu" class="fab-menu">
        <button id="fabMain" class="md-fab ripple tooltip" aria-label="{{ __('messages.open_actions') }}" data-tooltip="{{ __('messages.actions') }}">
            <span class="material-symbols-rounded">more_vert</span>
        </button>
        <button id="fabCreate" class="fab-mini secondary tooltip tooltip-left" data-tooltip="{{ __('messages.create_new_poll') }}" aria-label="{{ __('messages.create_new_poll') }}">
            <span class="material-symbols-rounded">add</span>
        </button>
        <button id="fabBulk" class="fab-mini tooltip tooltip-left" data-tooltip="{{ __('messages.bulk_mode') }}" aria-label="{{ __('messages.bulk_mode') }}">
            <span class="material-symbols-rounded">select_all</span>
        </button>
    </div>

    <!-- Create Poll Modal: modal tạo poll nhanh (form POST polls.store) -->
    <div id="createPollModal" class="hidden">
        <div>
            <div class="flex items-start justify-between mb-4">
                <div>
                    <h4 class="text-xl font-semibold">{{ __('messages.new_poll') }}</h4>
                </div>
                <button type="button" id="closeCreatePoll" class="btn btn-neutral">✕</button>
            </div>

            <form method="POST" action="{{ route('polls.store') }}" class="space-y-4">
                {{--
                    Form fields:
                    - question (textarea, required)
                    - poll_type (regular|ranking): toggle hiển thị regular options hoặc ranking info
                    - options[]: tối đa 10, có nút add/remove; validate server sẽ mở lại modal
                    - is_private + access_key: ẩn/hiện theo checkbox
                --}}
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
                    <textarea name="question" rows="3" class="w-full border rounded-lg p-3 dark:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500" placeholder="{{ __('messages.question_example') }}" required></textarea>
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
                            <button type="button" class="btn btn-neutral removeOption" aria-label="{{ __('messages.remove_option') }}">✕</button>
                        </div>
                        <div class="flex items-center gap-2 option-row">
                            <input type="text" name="options[]" class="flex-1 border rounded-lg p-3 dark:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500" placeholder="{{ __('messages.option_placeholder') }} 2" required>
                            <button type="button" class="btn btn-neutral removeOption" aria-label="{{ __('messages.remove_option') }}">✕</button>
                        </div>
                    </div>
                    <div class="flex items-center justify-between mt-2">
                        <button type="button" id="addModalOption" class="btn btn-neutral">{{ __('messages.add_option') }}</button>
                        <span class="text-xs text-[color:var(--on-surface-variant)]">{{ __('messages.max_options') }}</span>
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
                    <input type="text" name="access_key" class="w-full border rounded-lg p-3 dark:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500" placeholder="{{ __('messages.access_key_placeholder_max') }}">
                </div>
                </div>
                <div class="flex justify-end gap-2 sticky bottom-0 bg-[var(--surface)] border-t border-[color:var(--outline)] pt-4">
                    <button type="button" id="cancelCreatePoll" class="btn btn-neutral">{{ __('messages.cancel') }}</button>
                    <button type="submit" class="btn btn-primary">{{ __('messages.create') }}</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        // =============================
        // Filter/Search & Action Menus
        // =============================
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

        // Add ripple effect to cards (hiệu ứng click trên poll card, tránh ảnh hưởng menu)
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

        // =============================
        // Create Poll Modal Handlers
        // =============================
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
                    <button type="button" class="btn btn-neutral removeOption" aria-label="{{ __('messages.remove_option') }}">✕</button>
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

    <!-- Bulk Delete Confirm Modal -->
    <div id="bulkDeleteModal" class="hidden fixed inset-0 z-[100] flex items-center justify-center p-4 bg-black/50 backdrop-blur-sm" style="backdrop-filter: blur(4px);">
        <div class="card" style="max-width:520px;width:100%;">
            <div class="material-alert error">
                <div class="alert-content">
                    <i class="fa-solid fa-triangle-exclamation"></i>
                    <div class="alert-text">
                        <div class="alert-title">{{ __('messages.confirm_delete') }}</div>
                        <div class="alert-description">{{ __('messages.bulk_delete_confirmation') }}</div>
                    </div>
                </div>
            </div>
            <div class="flex justify-end gap-2">
                <button id="bulkDeleteCancel" class="btn btn-neutral">{{ __('messages.cancel') }}</button>
                <button id="bulkDeleteConfirm" class="btn btn-primary">{{ __('messages.delete') }}</button>
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
    
    // =============================
    // Single Delete Modal & Bulk Actions
    // =============================
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
    
    // Bulk selection & actions
    document.addEventListener('DOMContentLoaded', function(){
        // Single delete modal handlers
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
        // Bulk selection elements
        const checks = document.querySelectorAll('.bulk-check');
        const bulkBar = document.getElementById('bulkBar');
        const bulkCount = document.getElementById('bulkCount');
        const bulkExit = document.getElementById('bulkExit');
        const bulkClose = document.getElementById('bulkClose');
        const bulkReopen = document.getElementById('bulkReopen');
        const bulkExport = document.getElementById('bulkExport');
        const bulkDelete = document.getElementById('bulkDelete');
        const deleteModal = document.getElementById('bulkDeleteModal');
        const deleteCancel = document.getElementById('bulkDeleteCancel');
        const deleteConfirm = document.getElementById('bulkDeleteConfirm');

        const csrf = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        const selected = new Set();
        const selectedText = @json(__('messages.selected'));

        const fabMenu = document.getElementById('fabMenu');
        const fabMain = document.getElementById('fabMain');
        const fabBulk = document.getElementById('fabBulk');
        const fabCreateBtn = document.getElementById('fabCreate');

        // Bật/tắt selection mode (hiển thị thanh bulkBar, ẩn FAB)
        function setSelectionMode(enabled){
            document.body.classList.toggle('selection-mode', enabled);
            if (enabled) { bulkBar.classList.add('show'); fabMenu.style.display = 'none'; }
            else { bulkBar.classList.remove('show'); fabMenu.style.display = ''; }
        }

        // Cập nhật UI theo số lượng item đã chọn
        function updateUI(){
            bulkCount.textContent = `${selected.size} ${selectedText}`;
            setSelectionMode(selected.size > 0);
            
            // Enable/disable buttons based on selection
            const hasSelection = selected.size > 0;
            bulkClose.disabled = !hasSelection;
            bulkReopen.disabled = !hasSelection;
            bulkExport.disabled = !hasSelection;
            bulkDelete.disabled = !hasSelection;
        }

        // Bắt click trên poll-card khi đang ở selection-mode để toggle chọn
        document.addEventListener('click', function(e) {
            const pollCard = e.target.closest('.poll-card');
            if (!pollCard) return;
            
            // Check if we're in selection mode
            if (!document.body.classList.contains('selection-mode')) return;
            
            // Prevent default link behavior in bulk mode
            if (e.target.closest('.bulk-link')) {
                e.preventDefault();
                e.stopPropagation();
            }
            
            // Prevent action menu clicks
            if (e.target.closest('.poll-actions')) {
                e.stopPropagation();
                return;
            }
            
            // Toggle selection
            const checkbox = pollCard.querySelector('.bulk-check');
            const slug = pollCard?.dataset.slug;
            if (!slug || !checkbox) return;
            
            checkbox.checked = !checkbox.checked;
            
            if (checkbox.checked) { 
                selected.add(slug); 
                pollCard.classList.add('selected'); 
            } else { 
                selected.delete(slug); 
                pollCard.classList.remove('selected'); 
            }
            updateUI();
        });

        checks.forEach(ch => {
            ch.addEventListener('change', function(){
                const card = this.closest('.poll-card');
                const slug = card?.dataset.slug;
                if (!slug) return;
                if (this.checked) { selected.add(slug); card.classList.add('selected'); }
                else { selected.delete(slug); card.classList.remove('selected'); }
                updateUI();
            });
        });

        bulkExit?.addEventListener('click', function(){
            selected.clear();
            document.querySelectorAll('.bulk-check').forEach(c=>{ c.checked = false; });
            document.querySelectorAll('.poll-card.selected').forEach(el=>el.classList.remove('selected'));
            updateUI();
        });

        // FAB bulk toggles selection mode
        fabBulk?.addEventListener('click', function(){
            // Enter selection mode without selecting any item
            setSelectionMode(true);
        });

        // Toggle FAB menu open/close
        fabMain?.addEventListener('click', function(e){
            e.stopPropagation();
            fabMenu.classList.toggle('open');
        });

        // Click outside to close
        document.addEventListener('click', function(){ fabMenu.classList.remove('open'); });

        // Nút tạo poll nhanh từ FAB
        fabCreateBtn?.addEventListener('click', function(){
            window.location.href = '{{ route('polls.create') }}';
            fabMenu.classList.remove('open');
        });

        // Helpers gọi API POST/DELETE (dùng cho toggle/close/open/delete)
        async function post(url, body){
            return fetch(url, { method: 'POST', headers: { 'X-CSRF-TOKEN': csrf, 'Accept':'text/html' }, body });
        }
        async function del(url){
            return fetch(url, { method: 'POST', headers: { 'X-CSRF-TOKEN': csrf, 'Accept':'text/html' }, body: new URLSearchParams({ _method: 'DELETE' }) });
        }

        // Lấy danh sách thẻ poll đã chọn (phục vụ close/reopen/export/delete)
        function getSelectedCards(){
            const list = [];
            selected.forEach(slug => {
                const el = document.querySelector(`.poll-card[data-slug="${slug}"]`);
                if (el) list.push(el);
            });
            return list;
        }

        // Close selected polls: gọi /polls/{slug}/toggle với các poll đang active
        bulkClose?.addEventListener('click', function(){
            const items = getSelectedCards().filter(el => el.dataset.closed === '0');
            // Fire-and-forget requests, then reload shortly after
            items.forEach(el => { post(`/polls/${el.dataset.slug}/toggle`, new URLSearchParams()); });
            setTimeout(() => window.location.reload(), 500);
        });

        // Reopen selected polls: gọi /polls/{slug}/toggle với các poll đang closed
        bulkReopen?.addEventListener('click', function(){
            const items = getSelectedCards().filter(el => el.dataset.closed === '1');
            items.forEach(el => { post(`/polls/${el.dataset.slug}/toggle`, new URLSearchParams()); });
            setTimeout(() => window.location.reload(), 500);
        });

        // Export CSV các poll đã chọn: mở từng file tuần tự qua iframe ẩn để tránh chặn popup
        bulkExport?.addEventListener('click', function(){
            // Tải tuần tự để tránh bị chặn popup; dùng iframe ẩn mỗi 500ms
            const slugs = Array.from(selected);
            slugs.forEach((slug, idx) => {
                setTimeout(() => {
                    const iframe = document.createElement('iframe');
                    iframe.style.display = 'none';
                    iframe.src = `/polls/${slug}/export.csv`;
                    document.body.appendChild(iframe);
                    // Dọn dẹp sau 10s
                    setTimeout(() => { try { iframe.remove(); } catch(e){} }, 10000);
                }, idx * 500);
            });
        });

        // Hiện modal xác nhận xoá hàng loạt
        bulkDelete?.addEventListener('click', function(){
            deleteModal.classList.remove('hidden');
            deleteModal.classList.add('flex');
        });
        deleteCancel?.addEventListener('click', function(){ 
            deleteModal.classList.add('hidden'); 
            deleteModal.classList.remove('flex');
        });
        
        // Close modal when clicking outside
        deleteModal?.addEventListener('click', function(e){
            if (e.target === deleteModal) {
                deleteModal.classList.add('hidden');
                deleteModal.classList.remove('flex');
            }
        });
        // Xác nhận xoá: gửi DELETE tuần tự rồi reload
        deleteConfirm?.addEventListener('click', async function(){
            for (const slug of selected) { await del(`/polls/${slug}`); }
            window.location.reload();
        });
    });
    </script>
</x-app-layout>
