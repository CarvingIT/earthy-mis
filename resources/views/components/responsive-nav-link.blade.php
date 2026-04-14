@props(['active'])

@php
$classes = ($active ?? false)
            ? 'block w-full ps-3 pe-4 py-2 border-l-4 text-start text-base font-medium text-green-700 bg-green-50 focus:outline-none focus:text-green-800 focus:bg-green-100 transition duration-150 ease-in-out'
            : 'block w-full ps-3 pe-4 py-2 border-l-4 border-transparent text-start text-base font-medium text-gray-600 hover:text-gray-800 hover:bg-gray-50 hover:border-gray-300 focus:outline-none focus:text-gray-800 focus:bg-gray-50 focus:border-gray-300 transition duration-150 ease-in-out';

$styles = ($active ?? false)
            ? 'border-left-color:#16a34a !important;'
            : '';
@endphp

<a {{ $attributes->merge(['class' => $classes, 'style' => $styles]) }}>
    {{ $slot }}
</a>
