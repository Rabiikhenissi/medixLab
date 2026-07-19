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
            @if($type === 'password' && $name === 'password')
                <div class="mt-2 text-xs font-semibold text-gray-500">
                    <div class="flex items-center gap-1.5">
                        <span class="text-[10px] uppercase text-[#64748b] tracking-wider select-none">Sécurité :</span>
                        <div class="flex-1 h-1.5 bg-gray-250 rounded-full overflow-hidden flex gap-0.5">
                            <div id="password-strength-bar-1" class="h-full w-1/4 rounded-full transition-all duration-300 bg-gray-200"></div>
                            <div id="password-strength-bar-2" class="h-full w-1/4 rounded-full transition-all duration-300 bg-gray-200"></div>
                            <div id="password-strength-bar-3" class="h-full w-1/4 rounded-full transition-all duration-300 bg-gray-200"></div>
                            <div id="password-strength-bar-4" class="h-full w-1/4 rounded-full transition-all duration-300 bg-gray-200"></div>
                        </div>
                        <span id="password-strength-label" class="text-[10px] font-bold text-[#64748b] select-none min-w-[60px] text-right">Très faible</span>
                    </div>
                </div>
                <script>
                    document.addEventListener('DOMContentLoaded', () => {
                        const passInput = document.getElementById('password');
                        if (!passInput) return;

                        const bars = [
                            document.getElementById('password-strength-bar-1'),
                            document.getElementById('password-strength-bar-2'),
                            document.getElementById('password-strength-bar-3'),
                            document.getElementById('password-strength-bar-4')
                        ];
                        const label = document.getElementById('password-strength-label');

                        passInput.addEventListener('input', () => {
                            const val = passInput.value;
                            let score = 0;
                            if (val.length >= 6) score++;
                            if (/[a-z]/.test(val) && /[A-Z]/.test(val)) score++;
                            if (/\d/.test(val)) score++;
                            if (/[^A-Za-z0-9]/.test(val)) score++;

                            bars.forEach((b, idx) => {
                                b.style.backgroundColor = '#e5e7eb';
                                if (idx < score) {
                                    if (score === 1) b.style.backgroundColor = '#ef4444'; // Red
                                    else if (score === 2) b.style.backgroundColor = '#f59e0b'; // Amber
                                    else if (score === 3) b.style.backgroundColor = '#3b82f6'; // Blue
                                    else if (score === 4) b.style.backgroundColor = '#10b981'; // Green
                                }
                            });

                            if (val.length === 0) {
                                label.textContent = 'Très faible';
                                label.style.color = '#64748b';
                            } else if (score === 1) {
                                label.textContent = 'Faible';
                                label.style.color = '#ef4444';
                            } else if (score === 2) {
                                label.textContent = 'Moyen';
                                label.style.color = '#f59e0b';
                            } else if (score === 3) {
                                label.textContent = 'Fort';
                                label.style.color = '#3b82f6';
                            } else if (score === 4) {
                                label.textContent = 'Très fort';
                                label.style.color = '#10b981';
                            }
                        });
                    });
                </script>
            @endif
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
