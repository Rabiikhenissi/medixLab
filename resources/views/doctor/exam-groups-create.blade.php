<x-layouts.doctor>
    <x-slot:title>{{ __('doctor.exam_groups.create_title') }} - {{ __('app.brand') }}</x-slot:title>

    @section('content')

    <style>
        .glass-card {
            background: rgba(255, 255, 255, 0.85);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.7);
            box-shadow: 0 4px 24px -4px rgba(16, 30, 54, 0.08), 0 1px 4px -1px rgba(16, 30, 54, 0.04);
        }
        .form-input {
            width: 100%;
            padding: 0.625rem 1rem;
            border: 1px solid #e2e8f0;
            border-radius: 0.75rem;
            font-size: 0.8125rem;
            font-weight: 600;
            color: #1e293b;
            background: white;
            outline: none;
            transition: border-color 0.2s, box-shadow 0.2s;
        }
        .form-input:focus {
            border-color: #0066FF;
            box-shadow: 0 0 0 3px rgba(0, 102, 255, 0.12);
        }
        .exam-checkbox-label {
            display: flex;
            align-items: flex-start;
            gap: 0.625rem;
            padding: 0.5rem 0.75rem;
            background: white;
            border: 1px solid rgba(226, 232, 240, 0.8);
            border-radius: 0.625rem;
            cursor: pointer;
            transition: border-color 0.15s, background 0.15s;
        }
        .exam-checkbox-label:hover {
            border-color: rgba(0, 102, 255, 0.3);
            background: #F0F6FF;
        }
        .btn-primary {
            background: #0066FF;
            color: white;
            font-weight: 700;
            font-size: 0.75rem;
            padding: 0.625rem 1.25rem;
            border-radius: 0.75rem;
            border: none;
            cursor: pointer;
            transition: background 0.2s, transform 0.15s;
            display: inline-flex;
            align-items: center;
            gap: 0.375rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }
        .btn-primary:hover { background: #0052CC; transform: translateY(-1px); }
        .btn-secondary {
            background: #f1f5f9;
            color: #64748b;
            font-weight: 700;
            font-size: 0.75rem;
            padding: 0.625rem 1.25rem;
            border-radius: 0.75rem;
            border: 1px solid #e2e8f0;
            cursor: pointer;
            transition: background 0.2s;
            display: inline-flex;
            align-items: center;
            gap: 0.375rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            text-decoration: none;
        }
        .btn-secondary:hover { background: #e2e8f0; }
        .section-label {
            font-size: 0.65rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.1em;
            color: #64748b;
        }
    </style>

    <div class="w-full max-w-[700px] mx-auto py-4 md:py-8 space-y-6">
        <div class="glass-card rounded-[20px] px-6 py-5 flex items-center gap-4">
            <a href="{{ route('doctor.exam-groups.index') }}" class="w-9 h-9 flex items-center justify-center rounded-xl bg-[#f1f5f9] hover:bg-[#e2e8f0] border border-[#e2e8f0] transition cursor-pointer text-[#64748b]">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/>
                </svg>
            </a>
            <div>
                <h1 class="text-lg font-bold text-[#1e293b] tracking-tight">{{ __('doctor.exam_groups.create_title') }}</h1>
                <p class="text-xs text-[#64748b] mt-0.5">{{ __('doctor.exam_groups.create_subtitle') }}</p>
            </div>
        </div>

        @if($errors->any())
            <div class="glass-card rounded-[16px] px-5 py-3.5 border-l-4 border-red-500">
                <ul class="text-xs font-semibold text-[#1e293b] space-y-1">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="glass-card rounded-[20px] p-6 md:p-8">
            <form action="{{ route('doctor.exam-groups.store') }}" method="POST" class="space-y-5">
                @csrf
                <div>
                    <label class="section-label block mb-1.5">{{ __('doctor.group_name') }}</label>
                    <input type="text" name="name" class="form-input" placeholder="{{ __('doctor.exam_groups.name_placeholder') }}" value="{{ old('name') }}" required/>
                </div>

                <div>
                    <label class="section-label block mb-1.5">{{ __('doctor.description') }}</label>
                    <textarea name="description" rows="3" class="form-input resize-none" placeholder="{{ __('doctor.exam_groups.desc_placeholder') }}">{{ old('description') }}</textarea>
                </div>

                <div>
                    <label class="section-label block mb-2">{{ __('doctor.exams_available_label', ['n' => $exams->count()]) }}</label>
                    <div class="relative mb-2">
                        <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-3.5 h-3.5 text-[#94a3b8]" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                        <input type="text" id="createSearchInput" class="form-input pl-9" placeholder="{{ __('doctor.exam_groups.filter_placeholder') }}"/>
                    </div>
                    <div class="border border-[#e2e8f0] rounded-xl p-2.5 max-h-[320px] overflow-y-auto space-y-1.5 bg-[#F8FAFC]">
                        @forelse($exams as $exam)
                            <label class="exam-checkbox-label create-exam-item">
                                <input type="checkbox" name="exam_ids[]" value="{{ $exam->id }}" class="mt-0.5 accent-[#0066FF]"
                                    @if(is_array(old('exam_ids')) && in_array($exam->id, old('exam_ids'))) checked @endif
                                />
                                <div class="text-[11px] min-w-0">
                                    <span class="font-bold text-[#1e293b] block create-exam-name">{{ $exam->name }}</span>
                                    @if($exam->category)
                                        <span class="text-[9px] text-[#64748b] font-semibold uppercase">{{ $exam->category }}</span>
                                    @endif
                                </div>
                            </label>
                        @empty
                            <p class="text-center text-[11px] text-[#94a3b8] py-4 italic">{{ __('doctor.no_exams_available') }}</p>
                        @endforelse
                    </div>
                </div>

                <div class="flex gap-3 pt-2 border-t border-[#e2e8f0]">
                    <button type="submit" class="btn-primary flex-1 justify-center">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                        </svg>
                        {{ __('doctor.create_the_group') }}
                    </button>
                    <a href="{{ route('doctor.exam-groups.index') }}" class="btn-secondary">{{ __('common.cancel') }}</a>
                </div>
            </form>
        </div>
    </div>

    <script>
        const createSearchInput = document.getElementById('createSearchInput');
        if (createSearchInput) {
            createSearchInput.addEventListener('input', () => {
                const q = createSearchInput.value.toLowerCase();
                document.querySelectorAll('.create-exam-item').forEach(el => {
                    const name = el.querySelector('.create-exam-name')?.textContent.toLowerCase() ?? '';
                    el.style.display = name.includes(q) ? '' : 'none';
                });
            });
        }
    </script>
    @endsection
</x-layouts.doctor>
