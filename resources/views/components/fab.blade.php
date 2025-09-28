@props(['href' => '#', 'icon' => '+', 'text' => ''])

<a href="{{ $href }}" 
   class="fab ripple {{ !empty($text) ? 'fab-extended' : '' }}"
   {{ $attributes }}>
    @if(!empty($text))
        <span>{{ $icon }}</span>
        <span>{{ $text }}</span>
    @else
        <span>{{ $icon }}</span>
    @endif
</a>
