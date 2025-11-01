{{--
    Create Poll Page - polls/create.blade.php
    
    Trang tạo poll mới với Material Design 3 form.
    
    Features:
    - Two-column layout: Basic info (left) và Advanced settings (right)
    - Poll types: Standard, Ranking, Image
    - Dynamic form: Hiển thị/ẩn fields dựa trên poll type
    - Media upload: Upload images/videos cho image polls
    - Options management: Thêm/xóa options dynamically
    - Form validation: Client-side và server-side validation
    
    Poll Types:
    - Standard: Single/multiple choice với text options
    - Ranking: User phải rank tất cả options
    - Image: Chọn images với optional max selections
    
    JavaScript:
    - Dynamic form fields: Show/hide sections dựa trên poll type
    - Options management: Add/remove options với validation
    - Media upload: Handle file upload và URL validation
    - Character counter: Description field counter
    - Form submission: Validate và submit với loading state
    
    @author QuickPoll Team
--}}
<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-headline-large text-on-surface font-semibold">{{ __('messages.create_poll') }}</h1>
                <p class="text-body-medium text-on-surface-variant mt-1">{{ __('messages.create_poll_subtext') }}</p>
            </div>
            <div class="flex items-center gap-3">
                {{-- Back to Dashboard Button --}}
                <a href="{{ route('dashboard') }}" class="btn btn-neutral">
                    <i class="fa-solid fa-arrow-left"></i>
                    {{ __('messages.back_to_dashboard') }}
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8">
            {{-- Main Material Design Card --}}
            <div class="bg-[var(--surface)] overflow-hidden shadow-lg sm:rounded-2xl border border-[color:var(--outline)] text-[color:var(--on-surface)]">
                {{-- Validation Errors --}}
                @if ($errors->any())
                    <div class="bg-red-100 text-red-800 p-3 rounded mb-4">
                        <ul class="list-disc pl-5">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <div class="p-8">
                    {{-- Poll Creation Form --}}
                    <form id="poll-form" method="POST" action="{{ route('polls.store') }}" class="space-y-8">
                        @csrf
                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                            {{-- Left Section: Basic Information --}}
                            <div class="space-y-6">
                                <div class="border-b border-outline pb-4">
                                    <h3 class="text-headline-small text-on-surface font-semibold mb-2">{{ __('messages.basic_info') }}</h3>
                                    <p class="text-body-medium text-on-surface-variant">{{ __('messages.basic_info_desc') }}</p>
                                </div>
                            
                                {{-- Poll Title Input --}}
                                <div class="input-field">
                                    <input type="text" name="title" id="title" required placeholder=" " value="{{ old('title') }}">
                                    <label for="title">{{ __('messages.poll_title') }}</label>
                                </div>

                                {{-- Poll Description Textarea với character counter --}}
                                <div class="input-field">
                                    <textarea name="description" id="description" rows="3" placeholder=" " maxlength="500">{{ old('description') }}</textarea>
                                    <label for="description">{{ __('messages.description_optional') }}</label>
                                    <div class="text-right text-body-small text-on-surface-variant mt-1">
                                        <span id="desc-count">0</span>/500 {{ __('messages.characters') }}
                                    </div>
                                </div>

                                {{-- Media Upload Section: Chỉ hiển thị cho Image polls --}}
                                <div id="media-upload-section" class="space-y-3" style="display: none;">
                                    <label class="text-title-small text-on-surface">{{ __('messages.media_files_optional') }}</label>
                                    <div class="media-upload-section bg-surface-variant rounded-xl p-4 border border-outline">
                                        <div class="space-y-4">
                                            <!-- Upload Area -->
                                            <div class="media-upload-area border-2 border-dashed border-outline rounded-lg p-6 text-center hover:border-primary transition-colors cursor-pointer">
                                                <div class="upload-content">
                                                    <i class="fa-solid fa-cloud-upload-alt text-4xl text-on-surface-variant mb-2"></i>
                                                    <p class="text-body-medium text-on-surface-variant mb-2">{{ __('messages.drag_drop_media_files') }}</p>
                                                    <button type="button" class="btn btn-primary media-upload-btn">
                                                        <i class="fa-solid fa-upload"></i>
                                                        {{ __('messages.upload_media') }}
                                                    </button>
                                                    <p class="text-body-small text-on-surface-variant mt-2">{{ __('messages.support_media_types') }}</p>
                                                </div>
                                            </div>
                                            
                                            <!-- URL Input -->
                                            <div class="input-field">
                                                <input type="url" id="media-url-input" placeholder=" " class="media-url-input">
                                                <label for="media-url-input">{{ __('messages.or_enter_media_url') }}</label>
                                            </div>
                                            
                                            <!-- Media Preview Container -->
                                            <div id="media-preview-container" class="space-y-3 hidden">
                                                <div class="flex items-center justify-between">
                                                    <h4 class="text-title-small text-on-surface">{{ __('messages.media_preview') }}</h4>
                                                    <button type="button" id="clear-all-media" class="text-button text-error">
                                                        <i class="fa-solid fa-trash"></i>
                                                        {{ __('messages.clear_all') }}
                                                    </button>
                                                </div>
                                                <div id="media-items" class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                                    <!-- Media items will be added here dynamically -->
                                                </div>
                                            </div>
                                            
                                            <!-- Hidden inputs for form submission -->
                                            <div id="hidden-media-inputs">
                                                <!-- Hidden inputs will be added here -->
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                {{-- Poll Type Selection: Standard, Ranking, hoặc Image --}}
                                <div class="space-y-3">
                                    <div class="segmented-control">
                                        <input type="radio" name="poll_type" id="poll_type_standard" value="standard" checked class="segmented-input">
                                        <label for="poll_type_standard" class="segmented-button">
                                            <i class="fa-solid fa-chart-pie"></i>
                                            {{ __('messages.poll_type_standard') }}
                                        </label>
                                        
                                        <input type="radio" name="poll_type" id="poll_type_ranking" value="ranking" class="segmented-input">
                                        <label for="poll_type_ranking" class="segmented-button">
                                            <i class="fa-solid fa-sort-numeric-down"></i>
                                            {{ __('messages.poll_type_ranking') }}
                                        </label>

                                        <input type="radio" name="poll_type" id="poll_type_image" value="image" class="segmented-input">
                                        <label for="poll_type_image" class="segmented-button">
                                            <i class="fa-solid fa-image"></i>
                                            {{ __('messages.poll_type_image') }}
                                        </label>
                                    </div>
                                </div>

                                {{-- Choice Type: Single hoặc Multiple (chỉ cho Standard polls) --}}
                                <div id="choice-type-section" class="space-y-2">
                                    <div class="segmented-control">
                                        <input type="radio" name="choice_type" id="choice_type_single" value="single" checked class="segmented-input">
                                        <label for="choice_type_single" class="segmented-button">
                                            <i class="fa-solid fa-circle-dot"></i>
                                            {{ __('messages.choice_single') }}
                                        </label>
                                        
                                        <input type="radio" name="choice_type" id="choice_type_multiple" value="multiple" class="segmented-input">
                                        <label for="choice_type_multiple" class="segmented-button">
                                            <i class="fa-solid fa-check-square"></i>
                                            {{ __('messages.choice_multiple') }}
                                        </label>
                                    </div>
                                </div>

                                {{-- Maximum Choices: Chỉ hiển thị cho Standard polls với multiple selection --}}
                                <div id="max-choices-section" class="input-field hidden">
                                    <input type="number" name="max_choices" id="max_choices" min="2" placeholder=" " value="{{ old('max_choices') }}">
                                    <label for="max_choices">{{ __('messages.max_choices') }}</label>
                                    <div class="text-body-small text-on-surface-variant mt-1">{{ __('messages.leave_empty_unlimited') }}</div>
                                </div>

                                {{-- Maximum Image Selections: Chỉ cho Image polls --}}
                                <div id="max-image-selections-section" class="input-field hidden">
                                    <input type="number" name="max_image_selections" id="max_image_selections" min="1" placeholder=" " value="{{ old('max_image_selections') }}">
                                    <label for="max_image_selections">{{ __('messages.max_image_selections') }}</label>
                                    <div class="text-body-small text-on-surface-variant mt-1">{{ __('messages.leave_empty_unlimited') }}</div>
                                </div>

                                <div class="space-y-3">
                                    <label class="text-title-small text-on-surface">{{ __('messages.options') }}</label>
                                    
                                    <!-- Text Options Container -->
                                    <div id="text-options" class="space-y-3 max-h-80 overflow-y-auto pr-1">
                                        <div class="flex items-center gap-3 option-row">
                                            <div class="input-field flex-1">
                                                <input type="text" name="options[]" placeholder=" ">
                                                <label>{{ __('messages.option_1') }}</label>
                                            </div>
                                            <button type="button" class="btn btn-neutral removeOption px-3 py-2" aria-label="Remove option">✕</button>
                                        </div>
                                        <div class="flex items-center gap-3 option-row">
                                            <div class="input-field flex-1">
                                                <input type="text" name="options[]" placeholder=" ">
                                                <label>{{ __('messages.option_2') }}</label>
                                            </div>
                                            <button type="button" class="btn btn-neutral removeOption px-3 py-2" aria-label="Remove option">✕</button>
                                        </div>
                                    </div>

                                    <!-- Image Options Container -->
                                    <div id="image-options" class="space-y-4 max-h-80 overflow-y-auto pr-1 hidden">
                                        <div class="image-option-card bg-surface-variant rounded-xl p-4 border border-outline">
                                            <div class="space-y-3">
                                                <!-- Image Upload/URL Area -->
                                                <div class="image-upload-area border-2 border-dashed border-outline rounded-lg p-6 text-center hover:border-primary transition-colors cursor-pointer">
                                                    <div class="upload-content">
                                                        <i class="fa-solid fa-cloud-upload-alt text-4xl text-on-surface-variant mb-2"></i>
                                                        <p class="text-body-medium text-on-surface-variant mb-2">{{ __('messages.drag_drop_image') }}</p>
                                                        <button type="button" class="btn btn-primary upload-btn">
                                                            <i class="fa-solid fa-upload"></i>
                                                            {{ __('messages.upload_image') }}
                                                        </button>
                                                        <p class="text-body-small text-on-surface-variant mt-2">{{ __('messages.or') }}</p>
                                                        <div class="input-field mt-2">
                                                            <input type="url" class="image-url-input" placeholder=" " data-index="0">
                                                            <label>{{ __('messages.enter_image_url') }}</label>
                                                        </div>
                                                    </div>
                                                    <div class="image-preview hidden">
                                                        <div class="aspect-square w-32 h-32 mx-auto rounded-lg overflow-hidden bg-gray-100 dark:bg-gray-700">
                                                            <img src="" alt="Preview" class="w-full h-full object-cover">
                                                        </div>
                                                        <div class="mt-2 text-xs text-gray-500 dark:text-gray-400">
                                                            <i class="fa-solid fa-info-circle mr-1"></i>
                                                            {{ __('messages.image_crop_info') }}
                                                        </div>
                                                        <button type="button" class="btn btn-neutral mt-2 remove-image-btn">
                                                            <i class="fa-solid fa-trash"></i>
                                                            {{ __('messages.remove_image') }}
                                                        </button>
                                                    </div>
                                                </div>
                                                
                                                <!-- Image Title -->
                                                <div class="input-field">
                                                    <input type="text" name="image_titles[]" placeholder=" ">
                                                    <label>{{ __('messages.image_title_required') }}</label>
                                                </div>
                                                
                                                <!-- Hidden inputs for image data -->
                                                <input type="hidden" name="image_urls[]" class="image-url-hidden">
                                                <input type="hidden" name="image_option_texts[]" class="image-option-text-hidden">
                                                
                                                <!-- Remove button -->
                                                <div class="flex justify-end">
                                                    <button type="button" class="btn btn-neutral removeImageOption px-3 py-2" aria-label="Remove image option">✕</button>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <div class="image-option-card bg-surface-variant rounded-xl p-4 border border-outline">
                                            <div class="space-y-3">
                                                <!-- Image Upload/URL Area -->
                                                <div class="image-upload-area border-2 border-dashed border-outline rounded-lg p-6 text-center hover:border-primary transition-colors cursor-pointer">
                                                    <div class="upload-content">
                                                        <i class="fa-solid fa-cloud-upload-alt text-4xl text-on-surface-variant mb-2"></i>
                                                        <p class="text-body-medium text-on-surface-variant mb-2">{{ __('messages.drag_drop_image') }}</p>
                                                        <button type="button" class="btn btn-primary upload-btn">
                                                            <i class="fa-solid fa-upload"></i>
                                                            {{ __('messages.upload_image') }}
                                                        </button>
                                                        <p class="text-body-small text-on-surface-variant mt-2">{{ __('messages.or') }}</p>
                                                        <div class="input-field mt-2">
                                                            <input type="url" class="image-url-input" placeholder=" " data-index="1">
                                                            <label>{{ __('messages.enter_image_url') }}</label>
                                                        </div>
                                                    </div>
                                                    <div class="image-preview hidden">
                                                        <div class="aspect-square w-32 h-32 mx-auto rounded-lg overflow-hidden bg-gray-100 dark:bg-gray-700">
                                                            <img src="" alt="Preview" class="w-full h-full object-cover">
                                                        </div>
                                                        <div class="mt-2 text-xs text-gray-500 dark:text-gray-400">
                                                            <i class="fa-solid fa-info-circle mr-1"></i>
                                                            {{ __('messages.image_crop_info') }}
                                                        </div>
                                                        <button type="button" class="btn btn-neutral mt-2 remove-image-btn">
                                                            <i class="fa-solid fa-trash"></i>
                                                            {{ __('messages.remove_image') }}
                                                        </button>
                                                    </div>
                                                </div>
                                                
                                                <!-- Image Title -->
                                                <div class="input-field">
                                                    <input type="text" name="image_titles[]" placeholder=" ">
                                                    <label>{{ __('messages.image_title_required') }}</label>
                                                </div>
                                                
                                                <!-- Hidden inputs for image data -->
                                                <input type="hidden" name="image_urls[]" class="image-url-hidden">
                                                <input type="hidden" name="image_option_texts[]" class="image-option-text-hidden">
                                                
                                                <!-- Remove button -->
                                                <div class="flex justify-end">
                                                    <button type="button" class="btn btn-neutral removeImageOption px-3 py-2" aria-label="Remove image option">✕</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="flex items-center justify-start mt-3 gap-3">
                                        <button type="button" id="addOption" class="text-button text-primary">
                                            <i class="fa-solid fa-plus"></i>
                                            {{ __('messages.add_option') }}
                                        </button>
                                        <button type="button" id="addImageOption" class="text-button text-primary hidden">
                                            <i class="fa-solid fa-plus"></i>
                                            {{ __('messages.add_image_option') }}
                                        </button>
                                        <button type="button" id="addOther" class="text-button text-primary">
                                            <i class="fa-solid fa-plus"></i>
                                            {{ __('messages.add_other') }}
                                        </button>
                                    </div>
                                </div>

                                <div id="ranking-info" class="hidden bg-surface-variant p-4 rounded-xl border border-outline mt-4">
                                    <div class="flex items-start gap-3">
                                        <i class="fa-solid fa-info-circle text-primary mt-0.5"></i>
                                        <div>
                                            <p class="text-body-medium text-on-surface">{{ __('messages.ranking_info') }}</p>
                                            <p class="text-body-small text-on-surface-variant mt-1">{{ __('messages.participants_rank_all') }}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Right section: Settings -->
                            <div class="space-y-6">
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
                                    <input type="text" name="access_key" id="access_key" placeholder=" " value="">
                                    <label for="access_key">{{ __('messages.access_key') }}</label>
                                    <div class="text-body-small text-on-surface-variant mt-1">{{ __('messages.leave_empty_auto_generate') }}</div>
                                </div>

                                <!-- Advanced Settings Expansion Panel -->
                                <div class="space-y-4">
                                    <div class="border-t border-outline pt-4">
                                        <button type="button" id="toggle-advanced" class="w-full flex items-center justify-between p-4 bg-surface-variant rounded-xl border border-outline hover:bg-surface transition-colors">
                                            <div class="flex items-center gap-3">
                                                <span class="text-2xl">⚙️</span>
                                                <span class="text-title-small font-semibold text-on-surface">{{ __('messages.advanced_settings') }}</span>
                                            </div>
                                            <i class="fa-solid fa-chevron-down text-on-surface-variant transition-transform" id="advanced-chevron"></i>
                                        </button>
                                        <div id="advanced-content" class="hidden space-y-4 mt-4 animate-fade-in-down">
                                    <!-- Auto close toggle -->
                                    <div class="flex items-center justify-between py-3">
                                        <div class="flex-1">
                                            <span class="text-title-small font-medium text-on-surface">{{ __('messages.auto_close_poll') }}</span>
                                            <p class="text-body-small text-on-surface-variant mt-1">{{ __('messages.auto_close_poll_desc') }}</p>
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
                                            <label for="auto_close_at">{{ __('messages.auto_close_date_time') }}</label>
                                        </div>
                                    </div>

                                    <!-- Allow comments toggle -->
                                    <div class="flex items-center justify-between py-3">
                                        <div class="flex-1">
                                            <span class="text-title-small font-medium text-on-surface">{{ __('messages.allow_comments') }}</span>
                                            <p class="text-body-small text-on-surface-variant mt-1">{{ __('messages.allow_comments_desc') }}</p>
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
                                            <p class="text-body-small text-on-surface-variant mt-1">{{ __('messages.hide_share_button_desc') }}</p>
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
        </div>
        
        <!-- Action Buttons outside card -->
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8 mt-8 mb-8">
            <div class="flex justify-end gap-4">
                <a href="{{ route('dashboard') }}" class="btn btn-neutral px-6 py-3">
                    {{ __('messages.cancel') }}
                </a>
                <button type="submit" form="poll-form" class="btn btn-primary px-6 py-3">
                    <i class="fa-solid fa-plus"></i>
                    {{ __('messages.create') }}
                </button>
            </div>
        </div>
    </div>

    <script>
        // Helper: renumber placeholders (skip the .other-row)
        function renumberOptions(){
            const container = document.getElementById('text-options');
            const rows = container.querySelectorAll('.option-row:not(.other-row) input[type="text"]');
            rows.forEach((inp, idx)=>{
                inp.placeholder = `{{ __('messages.option_placeholder') }} ${idx+1}`;
            });
        }

        // Thêm lựa chọn
        const addOptionBtn = document.getElementById('addOption');
        if (addOptionBtn) {
            addOptionBtn.addEventListener('click', function() {
                const container = document.getElementById('text-options');
                if (container) {
                    const count = container.querySelectorAll('.option-row:not(.other-row)').length;
                    const row = document.createElement('div');
                    row.className = 'flex items-center gap-3 option-row';
                    row.innerHTML = `
                        <div class="input-field flex-1">
                            <input type="text" name="options[]" placeholder=" ">
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
                const container = document.getElementById('text-options');
                if (container) {
                    // Nếu đã tồn tại dòng other thì chỉ đẩy xuống cuối
                    let otherRow = container.querySelector('.option-row.other-row');
                    if (!otherRow) {
                        otherRow = document.createElement('div');
                        otherRow.className = 'flex items-center gap-3 option-row other-row';
                        otherRow.innerHTML = `
                            <div class="input-field flex-1">
                                <input type="text" name="options[]" value="other" placeholder=" " readonly class="pointer-events-none opacity-70 select-none">
                                <label>{{ __('messages.other') }}</label>
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
        const optionsContainer = document.getElementById('text-options');
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
            const container = document.getElementById('text-options');
            if (container) {
                const other = container.querySelector('.option-row.other-row');
                if (other) container.appendChild(other);
                renumberOptions();
            }
        }

        function renumberOptions(){
            const container = document.getElementById('text-options');
            if (container) {
                const rows = container.querySelectorAll('.option-row:not(.other-row) input[name="options[]"]');
                rows.forEach((inp, idx)=>{
                    inp.placeholder = `{{ __('messages.option_placeholder') }} ${idx+1}`;
                });
            }
        }

        // Keyboard shortcuts
        const optionsContainerForKeyboard = document.getElementById('text-options');
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
                const rankingInfo = document.getElementById('ranking-info');
                const choiceTypeSection = document.getElementById('choice-type-section');
                const maxChoicesSection = document.getElementById('max-choices-section');
                const maxImageSelectionsSection = document.getElementById('max-image-selections-section');
                const mediaUploadSection = document.getElementById('media-upload-section');
                const textOptions = document.getElementById('text-options');
                const imageOptions = document.getElementById('image-options');
                const addOptionBtn = document.getElementById('addOption');
                const addImageOptionBtn = document.getElementById('addImageOption');
                const addOtherBtn = document.getElementById('addOther');
                
                if (this.value === 'ranking') {
                    if (rankingInfo) rankingInfo.classList.remove('hidden');
                    if (choiceTypeSection) choiceTypeSection.classList.add('hidden');
                    if (maxChoicesSection) maxChoicesSection.classList.add('hidden');
                    if (maxImageSelectionsSection) maxImageSelectionsSection.classList.add('hidden');
                    if (mediaUploadSection) mediaUploadSection.style.display = 'none';
                    // Clear media when switching to ranking poll
                    if (typeof clearAllMedia === 'function') {
                        clearAllMedia();
                    }
                    if (textOptions) textOptions.classList.remove('hidden');
                    if (imageOptions) imageOptions.classList.add('hidden');
                    if (addOptionBtn) addOptionBtn.classList.remove('hidden');
                    if (addImageOptionBtn) addImageOptionBtn.classList.add('hidden');
                    if (addOtherBtn) addOtherBtn.classList.remove('hidden');
                } else if (this.value === 'image') {
                    if (rankingInfo) rankingInfo.classList.add('hidden');
                    if (choiceTypeSection) choiceTypeSection.classList.add('hidden');
                    if (maxChoicesSection) maxChoicesSection.classList.add('hidden');
                    if (maxImageSelectionsSection) maxImageSelectionsSection.classList.remove('hidden');
                    if (mediaUploadSection) mediaUploadSection.style.display = 'block';
                    if (textOptions) textOptions.classList.add('hidden');
                    if (imageOptions) imageOptions.classList.remove('hidden');
                    if (addOptionBtn) addOptionBtn.classList.add('hidden');
                    if (addImageOptionBtn) addImageOptionBtn.classList.remove('hidden');
                    if (addOtherBtn) addOtherBtn.classList.add('hidden');
                } else {
                    // Standard poll
                    if (rankingInfo) rankingInfo.classList.add('hidden');
                    if (choiceTypeSection) choiceTypeSection.classList.remove('hidden');
                    if (maxImageSelectionsSection) maxImageSelectionsSection.classList.add('hidden');
                    if (mediaUploadSection) mediaUploadSection.style.display = 'none';
                    // Clear media when switching away from image poll
                    if (typeof clearAllMedia === 'function') {
                        clearAllMedia();
                    }
                    if (textOptions) textOptions.classList.remove('hidden');
                    if (imageOptions) imageOptions.classList.add('hidden');
                    if (addOptionBtn) addOptionBtn.classList.remove('hidden');
                    if (addImageOptionBtn) addImageOptionBtn.classList.add('hidden');
                    if (addOtherBtn) addOtherBtn.classList.remove('hidden');
                    
                    // Check current choice type to show/hide max-choices-section
                    const selectedChoiceType = document.querySelector('input[name="choice_type"]:checked');
                    if (maxChoicesSection && selectedChoiceType) {
                        if (selectedChoiceType.value === 'multiple') {
                            maxChoicesSection.classList.remove('hidden');
                        } else {
                            maxChoicesSection.classList.add('hidden');
                        }
                    }
                }
            });
        });

        // Xử lý choice type để set allow_multiple và hiện max choices
        document.querySelectorAll('input[name="choice_type"]').forEach(input => {
            input.addEventListener('change', function() {
                const maxChoicesSection = document.getElementById('max-choices-section');
                
                // Tạo hidden input để gửi allow_multiple
                let hiddenInput = document.querySelector('input[name="allow_multiple"]');
                if (!hiddenInput) {
                    hiddenInput = document.createElement('input');
                    hiddenInput.type = 'hidden';
                    hiddenInput.name = 'allow_multiple';
                    document.getElementById('poll-form').appendChild(hiddenInput);
                }
                hiddenInput.value = this.value === 'multiple' ? '1' : '0';
                
                // Hiện/ẩn max choices field
                if (maxChoicesSection) {
                    if (this.value === 'multiple') {
                        maxChoicesSection.classList.remove('hidden');
                    } else {
                        maxChoicesSection.classList.add('hidden');
                    }
                }
            });
        });

        // Function to generate random access key
        function generateAccessKey() {
            const chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
            let result = '';
            for (let i = 0; i < 8; i++) {
                result += chars.charAt(Math.floor(Math.random() * chars.length));
            }
            return result;
        }

        // Voting security: hiển thị access key khi chọn private
        const votingSecurity = document.getElementById('voting_security');
        const accessKeyInput = document.getElementById('access_key');
        
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
                    
                    // Auto-generate access key when switching to private and field is empty
                    if (this.value === 'private' && accessKeyInput && !accessKeyInput.value.trim()) {
                        accessKeyInput.value = generateAccessKey();
                    }
                }
            });
        }

        // Auto-generate access key when field is focused and empty
        if (accessKeyInput) {
            accessKeyInput.addEventListener('focus', function() {
                if (!this.value.trim()) {
                    this.value = generateAccessKey();
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
        
        // Character counter for description
        const descTextarea = document.getElementById('description');
        const descCount = document.getElementById('desc-count');
        if (descTextarea && descCount) {
            descTextarea.addEventListener('input', function() {
                descCount.textContent = this.value.length;
            });
            // Initialize counter
            descCount.textContent = descTextarea.value.length;
        }

        // Image Upload Functionality
        let imageOptionIndex = 2; // Start from 2 since we have 2 default image options

        // Add Image Option
        const addImageOptionBtn = document.getElementById('addImageOption');
        if (addImageOptionBtn) {
            addImageOptionBtn.addEventListener('click', function() {
                const container = document.getElementById('image-options');
                if (container) {
                    const newOption = createImageOptionCard(imageOptionIndex);
                    container.appendChild(newOption);
                    imageOptionIndex++;
                }
            });
        }

        function createImageOptionCard(index) {
            const div = document.createElement('div');
            div.className = 'image-option-card bg-surface-variant rounded-xl p-4 border border-outline';
            div.innerHTML = `
                <div class="space-y-3">
                    <!-- Image Upload/URL Area -->
                    <div class="image-upload-area border-2 border-dashed border-outline rounded-lg p-6 text-center hover:border-primary transition-colors cursor-pointer">
                        <div class="upload-content">
                            <i class="fa-solid fa-cloud-upload-alt text-4xl text-on-surface-variant mb-2"></i>
                            <p class="text-body-medium text-on-surface-variant mb-2">{{ __('messages.drag_drop_image') }}</p>
                            <button type="button" class="btn btn-primary upload-btn">
                                <i class="fa-solid fa-upload"></i>
                                {{ __('messages.upload_image') }}
                            </button>
                            <p class="text-body-small text-on-surface-variant mt-2">{{ __('messages.or') }}</p>
                            <div class="input-field mt-2">
                                <input type="url" class="image-url-input" placeholder=" " data-index="${index}">
                                <label>{{ __('messages.enter_image_url') }}</label>
                            </div>
                        </div>
                        <div class="image-preview hidden">
                            <img src="" alt="Preview" class="max-w-full max-h-48 mx-auto rounded-lg">
                            <button type="button" class="btn btn-neutral mt-2 remove-image-btn">
                                <i class="fa-solid fa-trash"></i>
                                {{ __('messages.remove_image') }}
                            </button>
                        </div>
                    </div>
                    
                    <!-- Image Title -->
                    <div class="input-field">
                        <input type="text" name="image_titles[]" placeholder=" ">
                        <label>{{ __('messages.image_title_required') }}</label>
                    </div>
                    
                    <!-- Hidden inputs for image data -->
                    <input type="hidden" name="image_urls[]" class="image-url-hidden">
                    <input type="hidden" name="image_option_texts[]" class="image-option-text-hidden">
                    
                    <!-- Remove button -->
                    <div class="flex justify-end">
                        <button type="button" class="btn btn-neutral removeImageOption px-3 py-2" aria-label="Remove image option">✕</button>
                    </div>
                </div>
            `;
            return div;
        }

        // Image Upload Event Delegation
        document.addEventListener('click', function(e) {
            // Upload button click
            if (e.target.closest('.upload-btn')) {
                const uploadBtn = e.target.closest('.upload-btn');
                const fileInput = document.createElement('input');
                fileInput.type = 'file';
                fileInput.accept = 'image/*';
                fileInput.onchange = function(event) {
                    const file = event.target.files[0];
                    if (file) {
                        uploadImage(file, uploadBtn);
                    }
                };
                fileInput.click();
            }

            // Remove image button click
            if (e.target.closest('.remove-image-btn')) {
                const card = e.target.closest('.image-option-card');
                const uploadArea = card.querySelector('.image-upload-area');
                const uploadContent = uploadArea.querySelector('.upload-content');
                const imagePreview = uploadArea.querySelector('.image-preview');
                const hiddenUrlInput = card.querySelector('.image-url-hidden');
                const hiddenTextInput = card.querySelector('.image-option-text-hidden');
                
                if (uploadContent) uploadContent.classList.remove('hidden');
                if (imagePreview) imagePreview.classList.add('hidden');
                if (hiddenUrlInput) hiddenUrlInput.value = '';
                if (hiddenTextInput) hiddenTextInput.value = '';
            }

            // Remove image option button click
            if (e.target.closest('.removeImageOption')) {
                const card = e.target.closest('.image-option-card');
                card.remove();
            }
        });

        // Image URL input handling
        document.addEventListener('input', function(e) {
            if (e.target.classList.contains('image-url-input')) {
                const url = e.target.value.trim();
                if (url) {
                    validateAndSetImageUrl(url, e.target);
                }
            }
        });

        // Drag and drop functionality
        document.addEventListener('dragover', function(e) {
            if (e.target.closest('.image-upload-area')) {
                e.preventDefault();
                e.target.closest('.image-upload-area').classList.add('border-primary', 'bg-primary-container');
            }
        });

        document.addEventListener('dragleave', function(e) {
            if (e.target.closest('.image-upload-area')) {
                e.target.closest('.image-upload-area').classList.remove('border-primary', 'bg-primary-container');
            }
        });

        document.addEventListener('drop', function(e) {
            if (e.target.closest('.image-upload-area')) {
                e.preventDefault();
                const uploadArea = e.target.closest('.image-upload-area');
                uploadArea.classList.remove('border-primary', 'bg-primary-container');
                
                const files = e.dataTransfer.files;
                if (files.length > 0 && files[0].type.startsWith('image/')) {
                    uploadImage(files[0], uploadArea.querySelector('.upload-btn'));
                }
            }
        });

        function uploadImage(file, uploadBtn) {
            const formData = new FormData();
            formData.append('media', file);

            fetch('{{ route("media.upload") }}', {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const card = uploadBtn.closest('.image-option-card');
                    const uploadArea = card.querySelector('.image-upload-area');
                    const uploadContent = uploadArea.querySelector('.upload-content');
                    const imagePreview = uploadArea.querySelector('.image-preview');
                    const hiddenUrlInput = card.querySelector('.image-url-hidden');
                    const hiddenTextInput = card.querySelector('.image-option-text-hidden');
                    const titleInput = card.querySelector('input[name="image_titles[]"]');
                    
                    // Show preview
                    const imgElement = imagePreview.querySelector('img');
                    if (imgElement) imgElement.src = data.url;
                    if (uploadContent) uploadContent.classList.add('hidden');
                    if (imagePreview) imagePreview.classList.remove('hidden');
                    
                    // Set hidden values
                    if (hiddenUrlInput) hiddenUrlInput.value = data.url;
                    if (hiddenTextInput) hiddenTextInput.value = titleInput ? titleInput.value || 'Image Option' : 'Image Option';
                } else {
                    alert('Upload failed: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Upload error:', error);
                alert('Upload failed. Please try again.');
            });
        }

        function validateAndSetImageUrl(url, inputElement) {
            fetch('{{ route("media.validate-url") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({ url: url })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const card = inputElement.closest('.image-option-card');
                    const uploadArea = card.querySelector('.image-upload-area');
                    const uploadContent = uploadArea.querySelector('.upload-content');
                    const imagePreview = uploadArea.querySelector('.image-preview');
                    const hiddenUrlInput = card.querySelector('.image-url-hidden');
                    const hiddenTextInput = card.querySelector('.image-option-text-hidden');
                    const titleInput = card.querySelector('input[name="image_titles[]"]');
                    
                    // Show preview
                    const imgElement = imagePreview.querySelector('img');
                    if (imgElement) imgElement.src = url;
                    if (uploadContent) uploadContent.classList.add('hidden');
                    if (imagePreview) imagePreview.classList.remove('hidden');
                    
                    // Set hidden values
                    if (hiddenUrlInput) hiddenUrlInput.value = url;
                    if (hiddenTextInput) hiddenTextInput.value = titleInput ? titleInput.value || 'Image Option' : 'Image Option';
                } else {
                    alert('Invalid image URL: ' + data.message);
                }
            })
            .catch(error => {
                console.error('URL validation error:', error);
                alert('Could not validate image URL. Please check the URL and try again.');
            });
        }

        // Update hidden text input when title changes
        document.addEventListener('input', function(e) {
            if (e.target.name === 'image_titles[]') {
                const card = e.target.closest('.image-option-card');
                const hiddenTextInput = card.querySelector('.image-option-text-hidden');
                hiddenTextInput.value = e.target.value || 'Image Option';
            }
        });

        // Media Upload Functionality for Description
        let mediaItems = [];
        let mediaIndex = 0;

        // Media upload button click
        document.addEventListener('click', function(e) {
            if (e.target.closest('.media-upload-btn')) {
                const fileInput = document.createElement('input');
                fileInput.type = 'file';
                fileInput.accept = 'image/*,video/*';
                fileInput.multiple = true;
                fileInput.onchange = function(event) {
                    const files = Array.from(event.target.files);
                    files.forEach(file => uploadMedia(file));
                };
                fileInput.click();
            }

            // Clear all media
            if (e.target.closest('#clear-all-media')) {
                clearAllMedia();
            }

            // Remove individual media item
            if (e.target.closest('.remove-media-item')) {
                const mediaItem = e.target.closest('.media-item');
                const index = mediaItem.dataset.index;
                removeMediaItem(index);
            }
        });

        // Media URL input handling
        document.addEventListener('input', function(e) {
            if (e.target.classList.contains('media-url-input')) {
                const url = e.target.value.trim();
                if (url) {
                    validateAndAddMediaUrl(url);
                    e.target.value = ''; // Clear input after processing
                }
            }
        });

        // Drag and drop functionality for media
        const mediaUploadArea = document.querySelector('.media-upload-area');
        if (mediaUploadArea) {
            mediaUploadArea.addEventListener('dragover', function(e) {
                e.preventDefault();
                this.classList.add('border-primary', 'bg-primary-container');
            });

            mediaUploadArea.addEventListener('dragleave', function(e) {
                this.classList.remove('border-primary', 'bg-primary-container');
            });

            mediaUploadArea.addEventListener('drop', function(e) {
                e.preventDefault();
                this.classList.remove('border-primary', 'bg-primary-container');
                
                const files = Array.from(e.dataTransfer.files);
                files.forEach(file => {
                    if (file.type.startsWith('image/') || file.type.startsWith('video/')) {
                        uploadMedia(file);
                    }
                });
            });
        }

        function uploadMedia(file) {
            const formData = new FormData();
            formData.append('media', file);

            fetch('{{ route("media.upload") }}', {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    addMediaItem({
                        url: data.url,
                        type: data.type,
                        filename: data.filename,
                        path: data.path,
                        extension: data.extension
                    });
                } else {
                    alert('Upload failed: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Upload error:', error);
                alert('Upload failed. Please try again.');
            });
        }

        function validateAndAddMediaUrl(url) {
            fetch('{{ route("media.validate-url") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({ url: url })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    addMediaItem({
                        url: data.url,
                        type: data.type,
                        filename: url.split('/').pop(),
                        path: null,
                        extension: data.url.split('.').pop()
                    });
                } else {
                    alert('Invalid media URL: ' + data.message);
                }
            })
            .catch(error => {
                console.error('URL validation error:', error);
                alert('Could not validate media URL. Please check the URL and try again.');
            });
        }

        function addMediaItem(mediaData) {
            const mediaItem = {
                ...mediaData,
                index: mediaIndex++
            };
            
            mediaItems.push(mediaItem);
            renderMediaItem(mediaItem);
            updateMediaPreview();
            updateHiddenInputs();
        }

        function renderMediaItem(mediaItem) {
            const mediaItemsContainer = document.getElementById('media-items');
            const mediaItemElement = document.createElement('div');
            mediaItemElement.className = 'media-item bg-[var(--surface)] text-[color:var(--on-surface)] rounded-lg border border-[color:var(--outline)] overflow-hidden';
            mediaItemElement.dataset.index = mediaItem.index;

            if (mediaItem.type === 'video') {
                mediaItemElement.innerHTML = `
                    <div class="aspect-video bg-gray-100 dark:bg-gray-700">
                        <video controls class="w-full h-full object-cover">
                            <source src="${mediaItem.url}" type="video/${mediaItem.extension}">
                            Your browser does not support the video tag.
                        </video>
                    </div>
                    <div class="p-3">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-2">
                                <i class="fa-solid fa-video text-primary"></i>
                                <span class="text-sm font-medium text-gray-800 dark:text-gray-200">${mediaItem.filename}</span>
                            </div>
                            <button type="button" class="remove-media-item text-red-500 hover:text-red-700">
                                <i class="fa-solid fa-trash"></i>
                            </button>
                        </div>
                    </div>
                `;
            } else {
                mediaItemElement.innerHTML = `
                    <div class="aspect-square bg-gray-100 dark:bg-gray-700">
                        <img src="${mediaItem.url}" alt="${mediaItem.filename}" class="w-full h-full object-cover">
                    </div>
                    <div class="p-3">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-2">
                                <i class="fa-solid fa-image text-primary"></i>
                                <span class="text-sm font-medium text-gray-800 dark:text-gray-200">${mediaItem.filename}</span>
                            </div>
                            <button type="button" class="remove-media-item text-red-500 hover:text-red-700">
                                <i class="fa-solid fa-trash"></i>
                            </button>
                        </div>
                    </div>
                `;
            }

            mediaItemsContainer.appendChild(mediaItemElement);
        }

        function removeMediaItem(index) {
            mediaItems = mediaItems.filter(item => item.index !== index);
            const mediaItemElement = document.querySelector(`[data-index="${index}"]`);
            if (mediaItemElement) {
                mediaItemElement.remove();
            }
            updateMediaPreview();
            updateHiddenInputs();
        }

        function clearAllMedia() {
            mediaItems = [];
            const mediaItemsContainer = document.getElementById('media-items');
            mediaItemsContainer.innerHTML = '';
            updateMediaPreview();
            updateHiddenInputs();
        }

        function updateMediaPreview() {
            const previewContainer = document.getElementById('media-preview-container');
            if (mediaItems.length > 0) {
                previewContainer.classList.remove('hidden');
            } else {
                previewContainer.classList.add('hidden');
            }
        }

        function updateHiddenInputs() {
            const hiddenInputsContainer = document.getElementById('hidden-media-inputs');
            hiddenInputsContainer.innerHTML = '';

            // Only create hidden inputs if there are actual media items
            if (mediaItems && mediaItems.length > 0) {
                mediaItems.forEach(item => {
                    if (item && item.url) { // Only create input if item has URL
                        const hiddenInput = document.createElement('input');
                        hiddenInput.type = 'hidden';
                        hiddenInput.name = 'description_media[]';
                        hiddenInput.value = JSON.stringify(item);
                        hiddenInputsContainer.appendChild(hiddenInput);
                    }
                });
            }
        }
        // Auto-fill empty option inputs to prevent validation errors
        function autoFillEmptyOptions() {
            const optionInputs = document.querySelectorAll('input[name="options[]"]');
            optionInputs.forEach((input, index) => {
                if (!input.value.trim()) {
                    input.value = `Option ${index + 1}`;
                }
            });
            
            // Also handle image titles
            const imageTitleInputs = document.querySelectorAll('input[name="image_titles[]"]');
            imageTitleInputs.forEach((input, index) => {
                if (!input.value.trim()) {
                    input.value = `Image ${index + 1}`;
                }
            });
        }

        // Form validation before submit
        document.getElementById('poll-form').addEventListener('submit', function(e) {
            // Auto-fill empty options to prevent validation errors
            autoFillEmptyOptions();
            
            // Check validation BEFORE cleaning up inputs
            const pollType = document.querySelector('input[name="poll_type"]:checked');
            
            if (pollType && pollType.value === 'image') {
                // For image polls, check image options first
                const imageOptions = document.querySelectorAll('input[name="image_option_texts[]"]');
                const filledImageOptions = Array.from(imageOptions).filter(input => input.value.trim());
                
                if (filledImageOptions.length < 2) {
                    e.preventDefault();
                    alert('Please provide at least 2 image options for your poll.');
                    return false;
                }
            } else {
                // For standard/ranking polls, check text options
                const optionInputs = document.querySelectorAll('input[name="options[]"]');
                const filledOptions = Array.from(optionInputs).filter(input => input.value.trim());
                
                if (filledOptions.length < 2) {
                    e.preventDefault();
                    alert('Please provide at least 2 options for your poll.');
                    return false;
                }
            }
            
            // Clean up empty inputs AFTER validation
            const imageUrlInputs = document.querySelectorAll('input[name="image_urls[]"]');
            imageUrlInputs.forEach(input => {
                if (!input.value.trim()) {
                    input.remove(); // Remove empty inputs completely
                }
            });
            
            const imageTitleInputs = document.querySelectorAll('input[name="image_titles[]"]');
            imageTitleInputs.forEach(input => {
                if (!input.value.trim()) {
                    input.remove(); // Remove empty inputs completely
                }
            });
            
            const imageOptionTextInputs = document.querySelectorAll('input[name="image_option_texts[]"]');
            imageOptionTextInputs.forEach(input => {
                if (!input.value.trim()) {
                    input.remove(); // Remove empty inputs completely
                }
            });
            
            const descriptionMediaInputs = document.querySelectorAll('input[name="description_media[]"]');
            descriptionMediaInputs.forEach(input => {
                if (!input.value.trim()) {
                    input.remove(); // Remove empty inputs completely
                }
            });
        });
    </script>

    <!-- Custom CSS for Image Poll Aspect Ratio Optimization -->
    <style>
        /* Responsive Grid Layout for Image Options */
        @media (max-width: 640px) {
            .image-option-card {
                max-width: 100%;
                margin: 0 auto;
            }
            
            .image-upload-area {
                padding: 1rem;
            }
            
            .aspect-square {
                width: 120px;
                height: 120px;
            }
        }
        
        @media (min-width: 641px) and (max-width: 1024px) {
            .image-option-card {
                max-width: 280px;
            }
            
            .aspect-square {
                width: 150px;
                height: 150px;
            }
        }
        
        @media (min-width: 1025px) {
            .image-option-card {
                max-width: 320px;
            }
            
            .aspect-square {
                width: 180px;
                height: 180px;
            }
        }
        
        /* Ensure consistent aspect ratio across all image containers */
        .aspect-square img {
            object-fit: cover;
            object-position: center;
        }
        
        /* Smooth transitions for responsive changes */
        .image-option-card {
            transition: all 0.3s ease-in-out;
        }
    </style>
</x-app-layout>
