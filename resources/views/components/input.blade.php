@props([
    'type' => 'text',
    'name',
    'label' => '',
    'placeholder' => '',
    'required' => false,
    'value' => '',
    'options' => [],
    'showStrength' => false,
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
            <div class="relative {{ $type === 'password' ? 'password-wrap' : '' }}">
            <input type="{{ $type }}" name="{{ $name }}" id="{{ $name }}" placeholder="{{ $placeholder }}" value="{{ $value }}" @if($required) required @endif
                class="custom-input w-full px-4 py-2.5 bg-[#F8FAFC] border border-[#e2e8f0] rounded-xl text-sm text-[#1e293b] placeholder-[#94a3b8] focus:outline-none focus:bg-white {{ $type === 'password' ? 'pr-10' : '' }}">
            @if($type === 'password')
                <button type="button" tabindex="-1" onclick="togglePasswordVisibility(this)" class="password-toggle"
                    style="position:absolute;right:10px;top:50%;transform:translateY(-50%);background:none;border:none;cursor:pointer;color:#94a3b8;padding:4px;display:flex;align-items:center;justify-content:center;border-radius:6px;transition:color 0.15s;" aria-label="Afficher/Masquer le mot de passe">
                    <svg class="pw-eye w-[18px] h-[18px]" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                    <svg class="pw-eye-off w-[18px] h-[18px]" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24" style="display:none;">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 001.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.45 10.45 0 0112 4.5c4.756 0 8.773 3.162 10.065 7.498a10.523 10.523 0 01-4.293 5.774M6.228 6.228L3 3m3.228 3.228l3.65 3.65m7.894 7.894L21 21m-3.228-3.228l-3.65-3.65m0 0a3 3 0 10-4.243-4.243m4.242 4.242L9.88 9.88"/>
                    </svg>
                </button>
                @once
                <script>
                    function togglePasswordVisibility(btn) {
                        var input = btn.previousElementSibling;
                        if (!input || input.tagName !== 'INPUT') return;
                        var showing = input.type === 'text';
                        input.type = showing ? 'password' : 'text';
                        var eye = btn.querySelector('.pw-eye');
                        var eyeOff = btn.querySelector('.pw-eye-off');
                        if (eye) eye.style.display = showing ? '' : 'none';
                        if (eyeOff) eyeOff.style.display = showing ? 'none' : '';
                        btn.setAttribute('aria-label', showing ? 'Afficher le mot de passe' : 'Masquer le mot de passe');
                        input.focus();
                    }
                </script>
                @endonce
            @endif
            </div>
            @if($type === 'password' && $showStrength)
                <div class="mt-2 text-xs font-semibold text-gray-500">
                    <div class="flex items-center gap-1.5">
                        <span class="text-[10px] uppercase text-[#64748b] tracking-wider select-none">{{ __('password.security') }}</span>
                        <div class="flex-1 h-1.5 bg-gray-250 rounded-full overflow-hidden flex gap-0.5">
                            <div id="password-strength-bar-1" class="h-full w-1/4 rounded-full transition-all duration-300 bg-gray-200"></div>
                            <div id="password-strength-bar-2" class="h-full w-1/4 rounded-full transition-all duration-300 bg-gray-200"></div>
                            <div id="password-strength-bar-3" class="h-full w-1/4 rounded-full transition-all duration-300 bg-gray-200"></div>
                            <div id="password-strength-bar-4" class="h-full w-1/4 rounded-full transition-all duration-300 bg-gray-200"></div>
                        </div>
                        <span id="password-strength-label" class="text-[10px] font-bold text-[#64748b] select-none min-w-[60px] text-right">{{ __('password.very_weak') }}</span>
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
                                label.textContent = '{{ __('password.very_weak') }}';
                                label.style.color = '#64748b';
                            } else if (score === 1) {
                                label.textContent = '{{ __('password.weak') }}';
                                label.style.color = '#ef4444';
                            } else if (score === 2) {
                                label.textContent = '{{ __('password.medium') }}';
                                label.style.color = '#f59e0b';
                            } else if (score === 3) {
                                label.textContent = '{{ __('password.strong') }}';
                                label.style.color = '#3b82f6';
                            } else if (score === 4) {
                                label.textContent = '{{ __('password.very_strong') }}';
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
