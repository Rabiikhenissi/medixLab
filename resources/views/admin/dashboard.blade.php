@extends('layouts.admin')

@section('title', 'Espace Administrateur')

@section('page-title')
Espace <span style="color:#0066ff;">Administrateur</span>
@endsection

@section('page-subtitle', 'Gerez la plateforme et supervisez les examens medicaux disponibles.')

@section('content')
    <!-- Stats Cards -->
    <div class="stats-grid">
        <div class="stat-card anim anim-1">
            <div class="stat-icon blue">
                <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9.75 3.104v5.714a2.25 2.25 0 01-.659 1.591L5 14.5M9.75 3.104c-.251.023-.501.05-.75.082m.75-.082a24.301 24.301 0 014.5 0m0 0v5.714c0 .597.237 1.17.659 1.591L19.8 15.3M14.25 3.104c.251.023.501.05.75.082M19.8 15.3l-1.57.393A9.065 9.065 0 0112 15a9.065 9.065 0 00-6.23.693L5 14.5m14.8.8l1.402 1.402c1.232 1.232.65 3.318-1.067 3.611A48.309 48.309 0 0112 21c-2.773 0-5.491-.235-8.135-.687-1.718-.293-2.3-2.379-1.067-3.61L5 14.5" />
                </svg>
            </div>
            <div class="stat-info">
                <div class="stat-label">En Attente (Actifs)</div>
                <div class="stat-value">{{ $stats['total_exams'] }}</div>
            </div>
        </div>

        <div class="stat-card anim anim-2">
            <div class="stat-icon green">
                <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z" />
                </svg>
            </div>
            <div class="stat-info">
                <div class="stat-label">Patients Inscrits</div>
                <div class="stat-value">{{ $stats['total_patients'] }}</div>
            </div>
        </div>

        <div class="stat-card anim anim-3">
            <div class="stat-icon orange">
                <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M20.25 7.5l-.625 10.632a2.25 2.25 0 01-2.247 2.118H6.622a2.25 2.25 0 01-2.247-2.118L3.75 7.5M10 11.25h4M3.375 7.5h17.25c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125z" />
                </svg>
            </div>
            <div class="stat-info">
                <div class="stat-label">Total Examens</div>
                <div class="stat-value">{{ $stats['total_exams'] + $stats['archived_exams'] }}</div>
            </div>
        </div>
    </div>

    <!-- Exam Management Table -->
    <div class="data-section anim anim-4">
        <!-- Table Header -->
        <div class="data-header">
            <div>
                <div class="data-title">Catalogue des Examens</div>
            </div>
        </div>

        <!-- Filters + Add Button -->
        <form method="GET" action="{{ route('admin.dashboard') }}" id="filter-form">
            <div class="filters-bar">
                <!-- Category -->
                <div>
                    <span class="filter-label">Categorie</span>
                    <div class="filter-group" style="position:relative;display:inline-block;">
                        <select name="category" class="filter-select" onchange="document.getElementById('filter-form').submit()">
                            <option value="">Toutes les categories</option>
                            <option value="biochemistry" {{ $selectedCategory === 'biochemistry' ? 'selected' : '' }}>Biochimie</option>
                            <option value="hematology"   {{ $selectedCategory === 'hematology'   ? 'selected' : '' }}>Hematologie</option>
                            <option value="microbiology" {{ $selectedCategory === 'microbiology' ? 'selected' : '' }}>Microbiologie</option>
                            <option value="immunology"   {{ $selectedCategory === 'immunology'   ? 'selected' : '' }}>Immunologie</option>
                            <option value="urinalysis"   {{ $selectedCategory === 'urinalysis'   ? 'selected' : '' }}>Urinalyse</option>
                            <option value="other"        {{ $selectedCategory === 'other'        ? 'selected' : '' }}>Autre</option>
                        </select>
                        <svg class="select-arrow" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5"/></svg>
                    </div>
                </div>

                <!-- Search -->
                <div>
                    <span class="filter-label">Recherche rapide</span>
                    <div class="filter-group" style="position:relative;display:inline-block;">
                        <svg class="search-icon" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" style="position:absolute;left:10px;top:50%;transform:translateY(-50%);width:16px;height:16px;color:#94a3b8;pointer-events:none;">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z"/>
                        </svg>
                        <input type="text" name="search" value="{{ $search }}" placeholder="Rechercher par nom, code..." class="filter-input" style="padding-left:36px;">
                    </div>
                </div>

                <!-- Archived toggle -->
                <div style="align-self:flex-end;">
                    <label class="filter-checkbox-wrap">
                        <input type="checkbox" name="show_archived" value="1" {{ $showArchived ? 'checked' : '' }} onchange="document.getElementById('filter-form').submit()">
                        Afficher archives
                    </label>
                </div>

                <!-- Filter Button -->
                <div style="align-self:flex-end;">
                    <button type="submit" class="btn-filter">Options de filtrage</button>
                </div>

                <!-- Spacer -->
                <div style="flex:1;"></div>

                <!-- ADD EXAM BUTTON -->
                <div style="align-self:flex-end;">
                    <button type="button" onclick="openModal('create')" class="btn-add-exam">
                        <svg fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/>
                        </svg>
                        Ajouter un Examen
                    </button>
                </div>
            </div>
        </form>

        <!-- Table -->
        <table class="data-table">
            <thead>
                <tr>
                    <th>Code</th>
                    <th>Examen</th>
                    <th>Categorie</th>
                    <th>Plage Normale</th>
                    <th>Date Ajout</th>
                    <th>Statut</th>
                    <th style="text-align:right;">Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse($exams as $exam)
                    @php
                        $catLabel = [
                            'biochemistry' => 'Biochimie',
                            'hematology'   => 'Hematologie',
                            'microbiology' => 'Microbiologie',
                            'immunology'   => 'Immunologie',
                            'urinalysis'   => 'Urinalyse',
                            'other'        => 'Autre',
                        ][$exam->category] ?? $exam->category;
                    @endphp
                    <tr class="{{ $exam->is_archive ? 'archived' : '' }}">
                        <td>
                            <span class="exam-code">{{ $exam->code }}</span>
                        </td>
                        <td>
                            <div class="exam-name">{{ $exam->name }}</div>
                            @if($exam->description)
                                <div class="exam-desc">{{ $exam->description }}</div>
                            @endif
                        </td>
                        <td>
                            <span class="category-badge cat-{{ $exam->category }}">{{ $catLabel }}</span>
                        </td>
                        <td style="color:#475569;font-size:13px;">{{ $exam->default_normal_range ?? '—' }}</td>
                        <td style="color:#94a3b8;font-size:12px;white-space:nowrap;">{{ $exam->created_at ? $exam->created_at->format('d/m/Y') : '—' }}</td>
                        <td>
                            @if($exam->is_archive)
                                <span class="status-badge status-archived"><span class="dot"></span>Archive</span>
                            @else
                                <span class="status-badge status-active"><span class="dot"></span>Actif</span>
                            @endif
                        </td>
                        <td style="text-align:right;">
                            <div style="display:flex;align-items:center;justify-content:flex-end;gap:6px;">
                                <button
                                    class="table-action-btn"
                                    onclick="openModal('edit', {
                                        id: {{ $exam->id }},
                                        code: '{{ addslashes($exam->code) }}',
                                        name: '{{ addslashes($exam->name) }}',
                                        category: '{{ $exam->category }}',
                                        description: `{{ addslashes($exam->description ?? '') }}`,
                                        default_normal_range: '{{ addslashes($exam->default_normal_range ?? '') }}',
                                        preparation_instructions: `{{ addslashes($exam->preparation_instructions ?? '') }}`
                                    })"
                                    title="Modifier">
                                    <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10"/></svg>
                                </button>

                                <form action="{{ route('admin.exams.archive', $exam) }}" method="POST" style="display:inline;margin:0;"
                                      onsubmit="return confirm('{{ $exam->is_archive ? 'Restaurer cet examen ?' :'Archiver cet examen ?' }}')">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="table-action-btn {{ $exam->is_archive ? 'restore-btn' : 'archive-btn' }}" title="{{ $exam->is_archive ? 'Restaurer' : 'Archiver' }}">
                                        @if($exam->is_archive)
                                            <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 15L3 9m0 0l6-6M3 9h12a6 6 0 010 12h-3"/></svg>
                                        @else
                                            <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M20.25 7.5l-.625 10.632a2.25 2.25 0 01-2.247 2.118H6.622a2.25 2.25 0 01-2.247-2.118L3.75 7.5m8.25 3v6.75m0 0l-3-3m3 3l3-3M3.375 7.5h17.25c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125z"/></svg>
                                        @endif
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7">
                            <div class="empty-state">
                                <div class="empty-state-icon">
                                    <svg fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m3.75 9v6m3-3H9m1.5-12H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z"/></svg>
                                </div>
                                <h3>Aucun examen trouve</h3>
                                <p>Utilisez le bouton "Ajouter un Examen" pour commencer.</p>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <!-- Pagination -->
        @if($exams->hasPages())
            <div class="pagination-wrap">
                <div style="display:flex;gap:4px;align-items:center;">
                    @if($exams->onFirstPage())
                        <span style="padding:6px 12px;background:#f1f5f9;color:#94a3b8;border-radius:6px;font-size:13px;cursor:not-allowed;">« Precedent</span>
                    @else
                        <a href="{{ $exams->previousPageUrl() }}" style="padding:6px 12px;background:white;border:1px solid #e2e8f0;color:#374151;border-radius:6px;font-size:13px;font-weight:500;text-decoration:none;">« Precedent</a>
                    @endif

                    @foreach($exams->getUrlRange(max(1, $exams->currentPage()-2), min($exams->lastPage(), $exams->currentPage()+2)) as $page => $url)
                        @if($page == $exams->currentPage())
                            <span style="padding:6px 12px;background:#0066ff;color:white;border-radius:6px;font-size:13px;font-weight:700;">{{ $page }}</span>
                        @else
                            <a href="{{ $url }}" style="padding:6px 12px;background:white;border:1px solid #e2e8f0;color:#374151;border-radius:6px;font-size:13px;font-weight:500;text-decoration:none;">{{ $page }}</a>
                        @endif
                    @endforeach

                    @if($exams->hasMorePages())
                        <a href="{{ $exams->nextPageUrl() }}" style="padding:6px 12px;background:white;border:1px solid #e2e8f0;color:#374151;border-radius:6px;font-size:13px;font-weight:500;text-decoration:none;">Suivant »</a>
                    @else
                        <span style="padding:6px 12px;background:#f1f5f9;color:#94a3b8;border-radius:6px;font-size:13px;cursor:not-allowed;">Suivant »</span>
                    @endif
                </div>
            </div>
        @endif
    </div>

    <!-- MODAL -->
    <div id="exam-modal" style="display:none;position:fixed;inset:0;z-index:1000;">
        <div class="modal-backdrop" onclick="closeModal()">
            <div class="modal-box" id="exam-modal-card" onclick="event.stopPropagation()">

                <!-- Modal Header -->
                <div class="modal-header">
                    <div class="modal-title-wrap">
                        <div class="modal-title" id="modal-title">Nouvel Examen</div>
                        <div class="modal-subtitle">Completez les informations de l'examen</div>
                    </div>
                    <button class="modal-close" onclick="closeModal()">
                        <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                <!-- Modal Body -->
                <div class="modal-body">
                    @if($errors->any())
                        <div class="form-errors">
                            <ul>
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form id="exam-form" method="POST" action="{{ route('admin.exams.store') }}">
                        @csrf
                        <input type="hidden" id="form-method" name="_method" value="POST">

                        <div class="form-row">
                            <div class="form-group">
                                <label class="form-label">Code Examen<span class="required-star">*</span></label>
                                <input type="text" name="code" required placeholder="Ex: HBA1C" class="form-control" style="font-family:'SF Mono','Consolas',monospace;font-weight:600;letter-spacing:0.5px;">
                            </div>
                            <div class="form-group">
                                <label class="form-label">Nom de l'Examen<span class="required-star">*</span></label>
                                <input type="text" name="name" required placeholder="Ex: Hemoglobine Glyquee" class="form-control">
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label class="form-label">Categorie<span class="required-star">*</span></label>
                                <div style="position:relative;">
                                    <select name="category" required class="form-control">
                                        <option value="">Selectionner...</option>
                                        <option value="biochemistry">Biochimie</option>
                                        <option value="hematology">Hematologie</option>
                                        <option value="microbiology">Microbiologie</option>
                                        <option value="immunology">Immunologie</option>
                                        <option value="urinalysis">Urinalyse</option>
                                        <option value="other">Autre</option>
                                    </select>
                                    <svg style="position:absolute;right:10px;top:50%;transform:translateY(-50%);width:14px;height:14px;color:#94a3b8;pointer-events:none;" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5"/></svg>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="form-label">Plage Normale</label>
                                <input type="text" name="default_normal_range" placeholder="Ex: 4.0 - 5.6 %" class="form-control">
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="form-label">Description</label>
                            <textarea name="description" rows="2" placeholder="Description de l'examen..." class="form-control"></textarea>
                        </div>

                        <div class="form-group">
                            <label class="form-label">Instructions de Preparation</label>
                            <textarea name="preparation_instructions" rows="2" placeholder="Ex: Etre a jeun depuis 12h..." class="form-control"></textarea>
                        </div>
                    </form>
                </div>

                <!-- Modal Footer -->
                <div class="modal-footer">
                    <button type="button" onclick="closeModal()" class="btn-cancel">Annuler</button>
                    <button type="submit" form="exam-form" class="btn-submit">
                        <span id="modal-submit-text">Creer l'examen</span>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
        function openModal(mode, examData = null) {
            const modal   = document.getElementById('exam-modal');
            const card    = document.getElementById('exam-modal-card');
            const form    = document.getElementById('exam-form');
            const title   = document.getElementById('modal-title');
            const submit  = document.getElementById('modal-submit-text');
            const method  = document.getElementById('form-method');

            form.reset();

            if (mode === 'create') {
                title.textContent  = 'Nouvel Examen';
                submit.textContent = "Creer l'examen";
                form.action        = '{{ route("admin.exams.store") }}';
                method.value       = 'POST';
            } else if (mode === 'edit' && examData) {
                title.textContent  = "Modifier l'Examen";
                submit.textContent = 'Enregistrer les modifications';
                form.action        = '/admin/exams/' + examData.id;
                method.value       = 'PUT';

                ['code','name','category','description','default_normal_range','preparation_instructions'].forEach(f => {
                    const el = form.querySelector(`[name="${f}"]`);
                    if (el) el.value = examData[f] || '';
                });
            }

            modal.style.display = 'block';
            requestAnimationFrame(() => {
                card.style.transform = 'scale(1)';
                card.style.opacity   = '1';
            });
            document.body.style.overflow = 'hidden';
        }

        function closeModal() {
            const modal = document.getElementById('exam-modal');
            const card  = document.getElementById('exam-modal-card');

            card.style.transform = 'scale(0.95)';
            card.style.opacity   = '0';

            setTimeout(() => {
                modal.style.display  = 'none';
                document.body.style.overflow = '';
            }, 250);
        }

        document.addEventListener('keydown', e => { if (e.key === 'Escape') closeModal(); });

        @if($errors->any())
            document.addEventListener('DOMContentLoaded', () => {
                openModal('{{ old("_method") === "PUT" ? "edit" : "create" }}');
            });
        @endif
    </script>
@endsection
