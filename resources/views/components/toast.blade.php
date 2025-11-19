{{--
    Component: toast
    - Thông báo tạm thời (success/error/info); tự ẩn sau thời gian đặt sẵn.
--}}
@props(['type' => 'success', 'message' => '', 'duration' => 3000])

<div 
    x-data="{ 
        show: false, 
        message: '{{ $message }}',
        type: '{{ $type }}',
        duration: {{ $duration }}
    }"
    x-init="
        $watch('message', (value) => {
            if (value) {
                show = true;
                setTimeout(() => show = false, duration);
            }
        });
        $watch('show', (value) => {
            if (value) {
                setTimeout(() => show = false, duration);
            }
        });
    "
    x-show="show"
    x-transition:enter="transition ease-out duration-300"
    x-transition:enter-start="opacity-0 transform translate-y-2"
    x-transition:enter-end="opacity-100 transform translate-y-0"
    x-transition:leave="transition ease-in duration-200"
    x-transition:leave-start="opacity-100 transform translate-y-0"
    x-transition:leave-end="opacity-0 transform translate-y-2"
    class="fixed bottom-6 left-1/2 transform -translate-x-1/2 z-50 max-w-sm w-full mx-4"
>
    <div 
        :class="{
            'bg-success-500': type === 'success',
            'bg-error-500': type === 'error',
            'bg-warning-500': type === 'warning',
            'bg-primary-500': type === 'info'
        }"
        class="rounded-xl shadow-material-lg p-4 text-white"
    >
        <div class="flex items-center gap-3">
            <div class="flex-shrink-0">
                <i 
                    :class="{
                        'fa-check-circle': type === 'success',
                        'fa-exclamation-circle': type === 'error',
                        'fa-exclamation-triangle': type === 'warning',
                        'fa-info-circle': type === 'info'
                    }"
                    class="fa-solid text-lg"
                ></i>
            </div>
            <div class="flex-1">
                <p class="text-sm font-medium" x-text="message"></p>
            </div>
            <button 
                @click="show = false"
                class="flex-shrink-0 ml-2 p-1 rounded-lg hover:bg-[color:color-mix(in_srgb,var(--surface)_85%,transparent)] transition-colors"
            >
                <i class="fa-solid fa-times text-sm"></i>
            </button>
        </div>
    </div>
</div>

<script>
// Global toast function
window.showToast = function(message, type = 'success', duration = 3000) {
    const toast = document.querySelector('[x-data*="show: false"]');
    if (toast) {
        Alpine.store('toast', { message, type, duration });
    }
};
</script>
