@props(['value' => 0, 'max' => 100, 'size' => 'md', 'variant' => 'primary', 'showLabel' => false, 'label' => ''])

@php
$percentage = min(100, max(0, ($value / $max) * 100));
$sizeClasses = [
    'sm' => 'h-2',
    'md' => 'h-3',
    'lg' => 'h-4'
];
$variantClasses = [
    'primary' => 'bg-primary-500',
    'secondary' => 'bg-secondary-500',
    'success' => 'bg-success-500',
    'warning' => 'bg-warning-500',
    'error' => 'bg-error-500'
];
@endphp

<div class="progress-container">
    @if($showLabel && $label)
        <div class="flex justify-between items-center mb-2">
            <span class="text-sm font-medium text-surface-700">{{ $label }}</span>
            <span class="text-sm text-surface-600">{{ round($percentage) }}%</span>
        </div>
    @endif
    
    <div class="progress-track {{ $sizeClasses[$size] }} bg-surface-200 rounded-full overflow-hidden">
        <div 
            class="progress-fill {{ $variantClasses[$variant] }} h-full rounded-full transition-all duration-500 ease-out"
            style="width: {{ $percentage }}%"
            x-data="{ width: 0 }"
            x-init="
                setTimeout(() => {
                    width = {{ $percentage }};
                }, 100);
            "
            :style="`width: ${width}%`"
        ></div>
    </div>
    
    @if($showLabel && !$label)
        <div class="flex justify-between items-center mt-1">
            <span class="text-xs text-surface-500">{{ $value }}</span>
            <span class="text-xs text-surface-500">{{ $max }}</span>
        </div>
    @endif
</div>

<style>
.progress-container {
    @apply w-full;
}

.progress-track {
    @apply relative;
}

.progress-fill {
    @apply relative;
}

.progress-fill::after {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: linear-gradient(90deg, transparent, rgba(255,255,255,0.3), transparent);
    animation: shimmer 2s infinite;
}

@keyframes shimmer {
    0% { transform: translateX(-100%); }
    100% { transform: translateX(100%); }
}
</style>
