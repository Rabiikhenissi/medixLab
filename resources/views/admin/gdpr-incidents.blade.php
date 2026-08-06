@extends('layouts.admin')

@section('title', __('admin.gdpr_incidents.title'))
@section('page-title', __('admin.gdpr_incidents.register_title'))
@section('page-subtitle', __('admin.gdpr_incidents.page_subtitle'))

@section('content')
    <div class="data-section anim anim-1">
        <div class="data-header">
            <div class="data-title">{{ __('admin.gdpr_incidents.declare') }}</div>
        </div>

        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        @if (session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif
        @if ($errors->any())
            <div class="alert alert-danger">
                <ul style="margin:0;padding-left:16px;">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('admin.gdpr.incidents.store') }}" id="incident-form">
            @csrf
            <div class="filters-bar">
                <div>
                    <span class="filter-label">{{ __('admin.gdpr_incidents.type') }}</span>
                    <div class="filter-group">
                        <select name="incident_type" class="filter-input" required>
                            <option value="" disabled selected>{{ __('admin.gdpr_incidents.type_placeholder') }}</option>
                            <option value="data_breach">{{ __('admin.gdpr_incidents.type_data_breach') }}</option>
                            <option value="unauthorized_access">{{ __('admin.gdpr_incidents.type_unauthorized_access') }}</option>
                            <option value="data_loss">{{ __('admin.gdpr_incidents.type_data_loss') }}</option>
                            <option value="privacy_violation">{{ __('admin.gdpr_incidents.type_privacy_violation') }}</option>
                            <option value="other">{{ __('admin.gdpr_incidents.type_other') }}</option>
                        </select>
                    </div>
                </div>
                <div>
                    <span class="filter-label">{{ __('admin.gdpr_incidents.severity') }}</span>
                    <div class="filter-group">
                        <select name="severity" class="filter-input" required>
                            <option value="" disabled selected>{{ __('admin.gdpr_incidents.severity_placeholder') }}</option>
                            <option value="low">{{ __('admin.gdpr_incidents.severity_low') }}</option>
                            <option value="medium">{{ __('admin.gdpr_incidents.severity_medium') }}</option>
                            <option value="high">{{ __('admin.gdpr_incidents.severity_high') }}</option>
                            <option value="critical">{{ __('admin.gdpr_incidents.severity_critical') }}</option>
                        </select>
                    </div>
                </div>
                <div>
                    <span class="filter-label">{{ __('admin.gdpr_incidents.detected_at') }}</span>
                    <div class="filter-group">
                        <input type="date" name="detected_at" class="filter-input" value="{{ old('detected_at', now()->format('Y-m-d')) }}" required>
                    </div>
                </div>
                <div>
                    <span class="filter-label">{{ __('admin.gdpr_incidents.affected_accounts_optional') }}</span>
                    <div class="filter-group">
                        <input type="number" name="affected_users_count" min="0" class="filter-input" value="{{ old('affected_users_count') }}">
                    </div>
                </div>
            </div>

            <div class="filter-group" style="margin-top:12px;width:100%;">
                <textarea name="description" class="filter-input" rows="3" placeholder="{{ __('admin.gdpr_incidents.description_placeholder') }}" required>{{ old('description') }}</textarea>
            </div>

            <div style="margin-top:12px;">
                <button type="submit" class="btn-filter">{{ __('admin.gdpr_incidents.save_incident') }}</button>
            </div>
        </form>
    </div>

    <div class="data-section anim anim-2">
        <div class="data-header">
            <div class="data-title">{{ __('admin.gdpr_incidents.history', ['count' => $incidents->total()]) }}</div>
        </div>

        <table class="data-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>{{ __('admin.gdpr_incidents.type') }}</th>
                    <th>{{ __('admin.gdpr_incidents.severity') }}</th>
                    <th>{{ __('admin.gdpr_incidents.accounts') }}</th>
                    <th>{{ __('admin.gdpr_incidents.detected_on') }}</th>
                    <th>{{ __('admin.gdpr_incidents.description') }}</th>
                    <th>{{ __('admin.gdpr_incidents.authority') }}</th>
                    <th>{{ __('admin.gdpr_incidents.affected') }}</th>
                    <th>{{ __('common.status') }}</th>
                    <th>{{ __('common.actions') }}</th>
                </tr>
            </thead>
            <tbody>
                @forelse($incidents as $incident)
                    <tr>
                        <td>#{{ $incident->id }}</td>
                        <td>{{ ucwords(str_replace('_', ' ', $incident->incident_type)) }}</td>
                        <td>
                            <span class="severity severity-{{ $incident->severity }}">{{ strtoupper($incident->severity) }}</span>
                        </td>
                        <td>{{ $incident->affected_users_count ?? '—' }}</td>
                        <td>{{ $incident->detected_at->format('d/m/Y H:i') }}</td>
                        <td title="{{ $incident->description }}">{{ Str::limit($incident->description ?? '', 80) }}</td>
                        <td>{{ optional($incident->notified_authority_at)->format('d/m/Y') ?? '—' }}</td>
                        <td>{{ optional($incident->notified_affected_at)->format('d/m/Y') ?? '—' }}</td>
                        <td>
                            <span class="status-badge status-{{ $incident->status }}">{{ $incident->status === 'open' ? __('admin.gdpr_incidents.status_open') : __('admin.gdpr_incidents.status_resolved') }}</span>
                        </td>
                        <td>
                            @if ($incident->isOpen())
                                <details>
                                    <summary class="btn-resolve">{{ __('admin.gdpr_incidents.resolve') }}</summary>
                                    <form method="POST" action="{{ route('admin.gdpr.incidents.resolve', $incident) }}" style="margin-top:8px;">
                                        @csrf
                                        <textarea name="resolution" class="filter-input" rows="2" placeholder="{{ __('admin.gdpr_incidents.resolution_placeholder') }}" required></textarea>
                                        <button type="submit" class="btn-filter" style="margin-top:6px;">{{ __('admin.gdpr_incidents.mark_resolved') }}</button>
                                    </form>
                                </details>
                            @else
                                <span class="text-muted" title="{{ $incident->resolution }}">{{ Str::limit($incident->resolution ?? '', 60) }}</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="10" class="text-center">{{ __('admin.gdpr_incidents.no_incidents') }}</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        @if ($incidents->hasPages())
            <div class="pagination-wrap">
                {{ $incidents->links() }}
            </div>
        @endif
    </div>
@endsection

@section('styles')
    <style>
        .alert-success { background: #ecfdf5; color: #047857; border: 1px solid #a7f3d0; padding: 10px 14px; border-radius: 10px; margin-bottom: 16px; }
        .alert-danger { background: #fef2f2; color: #b91c1c; border: 1px solid #fecaca; padding: 10px 14px; border-radius: 10px; margin-bottom: 16px; }
        .filter-label { display: block; font-size: 11px; font-weight: 700; color: #64748b; text-transform: uppercase; letter-spacing: 0.4px; margin-bottom: 6px; }
        .filter-input {
            padding: 8px 12px; border-radius: 10px; border: 1px solid #e2e8f0; background: #fff;
            font-size: 13px; color: #1e293b; outline: none; min-width: 180px;
        }
        .filter-input:focus { border-color: #0066ff; box-shadow: 0 0 0 3px rgba(0,102,255,0.1); }
        .filter-group { display: inline-block; }
        .filters-bar { display: flex; flex-wrap: wrap; gap: 16px; align-items: flex-end; }
        .btn-filter {
            padding: 8px 18px; border-radius: 10px; border: none; cursor: pointer;
            background: #0066ff; color: #fff; font-size: 12px; font-weight: 700;
        }
        .btn-filter:hover { background: #0052cc; }
        .btn-resolve {
            display: inline-block; padding: 5px 10px; border-radius: 8px; font-size: 11px; font-weight: 700;
            background: #eef2ff; color: #4338ca; border: 1px solid #c7d2fe; cursor: pointer;
        }
        .severity { display: inline-block; padding: 3px 8px; border-radius: 999px; font-size: 11px; font-weight: 700; }
        .severity-low { background: #f0fdf4; color: #15803d; border: 1px solid #bbf7d0; }
        .severity-medium { background: #fffbeb; color: #b45309; border: 1px solid #fde68a; }
        .severity-high { background: #fff7ed; color: #c2410c; border: 1px solid #fed7aa; }
        .severity-critical { background: #fef2f2; color: #b91c1c; border: 1px solid #fecaca; }
        .status-badge { display: inline-block; padding: 3px 8px; border-radius: 999px; font-size: 11px; font-weight: 700; }
        .status-open { background: #fef2f2; color: #b91c1c; border: 1px solid #fecaca; }
        .status-resolved { background: #ecfdf5; color: #047857; border: 1px solid #a7f3d0; }
        .text-muted { color: #94a3b8; font-size: 12px; }
        .data-section { background: #fff; border: 1px solid #e8eef4; border-radius: 16px; padding: 20px; margin-bottom: 20px; }
        .data-header { display: flex; align-items: center; justify-content: space-between; margin-bottom: 16px; }
        .data-title { font-size: 14px; font-weight: 800; color: #0f172a; }
    </style>
@endsection
