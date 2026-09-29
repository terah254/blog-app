@props(['size' => 'md'])

@php
$sizeClasses = [
    'sm' => 'w-4 h-4 border-2',
    'md' => 'w-8 h-8 border-3',
    'lg' => 'w-12 h-12 border-4',
];
@endphp

<div {{ $attributes->merge(['class' => 'flex justify-center items-center']) }}>
    <div class="{{ $sizeClasses[$size] ?? $sizeClasses['md'] }} border-indigo-200 border-t-indigo-600 rounded-full animate-spin"></div>
</div>
