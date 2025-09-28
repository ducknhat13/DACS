<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-3xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="pt-4 pb-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="card">
                <div class="text-gray-900 dark:text-gray-100">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-semibold">{{ __('messages.your_polls') }}</h3>
                        <a href="{{ route('polls.create') }}" class="btn btn-primary">{{ __('messages.create_poll') }}</a>
                    </div>

                    <!-- Smart search bar -->
                    <form method="GET" class="mb-5">
                        <div class="w-full rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 shadow-sm p-2 sm:p-3 flex flex-wrap items-center gap-2">
                            <div class="flex-1 min-w-64">
                                <input type="text" name="q" value="{{ request('q') }}" placeholder="{{ __('messages.search_placeholder') }} / slug" class="w-full h-10 px-3 rounded-lg bg-gray-50 dark:bg-gray-700 border border-transparent focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500 dark:text-white">
                            </div>
                            <div class="flex items-center gap-2 ml-auto">
                                <div>
                                    <select name="status" class="h-10 px-3 rounded-lg bg-gray-50 dark:bg-gray-700 border border-transparent focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500 dark:text-white">
                                        @php $st = request('status','all'); @endphp
                                        <option value="all" {{ $st==='all'?'selected':'' }}>All</option>
                                        <option value="open" {{ $st==='open'?'selected':'' }}>Open</option>
                                        <option value="closed" {{ $st==='closed'?'selected':'' }}>Closed</option>
                                    </select>
                                </div>
                                <div>
                                    <select name="sort" class="h-10 px-3 rounded-lg bg-gray-50 dark:bg-gray-700 border border-transparent focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500 dark:text-white">
                                        @php $so = request('sort','newest'); @endphp
                                        <option value="newest" {{ $so==='newest'?'selected':'' }}>Newest</option>
                                        <option value="votes" {{ $so==='votes'?'selected':'' }}>Most votes</option>
                                    </select>
                                </div>
                                <button class="btn btn-neutral" aria-label="{{ __('messages.search') }}">
                                    {{ __('messages.search') }}
                                </button>
                                <button type="button" class="h-10 w-10 inline-flex items-center justify-center rounded-lg border border-gray-300 dark:border-gray-600 hover:bg-gray-50 dark:hover:bg-gray-700 text-gray-600 dark:text-gray-200" title="Reset filters" aria-label="Reset filters" onclick="window.location='{{ route('dashboard') }}'">
                                    <i class="fa-solid fa-rotate-right" aria-hidden="true"></i>
                                </button>
                            </div>
                        </div>
                    </form>

                    @php
                        $polls = $polls ?? [];
                    @endphp

                    @if (empty($polls) || count($polls) === 0)
                        <p class="text-sm text-gray-500">{{ __('messages.no_polls') }}</p>
                    @else
                        <div class="mb-2 px-3 sm:px-4 text-xs text-gray-500 dashboard-grid">
                            <div class="font-medium whitespace-nowrap text-left min-w-0">Polls</div>
                            <div class="text-center font-medium whitespace-nowrap">Participants</div>
                            <div class="text-center font-medium whitespace-nowrap">Deadline</div>
                            <div class="text-right font-medium whitespace-nowrap">Status</div>
                            <div></div>
                        </div>
                        <div class="space-y-2">
                            @foreach ($polls as $p)
                                @php
                                    $total = $p->votes_count ?? 0;
                                    $created = $p->created_at ? \Carbon\Carbon::parse($p->created_at)->format('M d, Y') : '';
                                    $deadline = $p->auto_close_at ? \Carbon\Carbon::parse($p->auto_close_at)->format('M d, Y') : '-';
                                    $isClosed = isset($p->is_closed) ? (bool)$p->is_closed : false;
                                @endphp
                                <div class="group bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl px-3 sm:px-4 py-2 hover:shadow-sm transition overflow-visible">
                                    <div class="relative dashboard-grid">
                                        <div class="flex items-center gap-3 pr-3 min-w-0">
                                            <div class="w-7 h-7 rounded-full bg-green-100 text-green-700 flex items-center justify-center"><i class="fa-solid fa-calendar" aria-hidden="true"></i></div>
                                            <div class="min-w-0">
                                                <a href="{{ route('polls.vote', $p->slug) }}" class="font-medium hover:text-indigo-600 truncate block">{{ $p->question }}</a>
                                                <div class="text-xs text-gray-500 whitespace-nowrap">{{ $created }}</div>
                                            </div>
                                        </div>
                                        <div class="text-sm text-gray-700 dark:text-gray-300 text-center whitespace-nowrap">{{ $total }}</div>
                                        <div class="text-sm text-gray-700 dark:text-gray-300 text-center whitespace-nowrap">{{ $deadline }}</div>
                                        <div class="flex justify-end items-center gap-2 whitespace-nowrap">
                                            @if($isClosed)
                                                <span class="inline-flex items-center gap-2 px-2.5 py-1 rounded-full bg-gray-100 text-gray-700 text-xs">● Closed</span>
                                            @else
                                                <span class="inline-flex items-center gap-2 px-2.5 py-1 rounded-full bg-green-100 text-green-700 text-xs">● Live</span>
                                            @endif
                                        </div>
                                        <button type="button" class="w-8 h-8 inline-flex items-center justify-center rounded-md hover:bg-gray-100 dark:hover:bg-gray-700 justify-self-end" aria-haspopup="menu" aria-expanded="false" onclick="toggleActionMenu('menu{{ $p->id }}')">
                                            <i class="fa-solid fa-ellipsis" aria-hidden="true"></i>
                                        </button>
                                        <div id="menu{{ $p->id }}" class="hidden absolute right-0 top-9 z-50 w-40 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-md shadow-lg p-1">
                                                <form method="POST" action="{{ route('polls.toggle', $p->slug) }}" class="block">
                                                    @csrf
                                                    <button type="submit" class="w-full text-left px-3 py-2 text-sm hover:bg-gray-100 dark:hover:bg-gray-700 rounded">{{ $isClosed ? __('messages.reopen') : __('messages.close_poll') }}</button>
                                                </form>
                                                <a href="{{ route('polls.export', $p->slug) }}" class="block px-3 py-2 text-sm hover:bg-gray-100 dark:hover:bg-gray-700 rounded">{{ __('messages.export_csv') }}</a>
                                                <form method="POST" action="{{ route('polls.destroy', $p->slug) }}" onsubmit="return confirm('{{ __('messages.delete_confirm') }}');" class="block">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="w-full text-left px-3 py-2 text-sm text-red-600 hover:bg-red-50 dark:hover:bg-red-900/30 rounded">{{ __('messages.delete') }}</button>
                                                </form>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

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
        function toggleRow(id) {
            const element = document.getElementById(id);
            const arrow = document.getElementById('arrow' + id.replace('pollRow', ''));
            
            if (element.classList.contains('hidden')) {
                element.classList.remove('hidden');
                if (arrow) arrow.style.transform = 'rotate(180deg)';
            } else {
                element.classList.add('hidden');
                if (arrow) arrow.style.transform = 'rotate(0deg)';
            }
        }

        function toggleActionMenu(id) {
            document.querySelectorAll('[id^="menu"]').forEach(function(el){ if (el.id !== id) el.classList.add('hidden'); });
            const el = document.getElementById(id);
            if (!el) return;
            el.classList.toggle('hidden');
        }

        // Đóng menu khi click ra ngoài
        document.addEventListener('click', function(e){
            const target = e.target;
            const isButton = target.closest && target.closest('[aria-haspopup="menu"]');
            const isMenu = target.closest && target.closest('[id^="menu"]');
            if (!isButton && !isMenu) {
                document.querySelectorAll('[id^="menu"]').forEach(function(el){ el.classList.add('hidden'); });
            }
        }, true);

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
