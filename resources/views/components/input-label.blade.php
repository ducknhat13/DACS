{{--
    Component: input-label
    - Nhãn cho input; hỗ trợ for/id, slot để đặt text label.
--}}
@props(['value'])

<label {{ $attributes->merge(['class' => 'block font-medium text-sm text-[color:var(--on-surface-variant)]']) }}>
    {{ $value ?? $slot }}
</label>
