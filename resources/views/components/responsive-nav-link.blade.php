{{--
    Component: responsive-nav-link
    - Link điều hướng cho menu responsive (mobile);
    - Tự đổi style khi active.
--}}
@props(['active'])

@php
$classes = ($active ?? false)
            ? 'block w-full ps-3 pe-4 py-2 border-l-4 border-primary-500 text-start text-base font-medium text-primary-700 bg-primary-50 focus:outline-none focus:text-primary-800 focus:bg-primary-100 focus:border-primary-700 transition duration-150 ease-in-out'
            : 'block w-full ps-3 pe-4 py-2 border-l-4 border-transparent text-start text-base font-medium text-[color:var(--on-surface-variant)] hover:text-[color:var(--on-surface)] hover:bg-[var(--surface-variant)] hover:border-[color:var(--outline)] focus:outline-none focus:text-[color:var(--on-surface)] focus:bg-[var(--surface-variant)] focus:border-[color:var(--outline)] transition duration-150 ease-in-out';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>
