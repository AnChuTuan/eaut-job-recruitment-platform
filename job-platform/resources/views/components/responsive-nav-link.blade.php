@props(['active'])

@php
$classes = ($active ?? false)
            ? 'block w-full ps-3 pe-4 py-2 border-l-4 border-brand-primary text-start text-base font-medium text-brand-navy bg-brand-accent focus:outline-none focus:text-brand-navy focus:bg-brand-section focus:border-brand-primary transition duration-150 ease-in-out'
            : 'block w-full ps-3 pe-4 py-2 border-l-4 border-transparent text-start text-base font-medium text-brand-navy/70 hover:text-brand-navy hover:bg-brand-canvas hover:border-brand-accent focus:outline-none focus:text-brand-navy focus:bg-brand-canvas focus:border-brand-accent transition duration-150 ease-in-out';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>
