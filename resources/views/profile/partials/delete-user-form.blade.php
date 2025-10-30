<section class="space-y-6">
    <div class="p-4 bg-error-50 border border-error-200 rounded-xl">
        <div class="flex items-start gap-3">
            <i class="fa-solid fa-exclamation-triangle text-error-500 mt-1"></i>
            <div>
                <h3 class="text-sm font-medium text-error-800">
                    {{ __('Permanent Action') }}
                </h3>
                <p class="text-sm text-error-700 mt-1">
                    {{ __('Once your account is deleted, all of its resources and data will be permanently deleted. Before deleting your account, please download any data or information that you wish to retain.') }}
                </p>
            </div>
        </div>
    </div>

    <button
        x-data=""
        x-on:click.prevent="$dispatch('open-modal', 'confirm-user-deletion')"
        class="btn btn-danger"
    >
        <i class="fa-solid fa-trash"></i>
        {{ __('Delete Account') }}
    </button>

    <x-modal name="confirm-user-deletion" :show="$errors->userDeletion->isNotEmpty()" focusable>
        <div class="p-6">
            <div class="flex items-center gap-3 mb-4">
                <div class="w-12 h-12 bg-error-100 rounded-full flex items-center justify-center">
                    <i class="fa-solid fa-exclamation-triangle text-error-600"></i>
                </div>
                <div>
                    <h2 class="text-lg font-semibold text-surface-900">
                        {{ __('Delete Account') }}
                    </h2>
                    <p class="text-sm text-surface-600">
                        {{ __('This action cannot be undone') }}
                    </p>
                </div>
            </div>

            <form method="post" action="{{ route('profile.destroy') }}" class="space-y-6">
                @csrf
                @method('delete')

                <div class="p-4 bg-error-50 border border-error-200 rounded-xl">
                    <p class="text-sm text-error-800">
                        {{ __('Once your account is deleted, all of its resources and data will be permanently deleted. Please enter your password to confirm you would like to permanently delete your account.') }}
                    </p>
                </div>

                <div class="input-field">
                    <input 
                        id="password" 
                        name="password" 
                        type="password" 
                        placeholder=" "
                        required
                    />
                    <label for="password">{{ __('Password') }}</label>
                    @error('password', 'userDeletion')
                        <div class="text-error-500 text-sm mt-1">{{ $message }}</div>
                    @enderror
                </div>

                <div class="flex-end gap-3">
                    <button 
                        type="button" 
                        x-on:click="$dispatch('close')"
                        class="btn btn-neutral"
                    >
                        {{ __('Cancel') }}
                    </button>

                    <button type="submit" class="btn btn-danger">
                        <i class="fa-solid fa-trash"></i>
                        {{ __('Delete Account') }}
                    </button>
                </div>
            </form>
        </div>
    </x-modal>
</section>
