@props([
    'type' => 'text',
    'name',
    'label' => '',
    'placeholder' => '',
    'required' => false,
    'value' => '',
    'options' => []
])

<div class="w-full">
    @if($type !== 'checkbox')
        @if($label)
            <label for="{{ $name }}" class="block text-xs font-semibold text-[#64748b] mb-1.5 select-none">
                {{ $label }} @if($required)<span class="text-rose-500">*</span>@endif
            </label>
        @endif

        @if($type === 'select')
            <div class="relative">
                <select name="{{ $name }}" id="{{ $name }}" @if($required) required @endif
                    class="custom-input w-full px-4 py-2.5 bg-[#F8FAFC] border border-[#e2e8f0] rounded-xl text-sm text-[#1e293b] focus:outline-none focus:bg-white appearance-none cursor-pointer">
                    @if($placeholder)
                        <option value="" disabled selected>{{ $placeholder }}</option>
                    @endif
                    @foreach($options as $val => $text)
                        <option value="{{ $val }}" {{ $value == $val ? 'selected' : '' }}>{{ $text }}</option>
                    @endforeach
                </select>
                <!-- Custom dropdown arrow -->
                <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-4 text-[#64748b]">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" />
                    </svg>
                </div>
            </div>
        @elseif($type === 'textarea')
            <textarea name="{{ $name }}" id="{{ $name }}" rows="3" placeholder="{{ $placeholder }}" @if($required) required @endif
                class="custom-input w-full px-4 py-2.5 bg-[#F8FAFC] border border-[#e2e8f0] rounded-xl text-sm text-[#1e293b] placeholder-[#94a3b8] focus:outline-none focus:bg-white resize-none">{{ $value }}</textarea>
        @else
            <input type="{{ $type }}" name="{{ $name }}" id="{{ $name }}" placeholder="{{ $placeholder }}" value="{{ $value }}" @if($required) required @endif
                class="custom-input w-full px-4 py-2.5 bg-[#F8FAFC] border border-[#e2e8f0] rounded-xl text-sm text-[#1e293b] placeholder-[#94a3b8] focus:outline-none focus:bg-white">
        @endif
    @else
        <div class="flex items-center">
            <input type="checkbox" name="{{ $name }}" id="{{ $name }}" value="1" {{ $value ? 'checked' : '' }}
                class="w-4 h-4 text-[#0066FF] border-[#e2e8f0] rounded focus:ring-[#0066FF]/20 focus:ring-offset-0 cursor-pointer">
            @if($label)
                <label for="{{ $name }}" class="ml-2 text-xs font-medium text-[#64748b] select-none cursor-pointer">
                    {{ $label }}
                </label>
            @endif
        </div>
    @endif
</div>
