@props(['disabled' => false])

<input @disabled($disabled) {{ $attributes->merge(['class' => 'border-[color:var(--outline)] bg-[var(--surface)] text-[color:var(--on-surface)] focus:border-primary-500 focus:ring-primary-500 rounded-md shadow-sm']) }}>
