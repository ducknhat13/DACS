@props(['active'])

@php
$classes = ($active ?? false)
            ? 'material-nav-link active'
            : 'material-nav-link';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>
