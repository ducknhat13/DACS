@props(['count' => 3, 'class' => ''])

<div class="skeleton-container {{ $class }}">
    @for($i = 0; $i < $count; $i++)
        <div class="skeleton-card animate-pulse">
            <div class="skeleton-header">
                <div class="skeleton-line skeleton-line-lg"></div>
                <div class="skeleton-line skeleton-line-sm"></div>
            </div>
            <div class="skeleton-content">
                <div class="skeleton-line"></div>
                <div class="skeleton-line"></div>
                <div class="skeleton-line skeleton-line-short"></div>
            </div>
            <div class="skeleton-footer">
                <div class="skeleton-button"></div>
                <div class="skeleton-button"></div>
            </div>
        </div>
    @endfor
</div>

<style>
.skeleton-container {
    @apply grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6;
}

.skeleton-card {
    @apply rounded-2xl p-6 border border-surface-200;
    background-color: var(--surface);
}

.skeleton-header {
    @apply mb-4 space-y-2;
}

.skeleton-content {
    @apply mb-6 space-y-3;
}

.skeleton-footer {
    @apply flex gap-3;
}

.skeleton-line {
    @apply h-4 bg-surface-200 rounded;
}

.skeleton-line-lg {
    @apply h-6 w-3/4;
}

.skeleton-line-sm {
    @apply h-3 w-1/2;
}

.skeleton-line-short {
    @apply w-1/3;
}

.skeleton-button {
    @apply h-10 w-20 bg-surface-200 rounded-xl;
}

@keyframes pulse {
    0%, 100% {
        opacity: 1;
    }
    50% {
        opacity: 0.5;
    }
}

.animate-pulse {
    animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
}
</style>
