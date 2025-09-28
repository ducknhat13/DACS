<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-3xl text-gray-800 dark:text-gray-200 leading-tight">{{ __('messages.please_enter_name') }}</h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-md mx-auto sm:px-6 lg:px-8">
            <div class="card">
                <form method="POST" action="{{ route('polls.name.save', $slug) }}" class="space-y-4">
                    @csrf
                    <label class="block mb-1 text-sm font-medium">{{ __('messages.your_name') }}</label>
                    <input type="text" name="voter_name" class="w-full border rounded-lg p-3 dark:bg-gray-900" required placeholder="{{ __('messages.your_name') }}">
                    <div class="flex justify-end gap-2">
                        <a href="{{ route('dashboard') }}" class="btn btn-neutral">{{ __('messages.cancel') }}</a>
                        <button type="submit" class="btn btn-primary">{{ __('messages.continue') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>