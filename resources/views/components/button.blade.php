@props([
    'type' => 'submit',
    'color' => 'slate',
    'fullWidth' => true
])

@php
    $colorClasses = [
        'blue' => 'bg-[#0066FF] hover:bg-[#0052CC] text-white shadow-md shadow-[#0066FF]/15 active:scale-[0.98]',
        'teal' => 'bg-[#0D9488] hover:bg-[#0B7A70] text-white shadow-md shadow-[#0D9488]/15 active:scale-[0.98]',
        'purple' => 'bg-[#7C3AED] hover:bg-[#6D28D9] text-white shadow-md shadow-[#7C3AED]/15 active:scale-[0.98]',
        'slate' => 'bg-[#1e293b] hover:bg-[#0f172a] text-white active:scale-[0.98]'
    ][$color] ?? 'bg-[#1e293b] hover:bg-[#0f172a] text-white';

    $widthClass = $fullWidth ? 'w-full' : '';
@endphp

<button type="{{ $type }}" {{ $attributes->merge(['class' => "$colorClasses $widthClass py-2.5 px-5 rounded-xl text-sm font-semibold tracking-wide transition-all duration-300 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#0066FF]/20 cursor-pointer flex items-center justify-center"]) }}>
    {{ $slot }}
</button>
