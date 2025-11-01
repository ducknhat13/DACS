<section>
    @php $requiresCurrent = (bool) (Auth::user()->has_local_password ?? !empty(Auth::user()->password)); @endphp
    <form method="post" action="{{ route('password.update') }}" class="space-y-6">
        @csrf
        @method('put')

        @if ($requiresCurrent)
        <div class="input-field">
            <input 
                id="update_password_current_password" 
                name="current_password" 
                type="password" 
                autocomplete="current-password"
                placeholder=" "
            />
            <label for="update_password_current_password">{{ __('Current Password') }}</label>
            @error('current_password', 'updatePassword')
                <div class="text-error-500 text-sm mt-1">{{ $message }}</div>
            @enderror
        </div>
        @endif

        <div class="input-field">
            <input 
                id="update_password_password" 
                name="password" 
                type="password" 
                autocomplete="new-password"
                placeholder=" "
            />
            <label for="update_password_password">{{ $requiresCurrent ? __('New Password') : __('Set Password') }}</label>
            @error('password', 'updatePassword')
                <div class="text-error-500 text-sm mt-1">{{ $message }}</div>
            @enderror
        </div>

        <div class="input-field">
            <input 
                id="update_password_password_confirmation" 
                name="password_confirmation" 
                type="password" 
                autocomplete="new-password"
                placeholder=" "
            />
            <label for="update_password_password_confirmation">{{ __('Confirm Password') }}</label>
            @error('password_confirmation', 'updatePassword')
                <div class="text-error-500 text-sm mt-1">{{ $message }}</div>
            @enderror
        </div>

        <div class="flex-between">
            <button type="submit" class="btn btn-primary">
                <i class="fa-solid fa-key"></i>
                {{ __('Save') }}
            </button>

            @if (session('status') === 'password-updated')
                <div 
                    x-data="{ show: true }"
                    x-show="show"
                    x-transition
                    x-init="setTimeout(() => show = false, 2000)"
                    class="flex items-center gap-2 text-success-600"
                >
                    <i class="fa-solid fa-check-circle"></i>
                    <span class="text-sm font-medium">{{ __('Saved.') }}</span>
                </div>
            @endif
        </div>
    </form>
</section>
