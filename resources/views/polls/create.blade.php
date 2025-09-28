<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-headline-large text-on-surface font-semibold">{{ __('messages.create_poll') }}</h1>
                <p class="text-body-medium text-on-surface-variant mt-1">Create engaging polls and gather feedback</p>
            </div>
            <div class="flex items-center gap-3">
                <a href="{{ route('dashboard') }}" class="btn btn-neutral">
                    <i class="fa-solid fa-arrow-left"></i>
                    Back to Dashboard
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-6 page-transition">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="card card-elevated animate-fade-in-up">
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
                        <div class="space-y-6 lg:border-r lg:border-outline lg:pr-8">
                            <div class="border-b border-outline pb-4">
                                <h3 class="text-headline-small text-on-surface font-semibold mb-2">{{ __('messages.basic_info') }}</h3>
                                <p class="text-body-medium text-on-surface-variant">{{ __('messages.basic_info_desc') }}</p>
                            </div>
                        <div class="input-field">
                            <textarea name="question" id="question" rows="3" required placeholder=" ">{{ old('question') }}</textarea>
                            <label for="question">{{ __('messages.question') }}</label>
                        </div>

                        <!-- Poll Type Segmented Buttons -->
                        <div class="space-y-3">
                            <label class="text-title-small text-on-surface font-medium">{{ __('messages.poll_type') }}</label>
                            <div class="segmented-control">
                                <input type="radio" name="poll_type" id="poll_type_regular" value="regular" checked class="segmented-input">
                                <label for="poll_type_regular" class="segmented-button">
                                    <i class="fa-solid fa-list-check"></i>
                                    Single Choice
                                </label>
                                
                                <input type="radio" name="poll_type" id="poll_type_ranking" value="ranking" class="segmented-input">
                                <label for="poll_type_ranking" class="segmented-button">
                                    <i class="fa-solid fa-sort-numeric-down"></i>
                                    Ranking
                                </label>
                            </div>
                        </div>

                        <div class="space-y-3">
                            <label class="text-title-small text-on-surface">{{ __('messages.options') }}</label>
                            <div id="options" class="space-y-3 max-h-80 overflow-y-auto pr-1">
                                <div class="flex items-center gap-3 option-row">
                                    <div class="input-field flex-1">
                                        <input type="text" name="options[]" placeholder=" " required>
                                        <label>{{ __('messages.option_placeholder') }} 1</label>
                                    </div>
                                    <button type="button" class="btn btn-neutral removeOption px-3 py-2" aria-label="Remove option">✕</button>
                                </div>
                                <div class="flex items-center gap-3 option-row">
                                    <div class="input-field flex-1">
                                        <input type="text" name="options[]" placeholder=" " required>
                                        <label>{{ __('messages.option_placeholder') }} 2</label>
                                    </div>
                                    <button type="button" class="btn btn-neutral removeOption px-3 py-2" aria-label="Remove option">✕</button>
                                </div>
                            </div>
                            <div class="flex items-center justify-start mt-4 gap-4">
                                <button type="button" id="addOption" class="text-button">
                                    <i class="fa-solid fa-plus"></i>
                                    Add Option
                                </button>
                                <button type="button" id="addOther" class="text-button">
                                    <i class="fa-solid fa-plus"></i>
                                    Add Other
                                </button>
                            </div>
                        </div>

                        <div id="regular-options" class="p-4 rounded-xl bg-surface-variant border border-outline">
                            <div class="flex items-center justify-between py-3">
                                <div class="flex-1">
                                    <span class="text-title-small font-medium text-on-surface">{{ __('messages.allow_multiple') }}</span>
                                    <p class="text-body-small text-on-surface-variant mt-1">Allow users to select multiple options</p>
                                </div>
                                <div class="material-switch">
                                    <input type="checkbox" id="allow_multiple" name="allow_multiple" value="1" class="switch-input">
                                    <label for="allow_multiple" class="switch-label">
                                        <span class="switch-slider"></span>
                                    </label>
                                </div>
                            </div>
                        </div>

                            <div id="ranking-info" class="hidden bg-surface-variant p-4 rounded-xl border border-outline mt-4">
                                <div class="flex items-start gap-3">
                                    <i class="fa-solid fa-info-circle text-primary mt-0.5"></i>
                                    <div>
                                        <p class="text-body-medium text-on-surface">{{ __('messages.ranking_info') }}</p>
                                        <p class="text-body-small text-on-surface-variant mt-1">Participants will rank all options from best to worst</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Right column: Settings + Live preview -->
                        <div class="space-y-6 lg:pl-8">
                            <div class="border-b border-outline pb-4">
                                <h3 class="text-headline-small text-on-surface font-semibold mb-2">{{ __('messages.settings') }}</h3>
                                <p class="text-body-medium text-on-surface-variant">{{ __('messages.settings_desc') }}</p>
                            </div>
                        
                        <!-- Voting security -->
                        <div class="input-field">
                            <select name="voting_security" id="voting_security" required>
                                <option value="" disabled selected></option>
                                <option value="session">{{ __('messages.one_vote_per_session') }}</option>
                                <option value="private">{{ __('messages.private_with_key') }}</option>
                            </select>
                            <label for="voting_security">{{ __('messages.voting_security') }}</label>
                        </div>

                        

                        <div id="access-key-field" class="input-field hidden">
                            <input type="text" name="access_key" id="access_key" placeholder=" ">
                            <label for="access_key">{{ __('messages.access_key') }}</label>
                        </div>

                        <!-- Advanced settings -->
                        <div class="space-y-4">
                            <div class="border-t border-outline pt-4">
                                <button type="button" id="toggle-advanced" class="w-full flex items-center justify-between p-4 bg-surface-variant rounded-xl border border-outline hover:bg-surface transition-colors">
                                    <div class="flex items-center gap-3">
                                        <i class="fa-solid fa-cog text-primary"></i>
                                        <span class="text-title-small font-semibold text-on-surface">{{ __('messages.advanced_settings') }}</span>
                                    </div>
                                    <i class="fa-solid fa-chevron-down text-on-surface-variant transition-transform" id="advanced-chevron"></i>
                                </button>
                                <div id="advanced-content" class="hidden space-y-4 mt-4 animate-fade-in-down">
                                    <!-- Auto close toggle -->
                                    <div class="flex items-center justify-between py-3">
                                        <div class="flex-1">
                                            <span class="text-title-small font-medium text-on-surface">{{ __('messages.auto_close_poll') }}</span>
                                            <p class="text-body-small text-on-surface-variant mt-1">Automatically close this poll at a specific date and time</p>
                                        </div>
                                        <div class="material-switch">
                                            <input type="checkbox" id="auto_close_enabled" name="auto_close_enabled" value="1" class="switch-input">
                                            <label for="auto_close_enabled" class="switch-label">
                                                <span class="switch-slider"></span>
                                            </label>
                                        </div>
                                    </div>
                                    <div id="auto_close_time" class="hidden mt-4">
                                        <div class="input-field">
                                            <input type="datetime-local" name="auto_close_at" placeholder=" ">
                                            <label for="auto_close_at">Auto Close Date & Time</label>
                                        </div>
                                    </div>

                                    <!-- Allow comments toggle -->
                                    <div class="flex items-center justify-between py-3">
                                        <div class="flex-1">
                                            <span class="text-title-small font-medium text-on-surface">{{ __('messages.allow_comments') }}</span>
                                            <p class="text-body-small text-on-surface-variant mt-1">Let participants leave comments on the poll</p>
                                        </div>
                                        <div class="material-switch">
                                            <input type="checkbox" id="allow_comments" name="allow_comments" value="1" class="switch-input">
                                            <label for="allow_comments" class="switch-label">
                                                <span class="switch-slider"></span>
                                            </label>
                                        </div>
                                    </div>

                                    <!-- Hide share toggle -->
                                    <div class="flex items-center justify-between py-3">
                                        <div class="flex-1">
                                            <span class="text-title-small font-medium text-on-surface">{{ __('messages.hide_share_button') }}</span>
                                            <p class="text-body-small text-on-surface-variant mt-1">Hide the share button from poll results</p>
                                        </div>
                                        <div class="material-switch">
                                            <input type="checkbox" id="hide_share" name="hide_share" value="1" class="switch-input">
                                            <label for="hide_share" class="switch-label">
                                                <span class="switch-slider"></span>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            
            <!-- Action buttons outside cards -->
            <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 mt-8">
                <div class="flex justify-end gap-4">
                    <a href="{{ route('dashboard') }}" class="btn btn-neutral">
                        {{ __('messages.cancel') }}
                    </a>
                    <button type="submit" form="poll-form" class="btn btn-primary">
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
        const addOptionBtn = document.getElementById('addOption');
        if (addOptionBtn) {
            addOptionBtn.addEventListener('click', function() {
                const container = document.getElementById('options');
                if (container) {
                    const count = container.querySelectorAll('.option-row:not(.other-row)').length;
                    const row = document.createElement('div');
                    row.className = 'flex items-center gap-3 option-row';
                    row.innerHTML = `
                        <div class="input-field flex-1">
                            <input type="text" name="options[]" placeholder=" " required>
                            <label>{{ __('messages.option_placeholder') }} ${count+1}</label>
                        </div>
                        <button type="button" class="btn btn-neutral removeOption px-3 py-2" aria-label="Remove option">✕</button>
                    `;
                    container.appendChild(row);
                    pushOtherToBottom();
                    renumberOptions();
                }
            });
        }

        // Ẩn/hiện nút add Other theo loại poll
        const addOtherBtn = document.getElementById('addOther');
        function refreshAddOtherVisibility(){
            const typeInputs = document.querySelectorAll('input[name="poll_type"]:checked');
            if (typeInputs.length > 0) {
                const type = typeInputs[0].value;
                if (addOtherBtn) {
                    if (type === 'ranking') {
                        addOtherBtn.classList.add('hidden');
                    } else {
                        addOtherBtn.classList.remove('hidden');
                    }
                }
            }
        }
        
        // Add event listeners to poll type inputs
        document.querySelectorAll('input[name="poll_type"]').forEach(input => {
            input.addEventListener('change', refreshAddOtherVisibility);
        });
        refreshAddOtherVisibility();

        // Thêm "Other" rỗng và luôn đẩy xuống cuối (chỉ với regular)
        if (addOtherBtn) {
            addOtherBtn.addEventListener('click', function(){
                const container = document.getElementById('options');
                if (container) {
                    // Nếu đã tồn tại dòng other thì chỉ đẩy xuống cuối
                    let otherRow = container.querySelector('.option-row.other-row');
                    if (!otherRow) {
                        otherRow = document.createElement('div');
                        otherRow.className = 'flex items-center gap-3 option-row other-row';
                        otherRow.innerHTML = `
                            <div class="input-field flex-1">
                                <input type="text" name="options[]" value="other" placeholder=" " readonly class="pointer-events-none opacity-70 select-none">
                                <label>Other</label>
                            </div>
                            <button type="button" class="btn btn-neutral removeOption px-3 py-2" aria-label="Remove option">✕</button>
                        `;
                        container.appendChild(otherRow);
                    }
                    pushOtherToBottom();
                }
            });
        }

        // Xóa lựa chọn (event delegation)
        const optionsContainer = document.getElementById('options');
        if (optionsContainer) {
            optionsContainer.addEventListener('click', function(e){
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
        }

        function pushOtherToBottom(){
            const container = document.getElementById('options');
            if (container) {
                const other = container.querySelector('.option-row.other-row');
                if (other) container.appendChild(other);
                renumberOptions();
            }
        }

        function renumberOptions(){
            const container = document.getElementById('options');
            if (container) {
                const rows = container.querySelectorAll('.option-row:not(.other-row) input[name="options[]"]');
                rows.forEach((inp, idx)=>{
                    inp.placeholder = `{{ __('messages.option_placeholder') }} ${idx+1}`;
                });
            }
        }

        // Keyboard shortcuts
        const optionsContainerForKeyboard = document.getElementById('options');
        if (optionsContainerForKeyboard) {
            optionsContainerForKeyboard.addEventListener('keydown', function(e){
                const isCtrlBackspace = (e.ctrlKey || e.metaKey) && (e.key === 'Backspace');
                if (e.key === 'Enter') {
                    e.preventDefault();
                    const addOptionBtn = document.getElementById('addOption');
                    if (addOptionBtn) addOptionBtn.click();
                } else if (isCtrlBackspace) {
                    const row = e.target.closest('.option-row');
                    if (row && !row.classList.contains('other-row')) {
                        const removeBtn = row.querySelector('.removeOption');
                        if (removeBtn) removeBtn.click();
                    }
                }
            });
        }


        // Xử lý thay đổi loại poll với segmented buttons
        document.querySelectorAll('input[name="poll_type"]').forEach(input => {
            input.addEventListener('change', function() {
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
        });

        // Voting security: hiển thị access key khi chọn private
        const votingSecurity = document.getElementById('voting_security');
        if (votingSecurity) {
            // Set default value to session
            votingSecurity.value = 'session';
            
            // Update floating label on change
            const updateFloatingLabel = () => {
                const label = votingSecurity.nextElementSibling;
                if (votingSecurity.value !== '') {
                    label.style.transform = 'translateY(-24px) scale(0.75)';
                    label.style.color = 'var(--on-surface-variant)';
                }
            };
            
            updateFloatingLabel();
            
            votingSecurity.addEventListener('change', function(){
                updateFloatingLabel();
                
                const accessKeyField = document.getElementById('access-key-field');
                if (accessKeyField) {
                    accessKeyField.classList.toggle('hidden', this.value !== 'private');
                }
            });
        }


        // Material Switch handling - no need for attachToggle function anymore
        // All switches are now handled by CSS and form submission
        // Advanced settings toggle
        const toggleAdvanced = document.getElementById('toggle-advanced');
        if (toggleAdvanced) {
            toggleAdvanced.addEventListener('click', function() {
                const content = document.getElementById('advanced-content');
                const chevron = document.getElementById('advanced-chevron');
                
                if (content) content.classList.toggle('hidden');
                if (chevron) chevron.classList.toggle('rotate-180');
            });
        }

        // Material Switch handling
        const autoCloseEnabled = document.getElementById('auto_close_enabled');
        if (autoCloseEnabled) {
            autoCloseEnabled.addEventListener('change', function() {
                const autoCloseTime = document.getElementById('auto_close_time');
                if (autoCloseTime) {
                    autoCloseTime.classList.toggle('hidden', !this.checked);
                }
            });
        }
        
        // Legacy toggle handling for other switches (if any remain)
        // attachToggle('toggle_allow_multiple', 'allow_multiple');
        // attachToggle('toggle_allow_comments', 'allow_comments');
        // attachToggle('toggle_hide_share', 'hide_share');
    </script>
    </x-app-layout>
