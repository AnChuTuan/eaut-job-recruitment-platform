@props(['active'])

@php
$classes = ($active ?? false)
            ? 'inline-flex items-center px-1 pt-1 border-b-2 border-brand-primary text-sm font-medium leading-5 text-brand-navy focus:outline-none focus:border-brand-primary transition duration-150 ease-in-out'
            : 'inline-flex items-center px-1 pt-1 border-b-2 border-transparent text-sm font-medium leading-5 text-brand-navy/70 hover:text-brand-navy hover:border-brand-accent focus:outline-none focus:text-brand-navy focus:border-brand-accent transition duration-150 ease-in-out';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>
