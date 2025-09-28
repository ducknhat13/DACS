<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-3xl text-gray-800 dark:text-gray-200 leading-tight">{{ __('messages.create_poll') }}</h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="card">
                @if ($errors->any())
                    <div class="bg-red-100 text-red-800 p-3 rounded mb-4">
                        <ul class="list-disc pl-5">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form id="poll-form" method="POST" action="{{ route('polls.store') }}" class="space-y-6">
                    @csrf
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                        <!-- Left column: Basic info -->
                        <div class="space-y-4 lg:border-r lg:border-gray-200 lg:dark:border-gray-700 lg:pr-8">
                            <div class="border-b border-gray-200 dark:border-gray-700 pb-3">
                                <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-1">{{ __('messages.basic_info') }}</h3>
                                <p class="text-sm text-gray-600 dark:text-gray-400">{{ __('messages.basic_info_desc') }}</p>
                            </div>
                        <div class="space-y-2">
                            <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300">{{ __('messages.question') }}</label>
                            <textarea name="question" class="w-full border border-gray-300 dark:border-gray-600 rounded-lg p-3 dark:bg-gray-800 dark:text-white focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all duration-200" rows="3" required placeholder="Enter your question...">{{ old('question') }}</textarea>
                        </div>

                        <div class="space-y-2">
                            <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300">{{ __('messages.poll_type') }}</label>
                            <select name="poll_type" id="poll_type" class="w-full border border-gray-300 dark:border-gray-600 rounded-lg p-3 dark:bg-gray-800 dark:text-white focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all duration-200" required>
                                <option value="regular">{{ __('messages.regular_poll') }}</option>
                                <option value="ranking">{{ __('messages.ranking_poll') }}</option>
                            </select>
                        </div>

                        <div class="space-y-2">
                            <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300">{{ __('messages.options') }}</label>
                            <div id="options" class="space-y-3 max-h-80 overflow-y-auto pr-1">
                                <div class="flex items-center gap-3 option-row">
                                    <input type="text" name="options[]" class="flex-1 border border-gray-300 dark:border-gray-600 rounded-lg p-3 dark:bg-gray-800 dark:text-white focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all duration-200" placeholder="{{ __('messages.option_placeholder') }} 1" required>
                                    <button type="button" class="btn btn-neutral removeOption px-3 py-2" aria-label="Remove option">✕</button>
                                </div>
                                <div class="flex items-center gap-3 option-row">
                                    <input type="text" name="options[]" class="flex-1 border border-gray-300 dark:border-gray-600 rounded-lg p-3 dark:bg-gray-800 dark:text-white focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all duration-200" placeholder="{{ __('messages.option_placeholder') }} 2" required>
                                    <button type="button" class="btn btn-neutral removeOption px-3 py-2" aria-label="Remove option">✕</button>
                                </div>
                            </div>
                            <div class="flex items-center justify-start mt-3 gap-3">
                                <button type="button" id="addOption" class="btn btn-neutral">{{ __('messages.add_option') }}</button>
                                <button type="button" id="addOther" class="text-xs text-indigo-600 hover:underline">add "Other"</button>
                            </div>
                        </div>

                        <div id="regular-options" class="p-4 rounded-lg bg-gray-50 dark:bg-gray-800/50 border border-gray-200 dark:border-gray-700">
                            <div class="flex items-center justify-between">
                                <span class="text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('messages.allow_multiple') }}</span>
                                <div class="toggle-switch">
                                    <input type="hidden" name="allow_multiple" id="allow_multiple" value="0">
                                    <button type="button" id="toggle_allow_multiple" class="text-2xl" aria-pressed="false" aria-label="Allow multiple"><i class="fa-solid fa-toggle-off"></i></button>
                                </div>
                            </div>
                        </div>

                            <div id="ranking-info" class="hidden bg-blue-50 dark:bg-blue-900/20 p-3 rounded-lg border border-blue-200 dark:border-blue-800">
                                <div class="flex items-start gap-2">
                                    <i class="fa-solid fa-info-circle text-blue-600 dark:text-blue-400 mt-0.5"></i>
                                    <p class="text-sm text-blue-800 dark:text-blue-200">{{ __('messages.ranking_info') }}</p>
                                </div>
                            </div>
                        </div>

                        <!-- Right column: Settings + Live preview -->
                        <div class="space-y-4 lg:pl-8">
                            <div class="border-b border-gray-200 dark:border-gray-700 pb-3">
                                <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-1">{{ __('messages.settings') }}</h3>
                                <p class="text-sm text-gray-600 dark:text-gray-400">{{ __('messages.settings_desc') }}</p>
                            </div>
                        
                        <!-- Voting security -->
                        <div class="space-y-2">
                            <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300">{{ __('messages.voting_security') }}</label>
                            <select name="voting_security" id="voting_security" class="w-full border border-gray-300 dark:border-gray-600 rounded-lg p-3 dark:bg-gray-800 dark:text-white focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all duration-200">
                                <option value="session">{{ __('messages.one_vote_per_session') }}</option>
                                <option value="private">{{ __('messages.private_with_key') }}</option>
                            </select>
                        </div>

                        

                        <div id="access-key-field" class="hidden space-y-2">
                            <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300">{{ __('messages.access_key') }}</label>
                            <input type="text" name="access_key" class="w-full border border-gray-300 dark:border-gray-600 rounded-lg p-3 dark:bg-gray-800 dark:text-white focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all duration-200" placeholder="{{ __('messages.access_key_placeholder') }}">
                        </div>

                        <!-- Advanced settings -->
                        <div class="space-y-3">
                            <div class="border-t border-gray-200 dark:border-gray-700 pt-3">
                                <button type="button" id="toggle-advanced" class="w-full flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-800/50 rounded-lg border border-gray-200 dark:border-gray-700 hover:bg-gray-100 dark:hover:bg-gray-700/50 transition-colors">
                                    <div class="flex items-center gap-2">
                                        <i class="fa-solid fa-cog text-indigo-600"></i>
                                        <span class="text-sm font-semibold text-gray-700 dark:text-gray-300">{{ __('messages.advanced_settings') }}</span>
                                    </div>
                                    <i class="fa-solid fa-chevron-down text-gray-500 transition-transform" id="advanced-chevron"></i>
                                </button>
                                <div id="advanced-content" class="hidden space-y-2 mt-3">
                                    <!-- Auto close toggle -->
                                    <div class="flex items-center justify-between py-2">
                                        <span class="text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('messages.auto_close_poll') }}</span>
                                        <button type="button" id="toggle_auto_close" class="text-2xl" aria-pressed="false" aria-label="Auto close"><i class="fa-solid fa-toggle-off"></i></button>
                                        <input type="hidden" name="auto_close_enabled" id="auto_close_enabled" value="0">
                                    </div>
                                    <div id="auto_close_time" class="hidden mt-2">
                                        <input type="datetime-local" name="auto_close_at" class="w-full border border-gray-300 dark:border-gray-600 rounded-lg p-3 dark:bg-gray-800 dark:text-white focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all duration-200">
                                    </div>

                                    <!-- Allow comments toggle -->
                                    <div class="flex items-center justify-between py-2">
                                        <span class="text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('messages.allow_comments') }}</span>
                                        <button type="button" id="toggle_allow_comments" class="text-2xl" aria-pressed="false" aria-label="Allow comments"><i class="fa-solid fa-toggle-off"></i></button>
                                        <input type="hidden" name="allow_comments" id="allow_comments" value="0">
                                    </div>

                                    <!-- Hide share toggle -->
                                    <div class="flex items-center justify-between py-2">
                                        <span class="text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('messages.hide_share_button') }}</span>
                                        <button type="button" id="toggle_hide_share" class="text-2xl" aria-pressed="false" aria-label="Hide share"><i class="fa-solid fa-toggle-off"></i></button>
                                        <input type="hidden" name="hide_share" id="hide_share" value="0">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            
            <!-- Action buttons outside cards -->
            <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 mt-6">
                <div class="flex justify-end gap-3">
                    <a href="{{ route('dashboard') }}" class="btn btn-neutral px-6 py-3">{{ __('messages.cancel') }}</a>
                    <button type="submit" form="poll-form" class="btn btn-primary px-6 py-3 flex items-center gap-2">
                        <i class="fa-solid fa-plus"></i>
                        {{ __('messages.create') }}
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Helper: renumber placeholders (skip the .other-row)
        function renumberOptions(){
            const container = document.getElementById('options');
            const rows = container.querySelectorAll('.option-row:not(.other-row) input[type="text"]');
            rows.forEach((inp, idx)=>{
                inp.placeholder = `{{ __('messages.option_placeholder') }} ${idx+1}`;
            });
        }

        // Thêm lựa chọn
        document.getElementById('addOption').addEventListener('click', function() {
            const container = document.getElementById('options');
            const count = container.querySelectorAll('.option-row:not(.other-row)').length;
            const row = document.createElement('div');
            row.className = 'flex items-center gap-2 option-row';
            row.innerHTML = `
                <input type="text" name="options[]" class="flex-1 border rounded-lg p-3 dark:bg-gray-900" placeholder="{{ __('messages.option_placeholder') }} ${count+1}" required>
                <button type="button" class="btn btn-neutral removeOption" aria-label="Remove option">✕</button>
            `;
            container.appendChild(row);
            pushOtherToBottom();
            renumberOptions();
        });

        // Ẩn/hiện nút add Other theo loại poll
        const addOtherBtn = document.getElementById('addOther');
        function refreshAddOtherVisibility(){
            const type = document.getElementById('poll_type').value;
            if (type === 'ranking') addOtherBtn.classList.add('hidden');
            else addOtherBtn.classList.remove('hidden');
        }
        document.getElementById('poll_type').addEventListener('change', refreshAddOtherVisibility);
        refreshAddOtherVisibility();

        // Thêm "Other" rỗng và luôn đẩy xuống cuối (chỉ với regular)
        addOtherBtn.addEventListener('click', function(){
            const container = document.getElementById('options');
            // Nếu đã tồn tại dòng other thì chỉ đẩy xuống cuối
            let otherRow = container.querySelector('.option-row.other-row');
            if (!otherRow) {
                otherRow = document.createElement('div');
                otherRow.className = 'flex items-center gap-2 option-row other-row';
                otherRow.innerHTML = `
                    <input type="text" name="options[]" value="other" class="flex-1 border rounded-lg p-3 dark:bg-gray-900 pointer-events-none opacity-70 select-none" placeholder="Other" readonly>
                    <button type="button" class="btn btn-neutral removeOption" aria-label="Remove option">✕</button>
                `;
                container.appendChild(otherRow);
            }
            pushOtherToBottom();
        });

        // Xóa lựa chọn (event delegation)
        document.getElementById('options').addEventListener('click', function(e){
            const btn = e.target.closest('.removeOption');
            if (!btn) return;
            const row = btn.closest('.option-row');
            if (row) {
                row.remove();
                // Cập nhật placeholder số thứ tự (bỏ qua other)
                renumberOptions();
            }
            pushOtherToBottom();
        });

        function pushOtherToBottom(){
            const container = document.getElementById('options');
            const other = container.querySelector('.option-row.other-row');
            if (other) container.appendChild(other);
            renumberOptions();
        }

        // Keyboard shortcuts
        document.getElementById('options').addEventListener('keydown', function(e){
            const isCtrlBackspace = (e.ctrlKey || e.metaKey) && (e.key === 'Backspace');
            if (e.key === 'Enter') {
                e.preventDefault();
                document.getElementById('addOption').click();
            } else if (isCtrlBackspace) {
                const row = e.target.closest('.option-row');
                if (row && !row.classList.contains('other-row')) {
                    row.querySelector('.removeOption')?.click();
                }
            }
        });


        // Xử lý thay đổi loại poll
        document.getElementById('poll_type').addEventListener('change', function() {
            const regularOptions = document.getElementById('regular-options');
            const rankingInfo = document.getElementById('ranking-info');
            if (this.value === 'ranking') {
                regularOptions.classList.add('hidden');
                rankingInfo.classList.remove('hidden');
            } else {
                regularOptions.classList.remove('hidden');
                rankingInfo.classList.add('hidden');
            }
        });

        // Voting security: hiển thị access key khi chọn private
        document.getElementById('voting_security').addEventListener('change', function(){
            const accessKeyField = document.getElementById('access-key-field');
            accessKeyField.classList.toggle('hidden', this.value !== 'private');
        });


        // Toggle helpers (unified UI)
        function attachToggle(buttonId, hiddenInputId, extraAction = null){
            const btn = document.getElementById(buttonId);
            const inp = document.getElementById(hiddenInputId);
            if (!btn || !inp) return;
            btn.addEventListener('click', function(){
                const pressed = this.getAttribute('aria-pressed') === 'true';
                this.setAttribute('aria-pressed', String(!pressed));
                const icon = this.querySelector('i');
                if (icon) {
                    icon.classList.toggle('fa-toggle-on', !pressed);
                    icon.classList.toggle('fa-toggle-off', pressed);
                    icon.classList.toggle('text-indigo-600', !pressed);
                }
                inp.value = pressed ? '0' : '1';
                
                // Execute extra action if provided (for auto_close datetime picker)
                if (extraAction) {
                    extraAction(!pressed);
                }
            });
        }
        // Advanced settings toggle
        document.getElementById('toggle-advanced').addEventListener('click', function() {
            const content = document.getElementById('advanced-content');
            const chevron = document.getElementById('advanced-chevron');
            
            content.classList.toggle('hidden');
            chevron.classList.toggle('rotate-180');
        });

        attachToggle('toggle_auto_close', 'auto_close_enabled', function(isOn) {
            document.getElementById('auto_close_time').classList.toggle('hidden', !isOn);
        });
        attachToggle('toggle_allow_multiple', 'allow_multiple');
        attachToggle('toggle_allow_comments', 'allow_comments');
        attachToggle('toggle_hide_share', 'hide_share');
    </script>
    </x-app-layout>
