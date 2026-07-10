<x-layouts.auth>
    <meta name="title" content="Groupes d'Examens – Medix eSanté">

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
        .exam-checkbox-label input:checked ~ div span {
            color: #0066FF;
        }
        .group-card {
            background: white;
            border: 1px solid rgba(226, 232, 240, 0.8);
            border-radius: 1rem;
            padding: 1rem 1.25rem;
            transition: border-color 0.2s, box-shadow 0.2s;
        }
        .group-card:hover {
            border-color: rgba(0, 102, 255, 0.2);
            box-shadow: 0 2px 12px -2px rgba(0, 102, 255, 0.08);
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
        .btn-primary:active { transform: translateY(0); }
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
        .btn-danger {
            background: #FEF2F2;
            color: #dc2626;
            font-weight: 700;
            font-size: 0.7rem;
            padding: 0.45rem 0.75rem;
            border-radius: 0.5rem;
            border: 1px solid #FECACA;
            cursor: pointer;
            transition: background 0.2s;
            display: inline-flex;
            align-items: center;
            gap: 0.25rem;
            text-transform: uppercase;
            letter-spacing: 0.04em;
        }
        .btn-danger:hover { background: #FEE2E2; }
        .btn-edit {
            background: #EFF6FF;
            color: #0066FF;
            font-weight: 700;
            font-size: 0.7rem;
            padding: 0.45rem 0.75rem;
            border-radius: 0.5rem;
            border: 1px solid #BFDBFE;
            cursor: pointer;
            transition: background 0.2s;
            display: inline-flex;
            align-items: center;
            gap: 0.25rem;
            text-transform: uppercase;
            letter-spacing: 0.04em;
            text-decoration: none;
        }
        .btn-edit:hover { background: #DBEAFE; }

        .section-label {
            font-size: 0.65rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.1em;
            color: #64748b;
        }
        .badge {
            font-size: 0.6rem;
            font-weight: 700;
            padding: 0.2rem 0.5rem;
            border-radius: 999px;
            background: rgba(0, 102, 255, 0.1);
            color: #0066FF;
            border: 1px solid rgba(0, 102, 255, 0.2);
        }

        @keyframes fadeSlideIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .animate-in { animation: fadeSlideIn 0.35s ease both; }
    </style>

    <div class="w-full max-w-[1200px] mx-auto py-4 md:py-8 space-y-6 animate-in">

        {{-- ===== HEADER ===== --}}
        <div class="glass-card rounded-[20px] px-6 py-5 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div class="flex items-center gap-4">
                <a href="{{ route('doctor.dashboard') }}" class="w-9 h-9 flex items-center justify-center rounded-xl bg-[#f1f5f9] hover:bg-[#e2e8f0] border border-[#e2e8f0] transition cursor-pointer text-[#64748b]">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/>
                    </svg>
                </a>
                <div>
                    <h1 class="text-lg font-bold text-[#1e293b] tracking-tight">Groupes d'Examens</h1>
                    <p class="text-xs text-[#64748b] mt-0.5">Créez et gérez vos groupes d'examens personnalisés</p>
                </div>
            </div>
            <div class="flex items-center gap-2">
                <span class="badge text-[11px] px-3 py-1">
                    {{ $examGroups->count() }} groupe(s)
                </span>
                @if(!$editGroup)
                <a href="{{ route('doctor.exam-groups.create') }}" class="btn-primary">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
                    </svg>
                    Nouveau groupe
                </a>
                @endif
            </div>
        </div>

        {{-- Flash messages --}}
        @if(session('success'))
            <div class="glass-card rounded-[16px] px-5 py-3.5 border-l-4 border-emerald-500 flex items-center gap-3">
                <svg class="w-4 h-4 text-emerald-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                </svg>
                <p class="text-xs font-semibold text-[#1e293b]">{{ session('success') }}</p>
            </div>
        @endif
        @if(session('error'))
            <div class="glass-card rounded-[16px] px-5 py-3.5 border-l-4 border-red-500 flex items-center gap-3">
                <svg class="w-4 h-4 text-red-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                </svg>
                <p class="text-xs font-semibold text-[#1e293b]">{{ session('error') }}</p>
            </div>
        @endif

        <div class="grid grid-cols-1 {{ $editGroup ? 'lg:grid-cols-5' : '' }} gap-6">

            {{-- ===== LEFT: EDIT FORM ===== --}}
            @if($editGroup)
            <div class="lg:col-span-2">
                {{-- EDIT FORM --}}
                <div class="glass-card rounded-[20px] p-6">
                    <div class="flex items-center gap-3 mb-5 pb-4 border-b border-[#e2e8f0]">
                        <div class="w-8 h-8 rounded-xl bg-[#0066FF]/10 flex items-center justify-center">
                            <svg class="w-4 h-4 text-[#0066FF]" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                            </svg>
                        </div>
                        <h2 class="text-sm font-bold text-[#1e293b]">Modifier le groupe</h2>
                    </div>

                    <form action="{{ route('doctor.exam-groups.update', $editGroup->id) }}" method="POST" class="space-y-4">
                        @csrf
                        @method('PUT')
                        <div>
                            <label class="section-label block mb-1.5">Nom du groupe</label>
                            <input type="text" name="name" class="form-input" value="{{ old('name', $editGroup->name) }}" required/>
                            @error('name')<p class="text-red-500 text-[10px] mt-1 font-semibold">{{ $message }}</p>@enderror
                        </div>

                        <div>
                            <label class="section-label block mb-1.5">Description</label>
                            <textarea name="description" rows="2" class="form-input resize-none">{{ old('description', $editGroup->description) }}</textarea>
                        </div>

                        <div>
                            <label class="section-label block mb-2">Examens sélectionnés</label>
                            <div class="relative mb-2">
                                <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-3.5 h-3.5 text-[#94a3b8]" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                                </svg>
                                <input type="text" id="editSearchInput" class="form-input pl-9" placeholder="Filtrer les examens..."/>
                            </div>
                            @error('exam_ids')<p class="text-red-500 text-[10px] mb-1 font-semibold">{{ $message }}</p>@enderror
                            @php $editGroupExamIds = $editGroup->items->pluck('exam_id')->toArray(); @endphp
                            <div class="border border-[#e2e8f0] rounded-xl p-2.5 max-h-[220px] overflow-y-auto space-y-1.5 bg-[#F8FAFC]" id="editExamList">
                                @forelse($exams as $exam)
                                    <label class="exam-checkbox-label edit-exam-item">
                                        <input type="checkbox" name="exam_ids[]" value="{{ $exam->id }}" class="mt-0.5 accent-[#0066FF]"
                                            @if(in_array($exam->id, $editGroupExamIds)) checked @endif
                                        />
                                        <div class="text-[11px] min-w-0">
                                            <span class="font-bold text-[#1e293b] block edit-exam-name">{{ $exam->name }}</span>
                                            @if($exam->category)
                                                <span class="text-[9px] text-[#64748b] font-semibold uppercase">{{ $exam->category }}</span>
                                            @endif
                                        </div>
                                    </label>
                                @empty
                                    <p class="text-center text-[11px] text-[#94a3b8] py-4 italic">Aucun examen disponible</p>
                                @endforelse
                            </div>
                        </div>

                        <div class="flex gap-2 pt-2">
                            <button type="submit" class="btn-primary flex-1 justify-center">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                                </svg>
                                Enregistrer
                            </button>
                            <a href="{{ route('doctor.exam-groups.index') }}" class="btn-secondary">Annuler</a>
                        </div>
                    </form>
                </div>
            </div>
            @endif

            {{-- ===== LIST ===== --}}
            <div class="{{ $editGroup ? 'lg:col-span-3' : '' }}">
                <div class="glass-card rounded-[20px] p-6">
                    <div class="flex items-center justify-between mb-5 pb-4 border-b border-[#e2e8f0]">
                        <h2 class="text-xs font-bold text-[#64748b] uppercase tracking-widest flex items-center gap-2">
                            <svg class="w-4 h-4 text-[#0066FF]" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                            </svg>
                            Mes groupes
                        </h2>
                        {{-- Search across list --}}
                        <div class="relative">
                            <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-3 h-3 text-[#94a3b8]" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                            </svg>
                            <input type="text" id="listSearchInput" placeholder="Rechercher..." class="pl-7 pr-3 py-1.5 text-[11px] font-semibold border border-[#e2e8f0] rounded-lg outline-none focus:border-[#0066FF] transition w-[160px]"/>
                        </div>
                    </div>

                    @if($examGroups->count() > 0)
                        <div class="space-y-3 max-h-[600px] overflow-y-auto pr-1" id="groupListContainer">
                            @foreach($examGroups as $group)
                                <div class="group-card group-list-item" data-name="{{ strtolower($group->name) }}">
                                    <div class="flex items-start justify-between gap-3">
                                        <div class="min-w-0 flex-1">
                                            <div class="flex items-center gap-2 flex-wrap">
                                                <p class="font-bold text-sm text-[#1e293b]">{{ $group->name }}</p>
                                                <span class="badge">{{ $group->items->count() }} examen(s)</span>
                                            </div>
                                            <p class="text-[11px] text-[#64748b] mt-1 truncate">
                                                {{ $group->description ?: 'Aucune description' }}
                                            </p>
                                            {{-- Exam tags --}}
                                            @if($group->items->count() > 0)
                                                <div class="flex flex-wrap gap-1 mt-2">
                                                    @foreach($group->items->take(5) as $item)
                                                        @if($item->exam)
                                                            <span class="text-[9px] font-bold bg-[#F8FAFC] border border-[#e2e8f0] text-[#64748b] px-2 py-0.5 rounded-full">
                                                                {{ $item->exam->name }}
                                                            </span>
                                                        @endif
                                                    @endforeach
                                                    @if($group->items->count() > 5)
                                                        <span class="text-[9px] font-bold bg-[#F8FAFC] border border-[#e2e8f0] text-[#94a3b8] px-2 py-0.5 rounded-full">
                                                            +{{ $group->items->count() - 5 }} autres
                                                        </span>
                                                    @endif
                                                </div>
                                            @endif
                                        </div>

                                        <div class="flex items-center gap-1.5 flex-shrink-0 self-start">
                                            <a href="{{ route('doctor.exam-groups.edit', $group->id) }}" class="btn-edit">
                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                                </svg>
                                                Modifier
                                            </a>
                                            <form action="{{ route('doctor.exam-groups.destroy', $group->id) }}" method="POST" class="inline" onsubmit="return confirm('Supprimer définitivement « {{ addslashes($group->name) }} » ?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn-danger">
                                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                                    </svg>
                                                    Supprimer
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                    <p class="text-[9px] text-[#94a3b8] font-semibold mt-2 pt-2 border-t border-[#f1f5f9]">
                                        Créé le {{ $group->created_at->format('d/m/Y') }}
                                    </p>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-16 border border-dashed border-[#cbd5e1] rounded-xl">
                            <div class="w-14 h-14 rounded-2xl bg-[#f1f5f9] flex items-center justify-center mx-auto mb-4">
                                <svg class="w-7 h-7 text-[#94a3b8]" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                                </svg>
                            </div>
                            <p class="text-xs font-semibold text-[#94a3b8]">Aucun groupe créé pour le moment.</p>
                            <a href="{{ route('doctor.exam-groups.create') }}" class="inline-flex items-center gap-1.5 mt-3 btn-primary">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
                                </svg>
                                Créer un groupe
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <script>
        // ——— Search inside EDIT exam list ———
        const editSearchInput = document.getElementById('editSearchInput');
        if (editSearchInput) {
            editSearchInput.addEventListener('input', () => {
                const q = editSearchInput.value.toLowerCase();
                document.querySelectorAll('.edit-exam-item').forEach(el => {
                    const name = el.querySelector('.edit-exam-name')?.textContent.toLowerCase() ?? '';
                    el.style.display = name.includes(q) ? '' : 'none';
                });
            });
        }

        // ——— Search through list cards ———
        const listSearchInput = document.getElementById('listSearchInput');
        if (listSearchInput) {
            listSearchInput.addEventListener('input', () => {
                const q = listSearchInput.value.toLowerCase();
                document.querySelectorAll('.group-list-item').forEach(card => {
                    card.style.display = card.dataset.name.includes(q) ? '' : 'none';
                });
            });
        }
    </script>
</x-layouts.auth>
