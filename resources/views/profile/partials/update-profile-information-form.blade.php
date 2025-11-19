{{--
    Partial: update-profile-information-form
    - Form cập nhật tên/email; có xác minh email nếu bật.
--}}
<section>
    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
        @csrf
    </form>

    <form method="post" action="{{ route('profile.update') }}" class="space-y-6">
        @csrf
        @method('patch')

        <div class="input-field">
            <input 
                id="name" 
                name="name" 
                type="text" 
                value="{{ old('name', $user->name) }}" 
                required 
                autofocus 
                autocomplete="name"
                placeholder=" "
            />
            <label for="name">{{ __('Name') }}</label>
            @error('name')
                <div class="text-error-500 text-sm mt-1">{{ $message }}</div>
            @enderror
        </div>

        <div class="input-field">
            <input 
                id="email" 
                name="email" 
                type="email" 
                value="{{ old('email', $user->email) }}" 
                required 
                autocomplete="username"
                placeholder=" "
            />
            <label for="email">{{ __('Email') }}</label>
            @error('email')
                <div class="text-error-500 text-sm mt-1">{{ $message }}</div>
            @enderror

            @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                <div class="mt-4 p-4 bg-warning-50 border border-warning-200 rounded-xl">
                    <p class="text-sm text-warning-800">
                        {{ __('Your email address is unverified.') }}
                        <button 
                            form="send-verification" 
                            class="btn btn-outline btn-sm ml-2"
                        >
                            {{ __('Click here to re-send the verification email.') }}
                        </button>
                    </p>

                    @if (session('status') === 'verification-link-sent')
                        <p class="mt-2 text-sm text-success-600 font-medium">
                            {{ __('A new verification link has been sent to your email address.') }}
                        </p>
                    @endif
                </div>
            @endif
        </div>

        <div class="flex-between">
            <button type="submit" class="btn btn-primary">
                <i class="fa-solid fa-save"></i>
                {{ __('Save') }}
            </button>

            @if (session('status') === 'profile-updated')
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
