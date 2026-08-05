@php
    $locales = ['fr' => __('lang.french'), 'en' => __('lang.english')];
    $currentLocale = app()->getLocale();
    $currentPath = request()->path();
    $query = request()->query();
@endphp

<div class="inline-flex items-center gap-0.5 rounded-full border border-[#e2e8f0] bg-white/90 backdrop-blur p-0.5 shadow-sm" role="group" aria-label="Language">
    @foreach ($locales as $code => $label)
        @php
            $query['lang'] = $code;
            $href = url($currentPath) . '?' . http_build_query($query);
        @endphp
        <a href="{{ $href }}"
           lang="{{ $code }}"
           title="{{ $label }}"
           class="rounded-full px-2.5 py-1 text-[11px] font-bold tracking-wide uppercase transition-colors duration-200 no-underline {{ $currentLocale === $code ? 'bg-[#1e293b] text-white' : 'text-[#64748b] hover:text-[#1e293b]' }}">
            {{ strtoupper($code) }}
        </a>
    @endforeach
</div>
