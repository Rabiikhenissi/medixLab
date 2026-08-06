<x-layouts.doctor>
    <x-slot:title>{{ __('doctor.exam_selection.title') }} - {{ __('app.brand') }}</x-slot:title>

    @section('content')
    <div class="w-full max-w-7xl mx-auto">
        <div class="glass-card rounded-[20px] p-6 md:p-8 relative overflow-hidden shadow-xs select-none">
            
            <!-- Header -->
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 pb-6 mb-6 border-b border-[#e2e8f0]/80">
                <div class="flex items-center gap-4">
                    <div class="w-14 h-14 rounded-2xl bg-gradient-to-br from-[#0066FF] to-[#0052CC] flex items-center justify-center shadow-lg flex-shrink-0">
                        <svg class="w-7 h-7 text-white" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M22 12h-4l-3 9L9 3l-3 9H2"/>
                        </svg>
                    </div>
                    <div>
                        <h1 class="text-xl font-bold text-[#1e293b]">
                            {{ __('doctor.exam_selection.prescription') }}
                        </h1>
                        <p class="text-xs text-[#64748b] mt-1 font-semibold">
                            {{ __('doctor.patient_label') }} : 
                            <span class="text-[#0066FF] font-bold">
                                {{ $patient->user->first_name }} {{ $patient->user->last_name }}
                            </span>
                            <span class="text-[10px] text-[#64748b] font-mono bg-[#f1f5f9] px-2 py-0.5 rounded border border-[#e2e8f0] ml-1">
                                {{ $patient->patient_code }}
                            </span>
                        </p>
                    </div>
                </div>

                <a href="{{ route('doctor.dashboard') }}"
                   class="bg-[#f1f5f9] hover:bg-[#e2e8f0] text-[#64748b] hover:text-[#1e293b] font-bold px-4 py-2.5 rounded-xl transition text-xs uppercase tracking-wider border border-[#e2e8f0] flex items-center justify-center gap-1.5 cursor-pointer">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/>
                    </svg>
                    {{ __('common.back') }}
                </a>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

                <!-- LEFT SIDE (2/3 width) -->
                <div class="lg:col-span-2 space-y-4">

                    <!-- Search Bar -->
                    <div class="relative">
                        <svg class="absolute left-3.5 top-1/2 -translate-y-1/2 w-4 h-4 text-[#0066FF]" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                        <input
                            id="searchExams"
                            type="text"
                            placeholder="{{ __('doctor.exam_selection.search_placeholder') }}"
                            class="w-full pl-10 pr-10 py-2.5 border border-[#e2e8f0] rounded-xl focus:outline-none focus:ring-2 focus:ring-[#0066FF]/20 focus:border-[#0066FF] transition text-[#1e293b] text-xs bg-white shadow-xs font-semibold"
                        >
                        <button id="clearSearchBtn"
                                class="hidden absolute right-3.5 top-1/2 -translate-y-1/2 text-[#64748b] hover:text-[#1e293b] text-sm cursor-pointer">
                            ✕
                        </button>
                    </div>

                    <!-- Exams list -->
                    <div class="border border-[#e2e8f0] rounded-2xl p-4 bg-[#F8FAFC]/50 max-h-[520px] overflow-y-auto">
                        <div id="examsContainer" class="space-y-3">
                            @forelse($exams as $exam)
                                <label id="exam_card_{{ $exam->id }}"
                                       class="exam-item flex gap-3.5 p-4 bg-white border border-[#e2e8f0] rounded-xl cursor-pointer hover:border-[#0066FF]/35 transition shadow-xs">
                                    
                                    <input
                                        type="checkbox"
                                        name="exam_ids"
                                        value="{{ $exam->id }}"
                                        data-id="{{ $exam->id }}"
                                        class="exam-checkbox mt-1 w-4 h-4 accent-[#0066FF]"
                                    >

                                    <div class="flex-1 min-w-0">
                                        <div class="flex flex-wrap gap-2 items-center">
                                            <h3 class="font-bold text-sm text-[#1e293b]">
                                                {{ $exam->name }}
                                            </h3>
                                            @if($exam->category)
                                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[9px] font-bold tracking-wider text-[#0066FF] bg-[#0066FF]/10 border border-[#0066FF]/20 uppercase">
                                                    {{ $exam->category }}
                                                </span>
                                            @endif
                                        </div>

                                        @if($exam->description)
                                            <p class="text-xs text-[#64748b] mt-1.5 leading-relaxed font-semibold">
                                                {{ $exam->description }}
                                            </p>
                                        @endif

                                        @if($exam->preparation_instructions)
                                            <div class="mt-2.5 text-[10px] font-semibold bg-amber-50 text-amber-700 border border-amber-200 rounded-lg px-3 py-2 flex items-start gap-1.5">
                                                <svg class="w-3.5 h-3.5 text-amber-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                                </svg>
                                                <span>{{ $exam->preparation_instructions }}</span>
                                            </div>
                                        @endif
                                    </div>
                                </label>
                            @empty
                                <div class="text-center py-10 text-[#64748b] italic">
                                    {{ __('doctor.no_exams_available') }}
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>
                
                <!-- RIGHT SIDE (1/3 width) -->
                <div class="space-y-4">

                    <!-- Selected counter -->
                    <div class="bg-gradient-to-br from-[#0066FF]/5 to-[#0066FF]/10 border border-[#0066FF]/20 rounded-2xl p-5 text-center">
                        <p class="text-[10px] font-bold text-[#64748b] uppercase tracking-wider">
                            {{ __('doctor.selected_exams') }}
                        </p>
                        <p id="selectedCount" class="text-4xl font-black text-[#0066FF] mt-1 select-none">
                            0
                        </p>
                    </div>

                    <!-- Groups -->
                    <div class="bg-white border border-[#e2e8f0] rounded-2xl p-5 shadow-xs">
                        <h3 class="text-xs font-bold text-[#64748b] uppercase tracking-widest mb-4 pb-2 border-b border-[#e2e8f0]/80 flex items-center gap-2">
                            <svg class="w-4 h-4 text-[#0066FF]" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                            </svg>
                            {{ __('doctor.exam_selection.quick_groups') }}
                        </h3>

                        <div class="space-y-2.5 max-h-60 overflow-y-auto pr-1">
                            @forelse($examGroups as $group)
                                <button
                                    type="button"
                                    class="quickGroupBtn w-full text-left p-3.5 rounded-xl border border-[#e2e8f0] bg-gradient-to-br from-[#F8FAFC] to-[#EFF6FF]/20 hover:border-[#0066FF]/35 transition shadow-xs flex items-center justify-between cursor-pointer"
                                    data-group-id="{{ $group->id }}"
                                    data-group-name="{{ $group->name }}"
                                    data-group-desc="{{ $group->description ?? __('doctor.no_description') }}"
                                    data-group-exams="{{ json_encode($group->items->map(fn($item)=>$item->exam->name ?? 'Examen')) }}"
                                >
                                    <div class="min-w-0 flex-1">
                                        <p class="text-xs font-bold text-[#1e293b]">
                                            {{ $group->name }}
                                        </p>
                                        <p class="text-[10px] text-[#64748b] mt-0.5 font-semibold">
                                            {{ __('doctor.exam_count', ['n' => $group->items->count()]) }}
                                        </p>
                                    </div>
                                    <svg class="w-4 h-4 text-[#0066FF] flex-shrink-0 ml-2" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
                                    </svg>
                                </button>
                            @empty
                                <p class="text-xs text-[#94a3b8] italic">
                                    {{ __('doctor.exam_selection.no_groups') }}
                                </p>
                            @endforelse
                        </div>
                    </div>

                    <!-- TIER 1.4 — Smart Exam Suggestions -->
                    <div id="smartSuggestionsPanel" class="bg-white border border-[#e2e8f0] rounded-2xl p-5 shadow-xs">
                        <h3 class="text-xs font-bold text-[#64748b] uppercase tracking-widest mb-3 pb-2 border-b border-[#e2e8f0]/80 flex items-center gap-2">
                            <svg class="w-4 h-4 text-[#0D9488]" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/>
                            </svg>
                            {{ __('doctor.exam_selection.smart_suggestions') }}
                        </h3>
                        <div id="smartSuggestionsList" class="space-y-2">
                            <div class="text-center py-4">
                                <div class="animate-spin w-4 h-4 border-2 border-[#0D9488] border-t-transparent rounded-full mx-auto mb-1"></div>
                                <p class="text-[10px] text-[#94a3b8]">{{ __('doctor.exam_selection.analyzing') }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Notes -->
                    <div class="bg-white border border-[#e2e8f0] rounded-2xl p-5 shadow-xs">
                        <label class="text-xs font-bold text-[#64748b] uppercase tracking-widest block mb-2">
                            {{ __('doctor.clinical_notes') }}
                        </label>
                        <textarea
                            id="clinicalNotes"
                            rows="3"
                            placeholder="{{ __('doctor.exam_selection.notes_placeholder') }}"
                            class="w-full border border-[#e2e8f0] rounded-xl p-3 text-xs outline-none focus:ring-2 focus:ring-[#0066FF]/20 focus:border-[#0066FF] transition text-[#1e293b] font-semibold bg-white resize-none"
                        ></textarea>
                    </div>

                    <!-- Actions -->
                    <div class="space-y-2.5">
                        <button
                            id="confirmBtn"
                            disabled
                            class="w-full py-3 rounded-xl bg-[#0066FF] hover:bg-[#0052CC] text-white font-bold transition transform hover:scale-[1.02] active:scale-[0.98] disabled:opacity-45 disabled:scale-100 text-xs uppercase tracking-wider shadow-md cursor-pointer"
                        >
                            {{ __('doctor.exam_selection.confirm_prescription') }}
                        </button>

                        <button
                            id="saveAsGroupBtn"
                            disabled
                            class="w-full py-3 rounded-xl bg-white border border-[#0066FF]/30 hover:bg-[#EFF6FF]/40 text-[#0066FF] font-bold transition transform hover:scale-[1.02] active:scale-[0.98] disabled:opacity-45 disabled:scale-100 text-xs uppercase tracking-wider shadow-xs cursor-pointer"
                        >
                            {{ __('doctor.exam_selection.save_as_group_btn') }}
                        </button>

                        <button
                            id="cancelBtn"
                            class="w-full py-3 rounded-xl bg-[#f1f5f9] hover:bg-[#e2e8f0] text-[#64748b] font-bold transition border border-[#e2e8f0] text-xs uppercase tracking-wider cursor-pointer"
                        >
                            {{ __('common.cancel') }}
                        </button>
                    </div>

                </div>

            </div>

        </div>
    </div>

    <!-- GROUP DETAILS PREVIEW MODAL -->
    <div id="groupDetailsModal" class="hidden fixed inset-0 bg-black/40 backdrop-blur-sm z-50 flex items-center justify-center p-4">
        <div class="glass-card rounded-[20px] w-full max-w-md p-8 shadow-2xl">
            <div class="flex justify-between items-center mb-6 pb-4 border-b border-[#e2e8f0]/80">
                <h3 class="text-base font-bold text-[#1e293b]">
                    {{ __('doctor.exam_selection.group_details') }}
                </h3>
                <button class="closeGroupDetailsModalBtn text-[#94a3b8] hover:text-[#1e293b] text-xl cursor-pointer">
                    ×
                </button>
            </div>

            <div class="space-y-4 text-xs">
                <div>
                    <span class="text-[#64748b] font-medium block">{{ __('common.name') }} :</span>
                    <span id="modalGroupName" class="font-bold text-sm text-[#1e293b]"></span>
                </div>

                <div>
                    <span class="text-[#64748b] font-medium block">{{ __('doctor.description') }} :</span>
                    <div id="modalGroupDesc" class="p-3 bg-[#F8FAFC] border border-[#e2e8f0] rounded-xl text-[#64748b] leading-relaxed mt-1"></div>
                </div>

                <div>
                    <span class="text-[#64748b] font-medium block mb-2">{{ __('doctor.exam_selection.included_exams') }} :</span>
                    <div id="modalGroupExamsList" class="space-y-1.5 max-h-48 overflow-y-auto pr-1"></div>
                </div>
            </div>

            <div class="flex gap-3 mt-6 pt-4 border-t border-[#e2e8f0]/80">
                <button
                    id="modalApplyGroupBtn"
                    class="flex-1 bg-[#0066FF] hover:bg-[#0052CC] text-white rounded-xl py-2.5 font-bold transition transform hover:scale-[1.02] active:scale-[0.98] text-xs uppercase tracking-wider shadow-md cursor-pointer"
                >
                    {{ __('doctor.exam_selection.apply_group') }}
                </button>
                <button
                    class="closeGroupDetailsModalBtn flex-1 bg-[#f1f5f9] hover:bg-[#e2e8f0] text-[#64748b] font-bold rounded-xl py-2.5 transition border border-[#e2e8f0] text-xs uppercase tracking-wider cursor-pointer"
                >
                    {{ __('common.close') }}
                </button>
            </div>
        </div>
    </div>

    <!-- CONFIRMATION MODAL -->
    <div id="confirmationModal" class="hidden fixed inset-0 bg-black/40 backdrop-blur-sm z-50 flex items-center justify-center p-4">
        <div class="glass-card rounded-[20px] max-w-md w-full p-8 shadow-2xl">
            <h3 class="text-base font-bold text-[#1e293b] mb-4 pb-3 border-b border-[#e2e8f0]/80">
                {{ __('doctor.exam_selection.confirm_prescription') }}
            </h3>

            <div id="confirmationExamsList" class="space-y-1.5 max-h-60 overflow-y-auto pr-1"></div>

            <div class="flex gap-3 mt-6 pt-4 border-t border-[#e2e8f0]/80">
                <button
                    id="confirmSubmitBtn"
                    class="flex-1 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl py-2.5 font-bold transition transform hover:scale-[1.02] active:scale-[0.98] text-xs uppercase tracking-wider shadow-md cursor-pointer"
                >
                    {{ __('common.confirm') }}
                </button>
                <button
                    class="closeModalBtn flex-1 bg-[#f1f5f9] hover:bg-[#e2e8f0] text-[#64748b] font-bold rounded-xl py-2.5 transition border border-[#e2e8f0] text-xs uppercase tracking-wider cursor-pointer"
                >
                    {{ __('common.cancel') }}
                </button>
            </div>
        </div>
    </div>

    <!-- SAVE AS GROUP MODAL -->
    <div id="saveGroupModal" class="hidden fixed inset-0 bg-black/40 backdrop-blur-sm z-50 flex items-center justify-center p-4">
        <div class="glass-card rounded-[20px] max-w-md w-full p-8 shadow-2xl">
            <div class="flex justify-between items-center mb-6 pb-4 border-b border-[#e2e8f0]/80">
                <h3 class="text-base font-bold text-[#1e293b]">
                    {{ __('doctor.exam_selection.save_as_group') }}
                </h3>
                <button class="closeSaveGroupModalBtn text-[#94a3b8] hover:text-[#1e293b] text-xl cursor-pointer">
                    ×
                </button>
            </div>

            <div class="space-y-4">
                <div>
                    <label class="text-[10px] font-bold text-[#64748b] uppercase tracking-widest block mb-1.5">{{ __('doctor.group_name') }} *</label>
                    <input
                        id="saveGroupName"
                        type="text"
                        placeholder="{{ __('doctor.exam_selection.group_name_placeholder') }}"
                        maxlength="255"
                        required
                        class="w-full border border-[#e2e8f0] rounded-xl p-3 text-xs outline-none focus:ring-2 focus:ring-[#0066FF]/20 focus:border-[#0066FF] transition text-[#1e293b] font-semibold bg-white"
                    >
                </div>
                <div>
                    <label class="text-[10px] font-bold text-[#64748b] uppercase tracking-widest block mb-1.5">{{ __('doctor.description') }}</label>
                    <textarea
                        id="saveGroupDesc"
                        rows="3"
                        maxlength="500"
                        placeholder="{{ __('doctor.exam_selection.desc_placeholder') }}"
                        class="w-full border border-[#e2e8f0] rounded-xl p-3 text-xs outline-none focus:ring-2 focus:ring-[#0066FF]/20 focus:border-[#0066FF] transition text-[#1e293b] font-semibold bg-white resize-none"
                    ></textarea>
                </div>
                <div id="saveGroupExamsPreview" class="text-xs text-[#64748b]"></div>
            </div>

            <div class="flex gap-3 mt-6 pt-4 border-t border-[#e2e8f0]/80">
                <button
                    id="confirmSaveGroupBtn"
                    class="flex-1 bg-[#0066FF] hover:bg-[#0052CC] text-white rounded-xl py-2.5 font-bold transition transform hover:scale-[1.02] active:scale-[0.98] text-xs uppercase tracking-wider shadow-md cursor-pointer"
                >
                    {{ __('common.save') }}
                </button>
                <button
                    class="closeSaveGroupModalBtn flex-1 bg-[#f1f5f9] hover:bg-[#e2e8f0] text-[#64748b] font-bold rounded-xl py-2.5 transition border border-[#e2e8f0] text-xs uppercase tracking-wider cursor-pointer"
                >
                    {{ __('common.cancel') }}
                </button>
            </div>
        </div>
    </div>

    <script>
        const patientId = {{ $patient->id }};
        let selectedExamIds = [];
        let activeGroupId = null;
        let activeGroupName = "";
        let isSubmitting = false;

        /* TIER 1.4 — Smart Suggestions */
        async function loadSmartSuggestions() {
            const el = document.getElementById('smartSuggestionsList');
            try {
                const params = new URLSearchParams();
                selectedExamIds.forEach(id => params.append('exam_ids[]', id));
                const res = await fetch(`/doctor/api/smart-suggestions/${patientId}?${params.toString()}`);
                const data = await res.json();
                if (!data.success || data.suggestions.length === 0) {
                    el.innerHTML = '<p class="text-[10px] text-[#94a3b8] italic text-center py-2">@lang('doctor.exam_selection.no_suggestions')</p>';
                    return;
                }
                el.innerHTML = data.suggestions.map(s => {
                    const typeColor = s.type === 'follow_up' ? 'amber' : (s.type === 'age_based' ? 'purple' : 'teal');
                    const typeLabel = s.type === 'follow_up' ? "@lang('doctor.suggestion_follow_up')" : (s.type === 'age_based' ? "@lang('doctor.suggestion_age')" : "@lang('doctor.suggestion_prevention')");
                    return `
                        <div class="p-3 bg-gradient-to-r from-[#0D9488]/5 to-transparent border border-[#0D9488]/15 rounded-xl">
                            <div class="flex items-center gap-2 mb-1">
                                <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[8px] font-bold uppercase bg-[#0D9488]/10 text-[#0D9488]">${typeLabel}</span>
                                <span class="text-[10px] font-bold text-[#1e293b]">${s.exam_name}</span>
                            </div>
                            <p class="text-[10px] text-[#64748b] leading-relaxed">${s.reason}</p>
                            <button type="button" onclick="addSuggestedExam(${s.exam_id})"
                                class="mt-1.5 text-[10px] font-bold text-[#0D9488] hover:text-[#0a7068] transition cursor-pointer">
                                + @lang('doctor.exam_selection.add_suggestion')
                            </button>
                        </div>`;
                }).join('');
            } catch(e) {
                el.innerHTML = '<p class="text-[10px] text-[#94a3b8] italic text-center py-2">@lang('doctor.exam_selection.load_error')</p>';
            }
        }

        function addSuggestedExam(examId) {
            const checkbox = document.querySelector(`.exam-checkbox[value="${examId}"]`);
            if (checkbox && !checkbox.checked) {
                checkbox.checked = true;
                checkbox.dispatchEvent(new Event('change'));
            }
            loadSmartSuggestions();
        }

        loadSmartSuggestions();

        /* SEARCH */
        const searchInput = document.getElementById('searchExams');
        const clearBtn = document.getElementById('clearSearchBtn');

        searchInput.addEventListener('input', () => {
            const value = searchInput.value.toLowerCase();
            document.querySelectorAll('.exam-item').forEach(card => {
                card.style.display = card.innerText.toLowerCase().includes(value) ? "" : "none";
            });
            clearBtn.classList.toggle("hidden", value.length === 0);
        });

        clearBtn.onclick = () => {
            searchInput.value = "";
            searchInput.dispatchEvent(new Event('input'));
        };

        /* CHECKBOXES */
        document.querySelectorAll('.exam-checkbox').forEach(box => {
            box.addEventListener('change', () => {
                const card = document.getElementById(`exam_card_${box.dataset.id}`);
                if (box.checked) {
                    card.classList.add("border-[#0066FF]", "bg-[#EFF6FF]/40");
                } else {
                    card.classList.remove("border-[#0066FF]", "bg-[#EFF6FF]/40");
                }
                updateCount();
            });
        });

        function updateCount() {
            selectedExamIds = [...document.querySelectorAll('.exam-checkbox:checked')].map(x => x.value);
            document.getElementById('selectedCount').innerText = selectedExamIds.length;
            document.getElementById('confirmBtn').disabled = selectedExamIds.length === 0;
            document.getElementById('saveAsGroupBtn').disabled = selectedExamIds.length === 0;
            clearTimeout(window._suggestionDebounce);
            window._suggestionDebounce = setTimeout(loadSmartSuggestions, 500);
        }

        /* CONFIRM MODAL */
        document.getElementById('confirmBtn').onclick = () => {
            const list = document.getElementById('confirmationExamsList');
            list.innerHTML = "";

            selectedExamIds.forEach(id => {
                const checkbox = document.querySelector(`.exam-checkbox[value="${id}"]`);
                const name = checkbox.closest('label').querySelector('h3').innerText;
                list.innerHTML += `
                    <div class="p-2.5 bg-[#F8FAFC] border border-[#e2e8f0] rounded-lg font-semibold text-[#1e293b] flex items-center gap-2">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                        ${name}
                    </div>
                `;
            });

            document.getElementById('confirmationModal').classList.remove('hidden');
        };

        /* CLOSE BUTTONS */
        document.querySelectorAll('.closeModalBtn').forEach(btn => {
            btn.onclick = () => {
                document.getElementById('confirmationModal').classList.add('hidden');
            };
        });

        document.querySelectorAll('.closeGroupDetailsModalBtn').forEach(btn => {
            btn.onclick = () => {
                document.getElementById('groupDetailsModal').classList.add('hidden');
            };
        });

        /* GROUP PREVIEW */
        document.querySelectorAll('.quickGroupBtn').forEach(btn => {
            btn.onclick = () => {
                activeGroupId = btn.dataset.groupId;
                activeGroupName = btn.dataset.groupName;

                document.getElementById('modalGroupName').innerText = btn.dataset.groupName;
                document.getElementById('modalGroupDesc').innerText = btn.dataset.groupDesc;

                const exams = JSON.parse(btn.dataset.groupExams);
                document.getElementById('modalGroupExamsList').innerHTML = exams.map(e => `
                    <div class="p-2 bg-[#F8FAFC] border border-[#e2e8f0] rounded-lg font-semibold text-[#1e293b] flex items-center gap-2">
                        <span class="w-1.5 h-1.5 rounded-full bg-[#0066FF]"></span>
                        ${e}
                    </div>
                `).join('');

                document.getElementById('groupDetailsModal').classList.remove('hidden');
            };
        });

        /* APPLY GROUP */
        document.getElementById('modalApplyGroupBtn').onclick = async () => {
            if (isSubmitting) return;
            const result = await Swal.fire({
                title: "@lang('doctor.exam_selection.apply_group')",
                text: "@lang('doctor.exam_selection.apply_group_confirm')".replace(':name', activeGroupName),
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#0066FF',
                cancelButtonColor: '#94a3b8',
                confirmButtonText: "@lang('doctor.yes_prescribe')",
                cancelButtonText: "@lang('common.cancel')"
            });
            if (!result.isConfirmed) return;
            lockUI();
            await sendGroupRequest();
        };

        async function sendGroupRequest() {
            try {
                const response = await fetch('{{ route('doctor.apply-exam-group') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({
                        patient_id: patientId,
                        exam_group_id: activeGroupId,
                        clinical_notes: document.getElementById('clinicalNotes')?.value || ''
                    })
                });

                const data = await response.json();
                if (data.success) {
                    window.location.href = '{{ route('doctor.dashboard') }}';
                } else {
                    Swal.fire({ icon: 'error', title: "@lang('doctor.error')", text: data.message || "@lang('common.error_generic')", confirmButtonColor: '#0066FF' });
                    unlockUI();
                }
            } catch (e) {
                console.error(e);
                Swal.fire({ icon: 'error', title: "@lang('doctor.error')", text: "@lang('doctor.error_retry')", confirmButtonColor: '#0066FF' });
                unlockUI();
            }
        }

        /* CONFIRM REQUEST */
        document.getElementById('confirmSubmitBtn').onclick = async () => {
            if (isSubmitting) return;
            lockUI();

            try {
                const response = await fetch('{{ route('doctor.create-exam-request') }}', {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                        "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({
                        patient_id: patientId,
                        exam_ids: selectedExamIds,
                        clinical_notes: document.getElementById('clinicalNotes').value
                    })
                });

                const data = await response.json();
                if (data.success) {
                    window.location.href = '{{ route('doctor.dashboard') }}';
                }
            } catch (e) {
                Swal.fire({ icon: 'error', title: "@lang('doctor.error')", text: "@lang('common.error_generic')", confirmButtonColor: '#0066FF' });
                unlockUI();
            }
        };

        function lockUI() {
            isSubmitting = true;
            document.querySelectorAll("button,input,textarea").forEach(x => x.disabled = true);
        }

        function unlockUI() {
            isSubmitting = false;
            document.querySelectorAll("button,input,textarea").forEach(x => x.disabled = false);
        }

        /* SAVE AS GROUP */
        document.getElementById('saveAsGroupBtn').addEventListener('click', () => {
            const list = document.getElementById('saveGroupExamsPreview');
            list.innerHTML = '<span class="font-bold text-[#1e293b]">' + selectedExamIds.length + '</span> ' + "@lang('doctor.exam_selection.will_be_included')";
            document.getElementById('saveGroupName').value = '';
            document.getElementById('saveGroupDesc').value = '';
            document.getElementById('saveGroupModal').classList.remove('hidden');
        });

        document.querySelectorAll('.closeSaveGroupModalBtn').forEach(btn => {
            btn.onclick = () => document.getElementById('saveGroupModal').classList.add('hidden');
        });

        document.getElementById('confirmSaveGroupBtn').onclick = async () => {
            const name = document.getElementById('saveGroupName').value.trim();
            if (!name) {
                document.getElementById('saveGroupName').focus();
                return;
            }
            if (isSubmitting) return;
            lockUI();

            try {
                const response = await fetch('{{ route('doctor.api.store-exam-group') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({
                        name: name,
                        description: document.getElementById('saveGroupDesc').value.trim(),
                        exam_ids: selectedExamIds.map(Number)
                    })
                });

                const data = await response.json();
                if (data.success) {
                    Swal.fire({ icon: 'success', title: "@lang('doctor.success')", text: data.message, confirmButtonColor: '#0066FF' }).then(() => {
                        try { sessionStorage.setItem('pendingExamSelection', JSON.stringify(selectedExamIds)); } catch (e) {}
                        window.location.reload();
                    });
                } else {
                    Swal.fire({ icon: 'error', title: "@lang('doctor.error')", text: data.message || "@lang('common.error_generic')", confirmButtonColor: '#0066FF' });
                    unlockUI();
                }
            } catch (e) {
                Swal.fire({ icon: 'error', title: "@lang('doctor.error')", text: "@lang('doctor.exam_selection.group_error')", confirmButtonColor: '#0066FF' });
                unlockUI();
            }
        };

        document.getElementById('cancelBtn').onclick = () => {
            window.location.href = '{{ route('doctor.dashboard') }}';
        };

        // Restore exam selection after saving a group (page reload)
        try {
            const pending = sessionStorage.getItem('pendingExamSelection');
            if (pending) {
                sessionStorage.removeItem('pendingExamSelection');
                JSON.parse(pending).forEach(id => {
                    const cb = document.querySelector(`.exam-checkbox[value="${id}"]`);
                    if (cb) {
                        cb.checked = true;
                        cb.dispatchEvent(new Event('change'));
                    }
                });
            }
        } catch (e) {}

        updateCount();
    </script>
    @endsection
</x-layouts.doctor>