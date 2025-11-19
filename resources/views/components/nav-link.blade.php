{{--
    Component: nav-link
    - Link điều hướng trong header/sidebar; hỗ trợ trạng thái active.
--}}
@props(['active'])

@php
$classes = ($active ?? false)
            ? 'material-nav-link active'
            : 'material-nav-link';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>
