{{--
    Page: polls/access
    - Bảo vệ poll private: nhập access key để vào trang vote/kết quả.
    - Frontend: hiển thị lỗi sai key, giữ lại giá trị cũ nếu cần.
--}}
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-3xl text-gray-800 dark:text-gray-200 leading-tight">{{ __('messages.access_key') }}</h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-md mx-auto sm:px-6 lg:px-8">
            <div class="card">
                @if (session('error'))
                    <div class="bg-red-100 text-red-800 p-3 rounded mb-4">{{ session('error') }}</div>
                @endif
                <form method="POST" action="{{ route('polls.access.check', $slug) }}" class="space-y-4">
                    @csrf
                    <label class="block mb-1 text-sm font-medium">{{ __('messages.access_key') }}</label>
                    <input type="text" name="access_key" class="w-full border rounded-lg p-3 dark:bg-gray-900" required placeholder="{{ __('messages.access_key_placeholder') }}">
                    <div class="flex justify-end gap-2">
                        <a href="{{ route('dashboard') }}" class="btn btn-neutral">{{ __('messages.cancel') }}</a>
                        <button type="submit" class="btn btn-primary">{{ __('messages.confirm') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>


