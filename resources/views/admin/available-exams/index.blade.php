@extends('layouts.admin')

@section('title', 'Examens Disponibles')

@section('page-title')
Examens <span style="color:#0066ff;">Disponibles</span>
@endsection

@section('page-subtitle')
Gérez les examens proposés par chaque laboratoire et leurs tarifs.
@endsection

@section('content')

<style>
    .ae-filters {
        display: flex;
        gap: 12px;
        align-items: end;
        margin-bottom: 20px;
        flex-wrap: wrap;
    }
    .ae-filters .field {
        display: flex;
        flex-direction: column;
        gap: 4px;
    }
    .ae-filters label {
        font-size: 11px;
        font-weight: 700;
        color: #64748b;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    .ae-filters input, .ae-filters select {
        background: #fff;
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        padding: 8px 12px;
        font-size: 13px;
        color: #1e293b;
        font-family: 'Inter', sans-serif;
        outline: none;
    }
    .ae-filters input:focus, .ae-filters select:focus {
        border-color: #0066ff;
    }
    .ae-filters input { width: 220px; }
    .alert-success {
        display: flex; align-items: center; gap: 10px;
        background: #f0fdf4; border: 1px solid #bbf7d0; border-radius: 10px;
        padding: 12px 16px; margin-bottom: 20px; font-size: 13px; color: #166534; font-weight: 500;
    }
    .alert-error {
        display: flex; align-items: center; gap: 10px;
        background: #fff1f2; border: 1px solid #fecaca; border-radius: 10px;
        padding: 12px 16px; margin-bottom: 20px; font-size: 13px; color: #dc2626; font-weight: 500;
    }
    .price-tag {
        font-weight: 800;
        color: #0066ff;
        font-size: 14px;
    }
    .status-dot {
        width: 8px; height: 8px; border-radius: 50%; display: inline-block;
    }
    .status-dot.active { background: #16a34a; }
    .status-dot.inactive { background: #cbd5e1; }
</style>

@if (session('success'))
    <div class="alert-success">{{ session('success') }}</div>
@endif
@if (session('error'))
    <div class="alert-error">{{ session('error') }}</div>
@endif

<div class="data-section anim anim-1">
    <div class="data-header">
        <span class="data-title">{{ $availableExams->total() }} examen(s) disponible(s)</span>
        <a href="{{ route('admin.available-exams.create') }}" class="btn-add-exam">
            <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
            Ajouter
        </a>
    </div>

    <div class="filters-bar">
        <form method="GET" class="ae-filters" style="margin:0;">
            <div class="field">
                <label>Recherche</label>
                <input type="text" name="search" value="{{ $search }}" placeholder="Nom ou code...">
            </div>
            <div class="field">
                <label>Laboratoire</label>
                <select name="labo_id">
                    <option value="">Tous</option>
                    @foreach($labos as $labo)
                        <option value="{{ $labo->id }}" {{ $laboId == $labo->id ? 'selected' : '' }}>{{ $labo->name }}</option>
                    @endforeach
                </select>
            </div>
            <button type="submit" class="btn-filter">Filtrer</button>
        </form>
    </div>

    <table class="data-table">
        <thead>
            <tr>
                <th>Laboratoire</th>
                <th>Examen</th>
                <th>Catégorie</th>
                <th>Prix</th>
                <th>Statut</th>
                <th style="text-align:right;">Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($availableExams as $ae)
                <tr>
                    <td style="font-weight:600; color:#0f172a;">{{ $ae->labo->name }}</td>
                    <td>
                        <span class="exam-code">{{ $ae->exam->code }}</span>
                        <div style="font-weight:600; color:#0f172a; font-size:13px; margin-top:2px;">{{ $ae->exam->name }}</div>
                    </td>
                    <td><span class="category-badge cat-{{ $ae->exam->category }}">{{ ucfirst($ae->exam->category) }}</span></td>
                    <td class="price-tag">{{ number_format($ae->price, 2) }} DT</td>
                    <td>
                        <span class="status-badge {{ $ae->is_active ? 'status-active' : 'status-archived' }}">
                            <span class="dot"></span>
                            {{ $ae->is_active ? 'Actif' : 'Inactif' }}
                        </span>
                    </td>
                    <td style="text-align:right;">
                        <a href="{{ route('admin.available-exams.edit', $ae) }}" class="table-action-btn" title="Modifier">
                            <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931z"/></svg>
                        </a>
                        <form action="{{ route('admin.available-exams.archive', $ae) }}" method="POST" style="display:inline;">
                            @csrf @method('PATCH')
                            <button type="submit" class="table-action-btn {{ $ae->is_archive ? 'restore-btn' : 'archive-btn' }}" title="{{ $ae->is_archive ? 'Restaurer' : 'Archiver' }}">
                                <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M20.25 7.5l-.625 10.632a2.25 2.25 0 01-2.247 2.118H6.622a2.25 2.25 0 01-2.247-2.118L3.75 7.5m8.25 3v6.75m0 0l-3-3m3 3l3-3"/></svg>
                            </button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6">
                        <div class="empty-state">
                            <div class="empty-state-icon">
                                <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M20.25 7.5l-.625 10.632a2.25 2.25 0 01-2.247 2.118H6.622a2.25 2.25 0 01-2.247-2.118L3.75 7.5m16.5 0V6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v10.5"/></svg>
                            </div>
                            <h3>Aucun examen disponible</h3>
                            <p>Commencez par ajouter des examens pour vos laboratoires.</p>
                        </div>
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="pagination-wrap">
        {{ $availableExams->links() }}
    </div>
</div>
@endsection
