<section class="space-y-6">
    <div class="p-4 bg-error-50 border border-error-200 rounded-xl">
        <div class="flex items-start gap-3">
            <i class="fa-solid fa-exclamation-triangle text-error-500 mt-1"></i>
            <div>
                <h3 class="text-sm font-medium text-error-800">
                    {{ __('app.permanent_action') }}
                </h3>
                <p class="text-sm text-error-700 mt-1">
                    {{ __('app.delete_account_warning') }}
                </p>
            </div>
        </div>
    </div>

    <button
        type="button"
        id="openDeleteAccountModal"
        class="btn btn-danger"
    >
        <i class="fa-solid fa-trash"></i>
        {{ __('app.delete_account') }}
    </button>

    <!-- Fullscreen Delete Account Modal -->
    <div id="deleteAccountOverlay" class="fixed inset-0 z-50 hidden">
        <div class="absolute inset-0 bg-black/50"></div>
        <div class="relative w-full h-full flex items-center justify-center p-4">
            <div class="w-full max-w-lg bg-[var(--surface)] text-[color:var(--on-surface)] rounded-xl shadow-lg border border-[color:var(--outline)]">
                <div class="p-6">
                    <div class="flex items-center gap-3 mb-4">
                        <div class="w-12 h-12 bg-error-100 rounded-full flex items-center justify-center">
                            <i class="fa-solid fa-exclamation-triangle text-error-600"></i>
                        </div>
                        <div>
                            <h2 class="text-lg font-semibold text-surface-900">
                                {{ __('app.delete_account') }}
                            </h2>
                            <p class="text-sm text-surface-600">
                                {{ __('app.action_cannot_be_undone') }}
                            </p>
                        </div>
                    </div>

                    @php $requiresPassword = (bool) (Auth::user()->has_local_password ?? !empty(Auth::user()->password)); @endphp
                    <form method="post" action="{{ route('profile.destroy') }}" class="space-y-6">
                        @csrf
                        @method('delete')

                        <div class="p-4 bg-error-50 border border-error-200 rounded-xl">
                            <p class="text-sm text-error-800">
                                {{ $requiresPassword ? __('app.delete_account_confirm') : __('app.delete_account_confirm_no_password') }}
                            </p>
                        </div>

                        @if ($requiresPassword)
                        <div class="input-field">
                            <input 
                                id="password" 
                                name="password" 
                                type="password" 
                                placeholder=" "
                                required
                                autocomplete="current-password"
                            />
                            <label for="password">{{ __('app.password') }}</label>
                            @error('password', 'userDeletion')
                                <div class="text-error-500 text-sm mt-1">{{ $message }}</div>
                            @enderror
                        </div>
                        @endif

                        <div class="flex-end gap-3">
                            <button 
                                type="button" 
                                id="closeDeleteAccountModal"
                                class="btn btn-neutral"
                            >
                                {{ __('app.cancel') }}
                            </button>

                            <button type="submit" class="btn btn-danger">
                                <i class="fa-solid fa-trash"></i>
                                {{ __('app.delete_account') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', function(){
        const openBtn = document.getElementById('openDeleteAccountModal');
        const closeBtn = document.getElementById('closeDeleteAccountModal');
        const overlay = document.getElementById('deleteAccountOverlay');
        const backdrop = overlay ? overlay.firstElementChild : null;

        // Di chuyển overlay ra ngoài body để tránh bị ảnh hưởng bởi transform/overflow của thẻ card
        if (overlay && overlay.parentElement !== document.body) {
            document.body.appendChild(overlay);
        }

        function open(){ if(overlay){ overlay.classList.remove('hidden'); document.body.style.overflow='hidden'; } }
        function close(){ if(overlay){ overlay.classList.add('hidden'); document.body.style.overflow=''; } }

        if (openBtn) openBtn.addEventListener('click', open);
        if (closeBtn) closeBtn.addEventListener('click', close);
        if (backdrop) backdrop.addEventListener('click', close);

        // Nếu có lỗi validate (ví dụ sai mật khẩu), tự mở lại modal
        @if ($errors->userDeletion->isNotEmpty())
            open();
        @endif
    });
    </script>
</section>
