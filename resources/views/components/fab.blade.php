@props(['href' => '#', 'icon' => '+', 'text' => ''])

<a href="{{ $href }}" 
   class="md-fab ripple {{ !empty($text) ? 'md-fab-extended' : '' }}"
   style="position:fixed;right:24px;bottom:24px;z-index:1000;"
   aria-label="Create poll"
   {{ $attributes }}>
    @if(!empty($text))
        <span class="material-symbols-rounded">add</span>
        <span>{{ $text }}</span>
    @else
        <span class="material-symbols-rounded">add</span>
    @endif
</a>
