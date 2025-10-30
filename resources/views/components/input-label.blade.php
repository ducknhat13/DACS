@props(['value'])

<label {{ $attributes->merge(['class' => 'block font-medium text-sm text-[color:var(--on-surface-variant)]']) }}>
    {{ $value ?? $slot }}
</label>
