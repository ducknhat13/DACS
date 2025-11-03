{{--
    Component: secondary-button
    - Nút phụ (neutral/secondary) cho hành động ít quan trọng hơn.
--}}
<button {{ $attributes->merge(['type' => 'button', 'class' => 'inline-flex items-center px-4 py-2 bg-[var(--surface)] border border-[color:var(--outline)] rounded-md font-semibold text-xs text-[color:var(--on-surface)] uppercase tracking-widest shadow-sm hover:bg-[color:color-mix(in_srgb,var(--primary)_8%,transparent)] focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 disabled:opacity-25 transition ease-in-out duration-150']) }}>
    {{ $slot }}
</button>
